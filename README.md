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
| 4 | MTN MoMo adapter + callback foundation | 🟡 In progress |
| 5 | Airtel Money provider adapter + callback verification | 🔜 Next |
| 6 | Seller / multi-vendor marketplace | Planned |
| 7 | Admin commerce dashboard | Planned |
| 8 | Notifications & delivery management | Planned |
| 9 | Reviews, wishlist, promotions & loyalty | Planned |
| 10 | Production hardening, testing & deployment | Planned |

## 💳 Payment architecture

The payment domain is provider-aware without coupling checkout to one vendor. Each payment belongs to an order and stores provider, method, lifecycle status, merchant reference, provider reference, amount, currency, payer phone and normalized provider response.

The live flow is designed as:

**Order → Payment Transaction → Provider Request → Processing → Callback/Status Verification → Successful/Failed → Order Payment State**

### MTN MoMo

The repository now contains an MTN Collections adapter. Its default sandbox base URL, subscription key, API user and API key are supplied through environment configuration. MTN's official documentation describes RequestToPay as asynchronous: a successful request returns `202 Accepted`, the transaction is processed asynchronously, and the final result is delivered through a callback; MTN also recommends status polling as a fallback because callbacks may not be retried. citeturn0search0turn0search1

Required environment variables:

```env
MTN_MOMO_BASE_URL=https://sandbox.momodeveloper.mtn.com
MTN_MOMO_SUBSCRIPTION_KEY=
MTN_MOMO_API_USER=
MTN_MOMO_API_KEY=
MTN_MOMO_TARGET_ENVIRONMENT=sandbox
```

Do not put real credentials into GitHub. The MTN developer portal requires a product subscription and provisioned API credentials before API use. citeturn0search8turn0search10

### Airtel Money

Airtel remains intentionally behind the provider contract until its exact production/sandbox API credentials, endpoints and callback requirements are configured for the merchant account. The checkout layer already isolates provider selection so Airtel can be added without rewriting orders or inventory.

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

Clear Laravel's cached configuration after changing payment environment variables:

```bash
php artisan config:clear
php artisan config:cache
```

Never commit production credentials, API keys, signing secrets or payment-provider tokens.

## 📝 Upgrade log

### Phase 4 — MTN MoMo Provider Adapter & Callback Foundation
**Implemented:**
- Added `MtnMomoGateway` implementing the platform `PaymentGateway` contract.
- Added MTN OAuth/token acquisition using environment credentials.
- Added MTN Collections RequestToPay initiation.
- Added Uganda MSISDN normalization for customer phone numbers.
- Added callback URL registration in the RequestToPay request.
- Added provider reference storage.
- Added MTN callback normalization into the platform payment lifecycle.
- Added idempotent callback behavior for already-finalized payments.
- Added automatic order transition to `paid/confirmed` only after a successful callback.
- Added environment-backed MTN configuration.
- Added public callback route separate from authenticated customer routes.

**Important implementation detail:** the adapter is configured for MTN's documented asynchronous RequestToPay model. The repository does not contain merchant credentials. A production deployment must use the merchant's actual MTN subscription/API credentials and HTTPS callback host.

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
- Prefer provider status verification/polling as a fallback where the provider documents it.
- Use HTTPS for production payment callbacks.
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
