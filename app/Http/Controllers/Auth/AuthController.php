<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function customerAccess(): RedirectResponse
    {
        try {
            $customer = User::where('role', 'customer')->first();
            if ($customer) {
                Auth::login($customer);
            }
        } catch (\Throwable $e) {
            // Jika koneksi database belum siap, tetap izinkan akses sebagai tamu
        }

        return redirect()->route('storefront.index')
            ->with('customer_notice', 'Selamat datang! Anda mengakses toko sebagai Customer (Tanpa Login).');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau kata sandi tidak cocok.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        if (Auth::user()->role === 'customer') {
            return redirect()->route('storefront.index')
                ->with('customer_notice', 'Selamat datang! Anda login sebagai Customer.');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
