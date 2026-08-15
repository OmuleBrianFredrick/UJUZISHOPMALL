# Ujuzi Shop Mall

Ujuzi Shop Mall is a Laravel 13 commerce platform being developed from an inventory-management foundation into a full online marketplace for customers, sellers and administrators.

## 🚀 Current platform capabilities

### Customer storefront
- Product catalogue, search, categories and sorting
- Product detail and related-product pages
- Responsive shopping experience
- Customer accounts for order tracking and purchase history
- Checkout requires customer name, email, phone number and delivery address
- Customer accounts are **not forced through staff OTP**

### Customer engagement — Phase 12 — COMPLETE
- Customer wishlist with duplicate protection and storefront add/remove/cart actions
- Verified-purchase product reviews and moderation
- 1–5 star product ratings
- Fixed and percentage promotions with validity/usage controls
- Admin promotion creation and activation/pause interface
- Purchase-based loyalty ledger and idempotent delivered-order awards
- Customer loyalty balance/history and checkout redemption

### Customer notifications & delivery — Phase 11 — COMPLETE
- Order, payment and fulfilment notifications
- Queued mail delivery and notification audit logging
- Delivery timeline: confirmed → processing → ready → shipped → delivered
- Sequential seller delivery transitions

### Shopping cart / Orders / Payments
- Session cart with stock-aware quantity operations
- Persistent orders and order items
- Transaction-safe inventory deduction
- MTN Mobile Money and Airtel Money architecture
- Provider-neutral payment callbacks and settlement

### Marketplace & seller finance
- Seller application, approval/rejection workflow
- Seller-owned product/order management
- Seller financial ledger and commission settlement
- Seller payout requests and admin payout workflow

### Admin
- Commerce Command Centre
- Sales/payment/inventory/seller reporting
- Review moderation
- Promotion management
- Seller approval
- Payout administration

### Staff security
- Mandatory email OTP for `admin` and `inventory_manager` roles after successful password verification
- OTP hashed at rest, five-minute expiry, single use and request throttling
- Customers remain outside the staff OTP flow

## 🧭 Product roadmap

| Phase | Area | Status |
|---|---|---|
| 1–9 | Storefront, checkout, payments, marketplace, seller finance and payouts | 🟢 Implemented |
| 10 | Admin commerce dashboard | 🟢 Implemented |
| 10A | Staff email OTP security | 🟢 Implemented |
| 11 | Customer notifications + delivery management | 🟢 Implemented |
| 12 | Reviews, wishlist, promotions & loyalty | 🟢 **COMPLETE** |
| 13A | Production configuration & readiness tooling | 🟢 Implemented |
| 13B | Automated regression/CI hardening | 🟡 In progress |
| 13C | Deployment, backups, monitoring & release runbook | 🔵 Next |

## 🔐 Authentication rule
Customers authenticate normally. Privileged inventory managers and administrators use:

**Email + Password → Password Verified → Email OTP → Authenticated Staff Session**

## 🗄️ Database schema
Phase 12 includes a defensive migration for `wishlists`, `reviews`, `promotions` and `loyalty_transactions`. It is guarded with `Schema::hasTable()` because portions of the commerce database were previously created locally/SQL-first. When synchronizing an existing local database, inspect the current schema before running migrations.

## 🧪 CI & production readiness
GitHub Actions runs migrations and the automated test suite on pushes and pull requests targeting `main` using PHP 8.2 and SQLite.

A production readiness command is available:

```bash
php artisan app:production-readiness
```

It checks the application key, debug configuration and critical session/cache/Phase 12 tables. Production deployments should run this check after environment configuration and migrations.

For production, use `APP_DEBUG=false`, HTTPS, secure HTTP-only cookies, a persistent queue worker and a real cache/session backend appropriate to the deployment scale.

## 📝 Upgrade log

### Phase 13A — Production Configuration & Readiness Tooling — IMPLEMENTED
- Added a production-readiness Artisan command.
- Added schema prerequisite checks for Phase 12 commerce tables.
- Added production configuration checks for application key and debug mode.
- Added a Phase 13 database smoke test.
- Added CI migration/test execution for deployment confidence.

### Phase 12 — Reviews, Wishlist, Promotions & Loyalty — COMPLETE
- Implemented wishlist, verified-purchase reviews, ratings and moderation.
- Implemented promotions and admin campaign management.
- Implemented loyalty ledger, delivered-order awarding and checkout redemption.
- Added reconciled Phase 12 database migration and regression tests.

### Phase 11 — Customer Notifications & Delivery Management — COMPLETE
- Added transactional customer notifications, queue/audit handling and delivery timeline.

### Phase 10A — Staff Email OTP Security — COMPLETE
- Restricted mandatory OTP to privileged staff while keeping customers outside the OTP flow.

### Phases 1–10 — COMPLETE
- Storefront, checkout, payments, marketplace, seller finance, payouts and admin commerce implemented.

## 🔒 Security principles
- Keep secrets in `.env` and outside version control.
- Use `APP_DEBUG=false` in production.
- Enforce HTTPS and secure HTTP-only session cookies in production.
- Validate all customer input server-side.
- Verify payment callbacks before marking orders paid.
- Make payment and financial settlement idempotent.
- Enforce seller ownership on seller operations.
- Never trust client-side discount calculations.
- Only permit verified purchasers to review delivered products.
- Keep financial and loyalty ledgers auditable.
- Back up the production database and test restoration before launch.

## 📌 Development principle
Every significant platform upgrade includes:
1. Focused implementation.
2. A descriptive Git commit.
3. README/upgrade-log documentation.
4. Repository reconciliation.
5. Tests or CI coverage where applicable.
6. Resolution of failed or partial work before the next phase.

## License
This project is proprietary unless otherwise stated by its owner. Laravel and dependencies remain subject to their respective licenses.
