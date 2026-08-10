<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ujuzi Shop Mall — Inventory')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>
<body class="app-body">

@auth
    <div class="app-shell">
        @include('layouts.sidebar')

        <div class="app-content">
            <header class="app-topbar-inner">
                <div class="topbar-title">@yield('title', 'Ujuzi Shop Mall')</div>
                <div class="topbar-right">
                    <select id="currencySwitcher" class="currency-select">
                        <option value="UGX" selected>UGX (USh)</option>
                        <option value="USD">USD ($)</option>
                    </select>
                    <span class="signed-in-text">Signed in as <strong>{{ Auth::user()->name }}</strong></span>
                    <form method="POST" action="{{ route('logout') }}" class="inline-form">
                        @csrf
                        <button type="submit" class="btn-outline-dark btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
                    </form>
                </div>
            </header>

            <main class="app-main-inner">
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

            <footer class="app-footer-edge">
                &copy; {{ date('Y') }} Ujuzi Shop Mall — Inventory Management System
            </footer>
        </div>
    </div>
@else
    <header class="app-topbar">
        <a href="{{ route('landing') }}" class="app-brand">Ujuzi<span>Shop Mall</span></a>
        <nav class="app-nav">
            <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'active' : '' }}">Login</a>
            <a href="{{ route('register') }}" class="btn-chip">Register</a>
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
@endauth

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
    const EXCHANGE_RATE_UGX_PER_USD = 3800;

    function formatCurrency(amountUgx, currency) {
        if (currency === 'USD') {
            return '$' + (amountUgx / EXCHANGE_RATE_UGX_PER_USD).toFixed(2);
        }
        return 'UGX ' + Number(amountUgx).toLocaleString();
    }

    const currencySwitcher = document.getElementById('currencySwitcher');
    if (currencySwitcher) {
        currencySwitcher.addEventListener('change', function () {
            const currency = this.value;
            document.querySelectorAll('.price-value').forEach(function (el) {
                const raw = parseFloat(el.dataset.ugx);
                el.textContent = formatCurrency(raw, currency);
            });
        });
    }
</script>
@stack('scripts')

</body>
</html>