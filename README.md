# Ujuzi Shop Mall

Ujuzi Shop Mall is a Laravel-based commerce platform being developed from an inventory-management foundation into a full online marketplace for customers, sellers and administrators.

## 🚀 Current platform capabilities

### Storefront
- Product catalogue, search, categories and sorting
- Product detail and related-product pages
- Responsive shopping experience

### Shopping cart
- Session-based cart
- Add/remove products and quantity updates
- Automatic totals and stock-aware operations

### Orders & checkout — Phase 2
- Customer checkout and delivery details
- Persistent orders and order items
- Unique order numbers
- Customer order history and order details
- Transaction-safe stock validation and inventory deduction
- Stock-out movement records linked to orders

### Payments — Phase 3 foundation
- Persistent payment transaction records
- Order-to-payment relationship
- UGX currency support
- Merchant and provider references
- Payment status lifecycle foundation: pending, processing, successful, failed
- MTN Mobile Money and Airtel Money method selection
- Provider-agnostic `PaymentGateway` contract for future API adapters
- Payment status screen and protected customer payment routes
- Duplicate pending-payment protection per order

### Existing inventory foundation
- Product management
- SKU/category/description/price management
- Stock-in and stock-out workflows
- Reorder-level monitoring
- Stock movement history
- Inventory analytics
- User management
- Authentication, OTP and Google authentication support

## 🧭 Product roadmap

| Phase | Area | Status |
|---|---|---|
| 1 | Storefront + shopping cart | ✅ Implemented |
| 2 | Checkout + orders + stock deduction | ✅ Implemented |
| 3 | Payment architecture + mobile-money foundation | 🟡 In progress |
| 4 | Live MTN/Airtel provider adapters + callbacks | 🔜 Next |
| 5 | Seller / multi-vendor marketplace | Planned |
| 6 | Admin commerce dashboard | Planned |
| 7 | Notifications & delivery management | Planned |
| 8 | Reviews, wishlist, promotions & loyalty | Planned |
| 9 | Production hardening, testing & deployment | Planned |

## 💳 Payment architecture

Phase 3 establishes the payment domain without hard-coding a specific provider API into checkout. Each payment belongs to an order and stores its provider, method, status, merchant reference, provider reference, amount, currency, payer phone and normalized provider response.

The current customer flow is:

**Order → Choose MTN/Airtel → Create payment transaction → Processing status → Provider adapter/callback → Verified payment → Paid order**

The live provider API adapters and signed/verified callbacks are intentionally the next milestone. Production credentials must remain in environment configuration and must never be committed to the repository.

## 🏗️ Architecture direction

**Customer storefront → Cart → Checkout → Order → Payment → Fulfilment**

Management side:

**Inventory → Orders → Payments → Sellers → Analytics**

The existing inventory system is retained as the back office rather than discarded.

## 🛠️ Development notes

Run migrations after pulling the latest changes:

```bash
composer install
php artisan migrate
php artisan storage:link
php artisan serve
```

Use the project's configured `.env` database and application settings. Never commit production credentials, API keys, signing secrets or payment-provider tokens.

## 📝 Upgrade log

### Phase 3 — Payment Architecture & Mobile-Money Foundation
**Implemented:**
- Added `payments` database table and transaction model.
- Added order-to-payment relationship.
- Added protected payment initiation/status routes.
- Added MTN Mobile Money and Airtel Money method selection.
- Added merchant references and provider-reference fields.
- Added UGX transaction support.
- Added payment lifecycle state fields.
- Added duplicate pending/processing payment protection.
- Added provider-agnostic `PaymentGateway` contract.
- Added payment initiation and status UI.

**Important implementation detail:** this milestone creates the payment domain and UX but does **not** pretend that a real mobile-money API call has happened. The actual MTN/Airtel API adapters, credentials, signed callbacks and server-side verification belong to the next payment milestone.

### Phase 2 — Checkout & Orders
- Added `Order` and `OrderItem` models and migrations.
- Added transactional checkout processing.
- Added stock locking and deduction during order creation.
- Added customer order history/detail pages.
- Connected cart to checkout.

### Phase 1 — Storefront & Cart
- Added customer product catalogue.
- Added product search, category filtering and sorting.
- Added product details and related products.
- Added session shopping cart and responsive storefront styling.

## 🔐 Security principles

- Keep secrets in `.env` and outside version control.
- Validate all customer input server-side.
- Use authenticated routes for customer commerce operations.
- Verify payment callbacks server-side before marking orders paid.
- Make callback processing idempotent to prevent duplicate payment effects.
- Review authorization rules before exposing administrative order/payment operations.

## 📌 Development principle

Every significant platform upgrade should include:
1. A focused implementation.
2. A clear Git commit message.
3. A GitHub development/comment log.
4. An update to this README.
5. A review before the next major module begins.

## License

This project is proprietary unless otherwise stated by its owner. The Laravel framework and its dependencies remain subject to their respective licenses.
