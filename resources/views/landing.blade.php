<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ujuzi Shop Mall — Inventory Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>
<body class="landing-body">

    <nav class="landing-nav">
        <a href="{{ route('landing') }}" class="landing-brand">Ujuzi<span>.</span></a>
        <div class="landing-nav-links">
            <a href="{{ route('login') }}">Login</a>
            <a href="{{ route('register') }}" class="btn-outline">Get Started</a>
        </div>
    </nav>

    <section class="hero">
        <div class="barcode">
            <span style="height:28px;"></span>
            <span style="height:18px;"></span>
            <span style="height:24px;"></span>
            <span style="height:14px;"></span>
            <span style="height:28px;"></span>
            <span style="height:20px;"></span>
            <span style="height:26px;"></span>
        </div>
        <h1>Ujuzi Shop Mall Inventory</h1>
        <p>
            A digital home for every product Ujuzi Shop Mall carries — real-time stock levels,
            simple stock-in and stock-out tracking, and one clear view of what's on the shelves,
            replacing the ledgers and guesswork of manual stock-taking.
        </p>
        <div class="hero-ctas">
            <a href="{{ route('register') }}" class="btn-primary">Create an Account</a>
            <a href="{{ route('login') }}" class="btn-secondary">Login</a>
        </div>
    </section>

    <section class="landing-section">
        <div class="landing-image-frame">
            <img src="{{ asset('images/mall-floor.jpg') }}" alt="Shop floor of Ujuzi Shop Mall" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
    </section>

    <section class="landing-section">
        <h2>About Ujuzi Shop Mall</h2>
        <p class="lead-muted">
            Ujuzi Shop Mall serves its community with a wide range of everyday products, from
            household essentials to specialty goods. As the mall has grown, so has the challenge
            of tracking stock across departments — this system replaces manual paper records with
            a single, always up-to-date digital inventory.
        </p>
    </section>

    <section class="landing-section">
        <h2>What the System Does</h2>
        <p class="lead-muted">Built around three essentials: accurate records, simple stock control, and secure access.</p>
        <div class="feature-grid">
            <div class="feature-card">
                <div class="icon-mono">01 / RECORDS</div>
                <h3>Digitized Product Records</h3>
                <p>Every product — name, SKU, category, price, and quantity — stored in one searchable place instead of scattered notebooks.</p>
            </div>
            <div class="feature-card">
                <div class="icon-mono">02 / STOCK</div>
                <h3>Stock-In &amp; Stock-Out</h3>
                <p>Log every delivery and every sale-driven deduction, with a running history of stock movement per product.</p>
            </div>
            <div class="feature-card">
                <div class="icon-mono">03 / ACCESS</div>
                <h3>Secure Staff Login</h3>
                <p>Only registered staff can add products or adjust stock — every change is tied to the account that made it.</p>
            </div>
        </div>
    </section>

    <section class="landing-section" style="border-bottom:none;">
        <div class="feature-grid" style="grid-template-columns: 1fr 1fr;">
            <div class="landing-image-frame"><img src="{{ asset('images/staff.jpg') }}" alt="Staff at the counter" style="width: 100%; height: 100%; object-fit: cover;"></div>
            <div class="landing-image-frame"><img src="{{ asset('images/products.jpg') }}" alt="Products on display" style="width: 100%; height: 100%; object-fit: cover;"></div>
        </div>
    </section>

    <footer class="landing-footer">
        &copy; {{ date('Y') }} UJUZI SHOP MALL — INVENTORY MANAGEMENT SYSTEM
    </footer>

</body>
</html>
