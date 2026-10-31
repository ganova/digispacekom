<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Akun CMS (admin penuh & staf) dan profil akun yang sedang login. */
class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', ['users' => User::orderBy('name')->get()]);
    }

    public function create(): View
    {
        return view('admin.users.form', ['user' => new User(['role' => 'staf'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'password' => ['required', 'string', 'min:10', 'confirmed'],
        ], [], ['name' => 'nama', 'password' => 'password', 'role' => 'peran']);

        User::create($data);

        return redirect()->route('admin.users.index')->with('ok', 'Pengguna ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', ['user' => $user]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'password' => ['nullable', 'string', 'min:10', 'confirmed'],
        ], [], ['name' => 'nama', 'password' => 'password', 'role' => 'peran']);

        // peran tidak boleh diturunkan bila itu satu-satunya admin penuh
        if ($user->isAdmin() && $data['role'] !== 'admin' && $this->lastAdmin($user)) {
            return back()->withInput()->withErrors(['role' => 'Minimal harus ada satu admin penuh.']);
        }

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('ok', 'Pengguna diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is(Auth::user())) {
            return back()->withErrors(['users' => 'Anda tidak bisa menghapus akun yang sedang dipakai.']);
        }

        if ($user->isAdmin() && $this->lastAdmin($user)) {
            return back()->withErrors(['users' => 'Minimal harus ada satu admin penuh.']);
        }

        $user->delete();

        return back()->with('ok', 'Pengguna dihapus.');
    }

    public function account(): View
    {
        return view('admin.users.account', ['user' => Auth::user()]);
    }

    public function updateAccount(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'current_password' => ['required_with:password', 'nullable', 'string'],
            'password' => ['nullable', 'string', 'min:10', 'confirmed'],
        ], [
            'current_password.required_with' => 'Isi password saat ini untuk mengganti password.',
        ], ['name' => 'nama', 'current_password' => 'password saat ini', 'password' => 'password baru']);

        if (filled($data['password'] ?? null) && ! Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->fill(['name' => $data['name'], 'email' => $data['email']]);

        if (filled($data['password'] ?? null)) {
            $user->password = $data['password'];
        }

        $user->save();

        return back()->with('ok', 'Akun diperbarui.');
    }

    private function lastAdmin(User $user): bool
    {
        return User::where('role', 'admin')->where('id', '!=', $user->id)->doesntExist();
    }
}
