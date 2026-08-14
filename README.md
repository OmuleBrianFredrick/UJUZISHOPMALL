# Ujuzi Shop Mall

Ujuzi Shop Mall is a Laravel 13 commerce platform being developed from an inventory-management foundation into a full online marketplace for customers, sellers and administrators.

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

### Payments — Phase 3/4
- Persistent payment transaction records
- Order-to-payment relationship
- UGX currency support
- Merchant and provider references
- Payment lifecycle: pending, processing, successful, failed
- MTN Mobile Money and Airtel Money method selection
- Provider-agnostic `PaymentGateway` contract
- MTN MoMo collections adapter using environment configuration
- Public MTN callback endpoint with idempotent state handling
- Payment status screen and protected customer payment routes
- Duplicate pending/processing payment protection

### Marketplace — Phase 5 foundation
- Customer-to-seller application flow
- Seller profile/store records
- Pending/approved/rejected seller lifecycle
- Admin seller approval and rejection workflow
- Seller-only dashboard scoped to seller-owned products
- Product ownership via `seller_id`
- Seller attribution retained on order items
- Customer, seller and admin role field foundation

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
| 3 | Payment architecture + mobile-money foundation | ✅ Implemented |
| 4 | MTN MoMo adapter + callback foundation | 🟡 Foundation implemented |
| 5 | Seller / multi-vendor marketplace foundation | 🟡 In progress |
| 6 | Seller product management + seller order views | 🔜 Next |
| 7 | Airtel Money adapter + callback verification | Planned |
| 8 | Admin commerce dashboard + commissions | Planned |
| 9 | Notifications & delivery management | Planned |
| 10 | Reviews, wishlist, promotions & loyalty | Planned |
| 11 | Production hardening, testing & deployment | Planned |

## 💳 Payment architecture

The payment domain is provider-aware without coupling checkout to one vendor. Each payment belongs to an order and stores provider, method, lifecycle status, merchant reference, provider reference, amount, currency, payer phone and normalized provider response.

The live flow is designed as:

**Order → Payment Transaction → Provider Request → Processing → Callback/Status Verification → Successful/Failed → Order Payment State**

### MTN MoMo

The repository contains an MTN Collections adapter. Its default sandbox base URL, subscription key, API user and API key are supplied through environment configuration. MTN documents RequestToPay as asynchronous: a successful request returns `202 Accepted`, the transaction is processed asynchronously, and the final result is delivered through a callback; MTN also recommends status polling as a fallback because callbacks may not be retried. citeturn0search0turn0search1

Required environment variables:

```env
MTN_MOMO_BASE_URL=https://sandbox.momodeveloper.mtn.com
MTN_MOMO_SUBSCRIPTION_KEY=
MTN_MOMO_API_USER=
MTN_MOMO_API_KEY=
MTN_MOMO_TARGET_ENVIRONMENT=sandbox
```

Do not put real credentials into GitHub. MTN's developer portal requires product subscription and provisioned API credentials before API use. citeturn0search8turn0search10

### Airtel Money

Airtel remains behind the provider contract until the exact merchant API endpoints, credentials and callback requirements are configured. Checkout is already provider-isolated so Airtel can be added without rewriting orders or inventory.

## 🏪 Marketplace architecture

The marketplace now has the core ownership model:

**Customer → Order → Order Item → Product → Seller**

A seller first submits a store application. An administrator approves or rejects the application. Only an approved seller is allowed into the seller dashboard. Product ownership is represented by `products.seller_id`, while `order_items.seller_id` preserves seller attribution at purchase time.

The current seller milestone deliberately stops before seller product CRUD, seller order management, commissions and payouts. Those are the next marketplace layer.

## 🏗️ Overall architecture direction

**Customer storefront → Cart → Checkout → Order → Payment → Fulfilment**

**Seller → Store → Products → Inventory → Orders → Earnings**

**Admin → Users → Seller approval → Products → Orders → Payments → Commissions → Analytics**

The existing inventory system is retained as the operational back office rather than discarded.

## 🛠️ Development notes

Run migrations after pulling the latest changes:

```bash
composer install
php artisan migrate
php artisan storage:link
php artisan serve
```

Clear Laravel's cached configuration after changing payment environment variables:

```bash
php artisan config:clear
php artisan config:cache
```

Never commit production credentials, API keys, signing secrets or payment-provider tokens.

## 📝 Upgrade log

### Phase 5 — Multi-Vendor Marketplace Foundation
**Implemented:**
- Added marketplace role field foundation to users.
- Added `seller_profiles` table with store identity and approval state.
- Added seller application flow.
- Added seller approval/rejection workflow for administrators.
- Added seller dashboard scoped to the authenticated seller's products.
- Added `seller_id` ownership to products.
- Added `seller_id` attribution to order items so seller ownership survives checkout.
- Added seller-facing application and dashboard UI.

**Boundary:** seller product CRUD, seller order views, commissions, payouts and seller analytics are intentionally separate next milestones. This prevents the first seller migration from coupling all marketplace concerns into one change.

### Phase 4 — MTN MoMo Provider Adapter & Callback Foundation
- Added MTN Collections adapter and environment-backed configuration.
- Added RequestToPay initiation and provider reference storage.
- Added public callback route and idempotent callback processing.
- Added automatic paid/confirmed order transition only after successful provider result.

### Phase 3 — Payment Architecture & Mobile-Money Foundation
- Added `payments` table and `Payment` model.
- Added order-to-payment relationship.
- Added protected payment initiation/status routes.
- Added MTN/Airtel method selection UI.
- Added payment lifecycle fields and transaction references.
- Added provider-agnostic gateway contract.

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
- Verify payment callbacks/server responses before marking orders paid.
- Make callback processing idempotent.
- Prefer provider status verification/polling as a fallback where documented.
- Use HTTPS for production payment callbacks.
- Enforce seller ownership on every seller product/order operation.
- Keep administrative seller approval behind authenticated role checks.

## 📌 Development principle

Every significant platform upgrade should include:
1. A focused implementation.
2. A clear Git commit message.
3. A GitHub development/comment log.
4. An update to this README.
5. A review before the next major module begins.

## License

This project is proprietary unless otherwise stated by its owner. The Laravel framework and its dependencies remain subject to their respective licenses.
