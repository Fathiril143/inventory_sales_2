<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Inventory Sales</title>
</head>
<body>
<main class="login-card">
    <div class="login-header">
        <h1>Inventory &amp; Sales</h1>
        <p>Akses akun Staff/Admin untuk pengelolaan internal, atau belanja langsung sebagai Customer tanpa login.</p>
    </div>

    <!-- Akses Customer Tanpa Login -->
    <div class="customer-access-box">
        <div class="customer-access-info">
            <span class="customer-access-badge">Akses Pelanggan</span>
            <strong>Masuk sebagai Customer (Tanpa Login)</strong>
            <p>Lihat stok produk dan buat pesanan langsung tanpa akun atau kata sandi.</p>
        </div>
        <a href="{{ route('customer.access') }}" class="btn-customer-direct" id="customer-direct-btn">
            Mulai Belanja sebagai Customer <span>→</span>
        </a>
    </div>

    <div class="auth-divider">
        <span>ATAU LOGIN ADMIN &amp; STAFF</span>
    </div>

    @if ($errors->any())
        <div class="error" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}">
        @csrf

        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="admin@inventory.test">

        <label for="password">Kata sandi</label>
        <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="••••••••">

        <label class="remember"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
        <button type="submit">Masuk ke Dashboard</button>
    </form>

    <div class="demo-helpers">
        <span class="demo-helpers-title">Pintasan Akun Demo:</span>
        <div class="demo-buttons">
            <button type="button" class="demo-pill" onclick="fillDemo('admin@inventory.test', 'admin12345')">👤 Admin</button>
            <button type="button" class="demo-pill" onclick="fillDemo('staff@inventory.test', 'staff12345')">💼 Staff</button>
            <a href="{{ route('customer.access') }}" class="demo-pill demo-pill-customer">🛒 Customer (Tanpa Login)</a>
        </div>
    </div>
</main>

<script>
    function fillDemo(email, password) {
        var emailInput = document.getElementById('email');
        var passInput = document.getElementById('password');
        if (emailInput && passInput) {
            emailInput.value = email;
            passInput.value = password;
            emailInput.focus();
        }
    }
</script>
</body>
</html>
