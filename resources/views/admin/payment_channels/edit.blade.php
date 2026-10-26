<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ubah Channel Pembayaran | {{ $channel->name }}</title>
</head>
<body style="font-family: Arial, sans-serif; max-width: 650px; margin: 3rem auto; padding: 0 1rem; color: #172033;">
    <p><a href="{{ route('payment-channels.index') }}">← Kembali ke Daftar Channel Pembayaran</a></p>
    <h1>Ubah Channel: {{ $channel->name }}</h1>
    <p style="color: #64748b;">Kode Metode: <code>{{ $channel->code }}</code> | Kategori: <strong>{{ $channel->category_label }}</strong></p>

    @if ($errors->any())
        <div style="padding: .8rem; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 1rem;">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('payment-channels.update', $channel) }}" style="display: flex; flex-direction: column; gap: 14px;">
        @csrf
        @method('PUT')

        <label>Nama Tampilan Metode Pembayaran<br>
            <input type="text" name="name" value="{{ old('name', $channel->name) }}" required style="width: 100%; padding: .7rem;">
        </label>

        <label>Nomor Rekening / Prefix Virtual Account<br>
            <input type="text" name="account_number" value="{{ old('account_number', $channel->account_number) }}" style="width: 100%; padding: .7rem;">
        </label>

        <label>Nama Pemilik Rekening / Merchant<br>
            <input type="text" name="account_name" value="{{ old('account_name', $channel->account_name) }}" style="width: 100%; padding: .7rem;">
        </label>

        <label>Biaya Admin Transaksi (Rp)<br>
            <input type="number" step="100" min="0" name="admin_fee" value="{{ old('admin_fee', (int)$channel->admin_fee) }}" required style="width: 100%; padding: .7rem;">
        </label>

        <label>Warna Aksen Brand (Hex Code)<br>
            <input type="text" name="color" value="{{ old('color', $channel->color) }}" placeholder="#176b78" style="width: 100%; padding: .7rem;">
        </label>

        <label style="display: flex; align-items: center; gap: 8px; margin-top: 6px; cursor: pointer;">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $channel->is_active))>
            <strong>Aktifkan metode pembayaran ini di toko online dan POS</strong>
        </label>

        <div style="margin-top: 1rem;">
            <button type="submit" style="padding: .8rem 1.6rem; background: #176b78; color: #fff; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;">
                Simpan Perubahan
            </button>
            <a href="{{ route('payment-channels.index') }}" style="margin-left: 10px; color: #64748b; text-decoration: none;">Batal</a>
        </div>
    </form>
</body>
</html>
