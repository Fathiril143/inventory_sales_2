<?php

namespace App\Http\Controllers;

use App\Models\PaymentChannel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentChannelController extends Controller
{
    public function index(): View
    {
        $channels = PaymentChannel::orderBy('sort_order')->get();

        return view('admin.payment_channels.index', compact('channels'));
    }

    public function toggle(PaymentChannel $channel): RedirectResponse
    {
        $channel->update([
            'is_active' => ! $channel->is_active,
        ]);

        $statusText = $channel->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('payment-channels.index')
            ->with('success', "Metode pembayaran {$channel->name} berhasil {$statusText}.");
    }

    public function edit(PaymentChannel $channel): View
    {
        return view('admin.payment_channels.edit', compact('channel'));
    }

    public function update(Request $request, PaymentChannel $channel): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'account_number' => ['nullable', 'string', 'max:60'],
            'account_name' => ['nullable', 'string', 'max:100'],
            'admin_fee' => ['required', 'numeric', 'min:0'],
            'color' => ['nullable', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $channel->update($validated);

        return redirect()->route('payment-channels.index')
            ->with('success', "Pengaturan metode pembayaran {$channel->name} berhasil diperbarui.");
    }
}
