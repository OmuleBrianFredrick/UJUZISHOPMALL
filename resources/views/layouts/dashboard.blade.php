<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ujuzi Shop Mall — Inventory')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>
<body class="app-body">

    <header class="app-topbar">
        <a href="{{ route('landing') }}" class="app-brand">Ujuzi<span>Shop Mall</span></a>
        <nav class="app-nav">
            @auth
                <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.index') ? 'active' : '' }}">Products</a>
                <a href="{{ route('products.create') }}" class="{{ request()->routeIs('products.create') ? 'active' : '' }}">Add Product</a>
                <form method="POST" action="{{ route('logout') }}" class="inline-form">
                    @csrf
                    <button type="submit" class="link-btn">Logout ({{ Auth::user()->name }})</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'active' : '' }}">Login</a>
                <a href="{{ route('register') }}" class="btn-chip">Register</a>
            @endauth
        </nav>
    </header>

    <main class="app-main">
        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="app-footer">
        &copy; {{ date('Y') }} Ujuzi Shop Mall — Inventory Management System
    </footer>

</body>
</html>
