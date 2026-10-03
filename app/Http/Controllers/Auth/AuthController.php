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

        $credentials = $request->only('email', 'password');
        $remember = (bool) $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();
            $role = $user->role?->name ?? 'candidate';

            return match ($role) {
                'admin', 'manager', 'staff' => redirect()->intended(route('dashboard')),
                default => redirect()->intended(route('portal.dashboard')),
            };
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
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
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id ?? 4,
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
