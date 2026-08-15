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
- Verified-purchase product reviews
- 1–5 star product ratings
- Review moderation with pending/approved/rejected states
- Admin review moderation screen
- Product average-rating helpers
- Fixed and percentage promotions
- Promotion validity rules: active window, minimum order and usage limit
- Admin promotion creation and activation/pause interface
- Purchase-based loyalty ledger
- Idempotent loyalty points awarded when an order is delivered
- Customer loyalty balance/history
- Checkout promotion-code and loyalty-redemption controls
- Dedicated Phase 12 migration for wishlist, reviews, promotions and loyalty transaction tables

### Customer notifications & delivery — Phase 11
- Order confirmation, payment and fulfilment notifications
- Queued mail delivery and notification audit logging
- Delivery-specific customer messages
- Customer delivery timeline: confirmed → processing → ready → shipped → delivered
- Sequential seller delivery transitions

### Shopping cart / Orders / Payments
- Session cart with stock-aware quantity operations
- Persistent orders and order items
- Transaction-safe inventory deduction
- MTN Mobile Money and Airtel Money architecture
- Provider-neutral payment callbacks and settlement
- Payment lifecycle and protected customer payment routes

### Marketplace & seller finance
- Seller application, approval and rejection workflow
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
| 13 | Production hardening, testing & deployment | 🔵 Next |

## 🔐 Authentication rule
Customers authenticate normally. Privileged inventory managers and administrators use:

**Email + Password → Password Verified → Email OTP → Authenticated Staff Session**

## ⭐ Reviews
Reviews are linked to a delivered order containing the reviewed product. Customers submit 1–5 star feedback; submissions enter moderation before appearing publicly.

## ❤️ Wishlist
Wishlist entries are uniquely constrained by customer + product. Customers can remove saved products or add available products directly to the cart.

## 🎟️ Promotions
Promotions support percentage/fixed discounts, minimum order amounts, start/end dates, usage limits and activation state. Discount calculations are recomputed server-side.

## 🏆 Loyalty
Delivered orders can award points through a transaction ledger. The base earning rule is **1 point per UGX 1,000 of delivered-order value**. Awarding is idempotent, and checkout redemption is validated transactionally.

## 🗄️ Phase 12 database schema
The repository now includes a defensive Phase 12 migration creating these tables when absent:

- `wishlists` — customer/product unique pair with foreign keys.
- `reviews` — customer/product review, rating, moderation status and verified-purchase flag.
- `promotions` — discount code, type, value, validity window, usage controls and active state.
- `loyalty_transactions` — immutable-ish points ledger linked to customers and optionally orders.

This migration is deliberately guarded with `Schema::hasTable()` because the project previously had portions of the commerce schema created locally and pushed directly to SQL. When synchronizing a local environment, run the normal Laravel migration process and reconcile any already-existing tables rather than blindly recreating them.

## 🧪 CI
GitHub Actions now runs Laravel migrations and the automated test suite on pushes and pull requests targeting `main` using PHP 8.2 and SQLite.

## 🛠️ Development

```bash
composer install
php artisan migrate
php artisan storage:link
php artisan serve
```

For queued email notifications, production must run a queue worker appropriate to the configured queue driver.

Never commit production credentials, API keys, signing secrets or payment-provider tokens.

## 📝 Upgrade log

### Phase 12 — Reviews, Wishlist, Promotions & Loyalty — COMPLETE
- Implemented wishlist storefront experience and database protection.
- Implemented verified-purchase review workflow and moderation.
- Implemented product ratings and approved-review presentation.
- Implemented admin promotion creation, activation and pausing.
- Implemented promotion validation and server-side discount rules.
- Implemented loyalty transaction ledger, delivered-order awarding and checkout redemption.
- Implemented customer checkout identity, promotion and loyalty controls.
- Added reconciled Phase 12 database migration for missing/local-first tables.
- Added regression coverage for promotion business rules.
- Added GitHub Actions CI for migrations and automated tests.
- Updated documentation and roadmap to reflect Phase 12 completion.

### Phase 11 — Customer Notifications & Delivery Management — COMPLETE
- Added transactional customer notifications, queue/audit handling and delivery timeline.

### Phase 10A — Staff Email OTP Security — COMPLETE
- Restricted mandatory OTP to privileged staff while keeping customers outside the OTP flow.

### Phase 10 — Admin Commerce Command Centre — COMPLETE
- Added centralized commerce KPIs and operational reporting.

### Phases 1–9 — COMPLETE
- Storefront, checkout, payments, marketplace, seller finance, payment adapters and payouts implemented.

## 🔒 Security principles
- Keep secrets in `.env` and outside version control.
- Validate all customer input server-side.
- Verify payment callbacks before marking orders paid.
- Make payment and financial settlement idempotent.
- Enforce seller ownership on seller operations.
- Never trust client-side discount calculations.
- Only permit verified purchasers to review delivered products.
- Keep financial and loyalty ledgers auditable.

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
