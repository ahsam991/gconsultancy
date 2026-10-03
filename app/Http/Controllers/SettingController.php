<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('key')->get();
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable|string',
        ]);
        foreach ($data['settings'] as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        AuditService::log('settings.updated', null, null, $data);
        return redirect()->route('settings.index')->with('status', 'Settings updated.');
    }

    public function usersIndex()
    {
        $users = User::with(['role', 'team'])->orderBy('name')->paginate(15);
        return view('users.index', compact('users'));
    }

    public function userCreate()
    {
        $roles = Role::orderBy('name')->get();
        $teams = \App\Models\Team::active()->orderBy('name')->get();
        return view('users.create', compact('roles', 'teams'));
    }

    public function userStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'team_id' => 'nullable|exists:teams,id',
            'phone' => 'nullable|string|max:50',
        ]);
        $user = User::create($data);
        AuditService::log('user.created', null, null, ['id' => $user->id, 'email' => $user->email]);
        return redirect()->route('users.show', $user)->with('status', 'User created.');
    }

    public function userShow(User $user)
    {
        $user->load(['role', 'team']);
        return view('users.show', compact('user'));
    }

    public function userEdit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        $teams = \App\Models\Team::active()->orderBy('name')->get();
        return view('users.edit', compact('user', 'roles', 'teams'));
    }

    public function userUpdate(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role_id' => 'required|exists:roles,id',
            'team_id' => 'nullable|exists:teams,id',
            'phone' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $user->update($data);
        return redirect()->route('users.show', $user)->with('status', 'User updated.');
    }

    public function userDestroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('status', 'User deleted.');
    }

    public function auditIndex(Request $request)
    {
        $logs = AuditLog::with('user')->orderByDesc('created_at')->paginate(15);
        return view('audit.index', compact('logs'));
    }

    public function sessionsIndex()
    {
        $sessions = \DB::table('sessions')->orderByDesc('last_activity')->paginate(15);
        $users = \App\Models\User::whereIn('id', $sessions->pluck('user_id')->filter()->unique())->get()->keyBy('id');
        return view('settings.sessions', compact('sessions', 'users'));
    }

    public function sessionTerminate($id)
    {
        \DB::table('sessions')->where('id', $id)->delete();
        \App\Services\AuditService::log('auth.session_terminated', null, null, ['session' => $id]);
        return redirect()->back()->with('status', 'Session terminated (user will be logged out).');
    }

    public function loginHistory()
    {
        $history = \App\Models\LoginHistory::with('user')->orderByDesc('created_at')->paginate(20);
        return view('settings.login-history', compact('history'));
    }

    public function notificationsIndex(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(15);
        return view('notifications.index', compact('notifications'));
    }

    public function notificationRead(Request $request, string $notification)
    {
        $n = $request->user()->notifications()->findOrFail($notification);
        $n->markAsRead();
        return redirect()->back()->with('status', 'Notification marked as read.');
    }

    public function notificationsReadAll(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return redirect()->back()->with('status', 'All notifications marked as read.');
    }
}
