<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::orderBy('name')->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,staff,customer'],
        ]);

        User::create($validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'Pengguna berhasil dibuat.');
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,staff,customer'],
        ]);

        if ($request->user()->is($user) && $validated['role'] !== 'admin') {
            return back()->withErrors(['role' => 'Kamu tidak bisa mengubah role akun yang sedang dipakai.'])->withInput();
        }

        if ($user->role === 'admin' && $validated['role'] !== 'admin'
            && User::where('role', 'admin')->count() <= 1) {
            return back()->withErrors(['role' => 'Aplikasi harus memiliki setidaknya satu Admin.'])->withInput();
        }

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return redirect()->route('users.index')->withErrors(['user' => 'Kamu tidak bisa menghapus akun yang sedang dipakai.']);
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return redirect()->route('users.index')->withErrors(['user' => 'Aplikasi harus memiliki setidaknya satu Admin.']);
        }

        if ($user->purchases()->exists() || $user->sales()->exists()) {
            return redirect()->route('users.index')->withErrors(['user' => 'Pengguna dengan riwayat transaksi tidak bisa dihapus.']);
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
