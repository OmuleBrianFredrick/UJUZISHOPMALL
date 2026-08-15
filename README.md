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
| 13B | Automated regression/CI hardening | 🟢 Implemented |
| 13C | Final repository audit & production release gate | 🟡 In progress |

## 🔐 Authentication rule
Customers authenticate normally. Privileged inventory managers and administrators use:

**Email + Password → Password Verified → Email OTP → Authenticated Staff Session**

## 🗄️ Database schema
Phase 12 includes a defensive migration for `wishlists`, `reviews`, `promotions` and `loyalty_transactions`. It is guarded with `Schema::hasTable()` because portions of the commerce database were previously created locally/SQL-first. When synchronizing an existing local database, inspect the current schema before running migrations.

## 🏥 Health & readiness
The application exposes a lightweight public health endpoint:

```text
GET /health
```

It verifies database connectivity and returns HTTP 200 when healthy or HTTP 503 when the database check fails.

The production readiness command checks critical environment and schema prerequisites:

```bash
php artisan app:production-readiness
```

A deployment should not be considered ready until migrations, the readiness command, automated tests and `/health` all pass.

## 🧪 CI & production readiness
GitHub Actions uses **PHP 8.3**, matching the repository's `composer.json` requirement and Laravel 13 runtime. It runs dependency validation, migrations and the automated test suite on pushes and pull requests targeting `main`.

Production logging defaults to a rotating daily log channel with configurable retention. Set `LOG_LEVEL`, `LOG_DAILY_DAYS`, `LOG_CHANNEL` and `LOG_STACK` through environment configuration rather than committing environment-specific secrets.

For production, use `APP_DEBUG=false`, HTTPS, secure HTTP-only cookies, a persistent queue worker and a real cache/session backend appropriate to the deployment scale.

## 🚀 Production operations
The repository contains:

- `docs/PRODUCTION_RUNBOOK.md` — deployment, recovery and operational procedure.
- `docs/PRODUCTION_CHECKLIST.md` — release gate checklist covering environment, database, application, workers, observability and recovery.

Recommended release sequence:

**Backup → migrate → readiness check → tests → health check → workers → smoke test → monitor.**

## 📝 Upgrade log

### Phase 13 — Final Repository Audit & Production Release Gate — IN PROGRESS
- Audited the repository's declared PHP/Laravel runtime against CI.
- Found and corrected a PHP runtime mismatch: `composer.json` requires PHP `^8.3`, while CI had been configured for PHP 8.2.
- Added a final production release checklist.
- Reconciled the README with the actual CI/runtime requirement.
- CI execution remains the final external verification gate; GitHub Actions currently fails before recording workflow steps.

### Phase 13C — Production Operations
- Added public database-backed `/health` endpoint.
- Added health endpoint regression coverage.
- Added production deployment/recovery runbook.
- Hardened production logging defaults with daily rotation and configurable retention.

### Phase 13B — Automated Regression & Security Hardening — COMPLETE
- Added staff OTP/customer authentication regression coverage.
- Added admin authorization regression coverage.
- Added order ownership tests.
- Added payment ownership test.
- Added seller authorization tests.
- Added checkout failure-path tests for stock, loyalty and promotions.
- Added Phase 12 schema smoke coverage.
- Added GitHub Actions migration/test execution.

### Phase 13A — Production Configuration & Readiness Tooling — COMPLETE
- Added production-readiness Artisan command.
- Added schema prerequisite checks.
- Added production application-key and debug checks.

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
- Never log passwords, OTP values, API secrets or payment credentials.
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
