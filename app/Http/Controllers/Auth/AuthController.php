<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user && $user->locked_until && $user->locked_until->isFuture()) {
            $this->recordLogin($request, $user, false);
            return back()->withErrors([
                'email' => 'Account locked after too many failed attempts. Try again after '.$user->locked_until->format('H:i').'.',
            ])->onlyInput('email');
        }

        $credentials = $request->only('email', 'password');
        $remember = (bool) $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            /** @var User $user */
            $user = Auth::user();
            $user->forceFill(['failed_attempts' => 0, 'locked_until' => null, 'last_login_at' => now()])->save();
            $request->session()->regenerate();
            $this->recordLogin($request, $user, true);

            if ($user->two_factor_enabled) {
                Auth::logout();
                $request->session()->put('2fa_user_id', $user->id);
                $request->session()->put('2fa_remember', $remember);
                return redirect()->route('2fa.challenge');
            }

            return $this->roleRedirect($user);
        }

        if ($user) {
            $attempts = $user->failed_attempts + 1;
            $update = ['failed_attempts' => $attempts];
            if ($attempts >= 5) {
                $update['locked_until'] = now()->addMinutes(15);
                \App\Services\AuditService::log('auth.lockout', $user, null, ['email' => $user->email]);
            }
            $user->forceFill($update)->save();
            $this->recordLogin($request, $user, false);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    protected function roleRedirect(User $user)
    {
        $role = $user->role?->name ?? 'candidate';
        return match ($role) {
            'admin', 'manager', 'staff' => redirect()->intended(route('dashboard')),
            default => redirect()->intended(route('portal.dashboard')),
        };
    }

    protected function recordLogin(Request $request, ?User $user, bool $success): void
    {
        try {
            $ua = (string) $request->userAgent();
            \App\Models\LoginHistory::create([
                'user_id' => $user?->id,
                'email' => $request->email,
                'ip' => $request->ip(),
                'user_agent' => substr($ua, 0, 500),
                'device' => str_contains(strtolower($ua), 'mobile') ? 'mobile' : 'desktop',
                'browser' => $this->browserName($ua),
                'successful' => $success,
                'session_id' => $success ? $request->session()->getId() : null,
            ]);
            if ($user) {
                \App\Services\AuditService::log($success ? 'auth.login' : 'auth.failed', $user);
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    protected function browserName(string $ua): string
    {
        foreach (['Chrome' => 'Chrome', 'Firefox' => 'Firefox', 'Safari' => 'Safari', 'Edge' => 'Edg', 'Opera' => 'OPR'] as $name => $needle) {
            if (str_contains($ua, $needle)) return $name;
        }
        return 'Other';
    }

    public function twoFactorChallenge(Request $request)
    {
        if (! $request->session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }
        return view('auth.2fa-challenge');
    }

    public function twoFactorVerify(Request $request)
    {
        $request->validate(['code' => 'required|string|max:20']);
        $user = User::find($request->session()->get('2fa_user_id'));
        if (! $user || ! $user->two_factor_enabled) {
            return redirect()->route('login');
        }
        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $valid = false;
        try {
            $valid = $google2fa->verifyKey(decrypt($user->two_factor_secret), $request->code);
        } catch (\Throwable $e) {
            $valid = false;
        }
        if (! $valid && $user->two_factor_recovery_codes) {
            foreach (json_decode(decrypt($user->two_factor_recovery_codes), true) ?? [] as $i => $hash) {
                if (\Illuminate\Support\Facades\Hash::check($request->code, $hash)) {
                    $codes = json_decode(decrypt($user->two_factor_recovery_codes), true);
                    unset($codes[$i]);
                    $user->forceFill(['two_factor_recovery_codes' => encrypt(json_encode(array_values($codes)))])->save();
                    $valid = true;
                    break;
                }
            }
        }
        if (! $valid) {
            return back()->withErrors(['code' => 'Invalid authentication code.']);
        }
        Auth::login($user, $request->session()->pull('2fa_remember', false));
        $request->session()->forget('2fa_user_id');
        $request->session()->regenerate();
        $this->recordLogin($request, $user, true);
        return $this->roleRedirect($user);
    }

    public function twoFactorSetup(Request $request)
    {
        $user = $request->user();
        if ($user->two_factor_enabled) {
            return redirect()->back()->with('status', 'Two-factor authentication is already enabled.');
        }
        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $secret = $request->session()->get('2fa_temp_secret') ?? tap($google2fa->generateSecretKey(), fn ($s) => $request->session()->put('2fa_temp_secret', $s));
        $qrUrl = $google2fa->getQRCodeUrl(config('app.name', 'Global Consultancy'), $user->email, $secret);
        $qrSvg = (new \BaconQrCode\Writer(new \BaconQrCode\Renderer\ImageRenderer(new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200), new \BaconQrCode\Renderer\Image\SvgImageBackEnd())))->writeString($qrUrl);
        return view('auth.2fa-setup', compact('secret', 'qrSvg'));
    }

    public function twoFactorConfirm(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $secret = $request->session()->get('2fa_temp_secret');
        if (! $secret) {
            return redirect()->route('2fa.setup');
        }
        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        try {
            $valid = $google2fa->verifyKey($secret, $request->code);
        } catch (\Throwable $e) {
            $valid = false;
        }
        if (! $valid) {
            return back()->withErrors(['code' => 'Invalid code. Try again.']);
        }
        $codes = [];
        $plain = [];
        for ($i = 0; $i < 8; $i++) {
            $code = strtoupper(\Illuminate\Support\Str::random(10));
            $plain[] = $code;
            $codes[] = \Illuminate\Support\Facades\Hash::make($code);
        }
        $request->user()->forceFill([
            'two_factor_secret' => encrypt($secret),
            'two_factor_enabled' => true,
            'two_factor_recovery_codes' => encrypt(json_encode($codes)),
        ])->save();
        $request->session()->forget('2fa_temp_secret');
        \App\Services\AuditService::log('auth.2fa_enabled', $request->user());
        return redirect()->back()->with('status', 'Two-factor enabled.')->with('recovery_codes', $plain);
    }

    public function twoFactorDisable(Request $request)
    {
        $request->user()->forceFill([
            'two_factor_secret' => null, 'two_factor_enabled' => false, 'two_factor_recovery_codes' => null,
        ])->save();
        \App\Services\AuditService::log('auth.2fa_disabled', $request->user());
        return redirect()->back()->with('status', 'Two-factor authentication disabled.');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'consent' => 'accepted',
        ]);

        // Public registration is candidate-only; staff accounts are created by admins.
        $candidateRole = \App\Models\Role::where('name', 'candidate')->first();
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $candidateRole?->id,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('login')->with('status', 'Account created successfully!');
    }

    public function forgetPassword(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('auth.forgot-password');
        }

        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        return back()->with('status', $status === Password::RESET_LINK_SENT ? 'We have emailed your password reset link!' : 'Something went wrong.');
    }

    public function postForgetPassword(Request $request)
    {
        return $this->forgetPassword($request);
    }

    public function resetPassword(Request $request, ?string $token = null)
    {
        if ($request->isMethod('get')) {
            return view('auth.reset-password', ['token' => $token, 'email' => $request->get('email')]);
        }

        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Your password has been reset successfully!')
            : back()->withErrors(['email' => __($status)]);
    }

    public function postResetPassword(Request $request)
    {
        return $this->resetPassword($request);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('status', 'Logged out successfully.');
    }
}
