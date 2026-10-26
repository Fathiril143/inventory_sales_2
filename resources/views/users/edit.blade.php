<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ubah Pengguna | Inventory Sales</title>
</head>
<body style="font-family: Arial, sans-serif; max-width: 640px; margin: 3rem auto; padding: 0 1rem; color: #172033;">
    <p><a href="{{ route('users.index') }}">← Kembali ke Pengguna</a></p>
    <h1>Ubah Pengguna</h1>

    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf
        @method('PUT')

        <label for="name" style="display: block; margin: 1rem 0 .4rem;">Nama</label>
        <input id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="150" style="box-sizing: border-box; width: 100%; padding: .7rem;">
        @error('name') <p style="color: #b91c1c;">{{ $message }}</p> @enderror

        <label for="email" style="display: block; margin: 1rem 0 .4rem;">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required maxlength="255" style="box-sizing: border-box; width: 100%; padding: .7rem;">
        @error('email') <p style="color: #b91c1c;">{{ $message }}</p> @enderror

        <label for="role" style="display: block; margin: 1rem 0 .4rem;">Role</label>
        <select id="role" name="role" required style="padding: .7rem;">
            <option value="staff" @selected(old('role', $user->role) === 'staff')>Staff</option>
            <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin / Owner</option>
            <option value="customer" @selected(old('role', $user->role) === 'customer')>Customer / Pelanggan (Tanpa Perlu Login)</option>
        </select>
        @error('role') <p style="color: #b91c1c;">{{ $message }}</p> @enderror

        <label for="password" style="display: block; margin: 1rem 0 .4rem;">Kata sandi baru (opsional)</label>
        <input id="password" name="password" type="password" minlength="8" autocomplete="new-password" style="box-sizing: border-box; width: 100%; padding: .7rem;">
        @error('password') <p style="color: #b91c1c;">{{ $message }}</p> @enderror

        <label for="password_confirmation" style="display: block; margin: 1rem 0 .4rem;">Ulangi kata sandi baru</label>
        <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" style="box-sizing: border-box; width: 100%; padding: .7rem;">

        <button type="submit" style="margin-top: 1rem; padding: .7rem 1rem;">Simpan Perubahan</button>
    </form>
</body>
</html>
