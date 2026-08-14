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

### Orders & checkout
- Customer checkout and delivery details
- Persistent orders and order items
- Unique order numbers
- Customer order history and order details
- Transaction-safe stock validation and inventory deduction
- Stock-out movement records linked to orders

### Payments
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

### Marketplace
- Customer-to-seller application flow
- Seller profile/store records
- Pending/approved/rejected seller lifecycle
- Admin seller approval and rejection workflow
- Seller dashboard scoped to seller-owned products
- Seller product creation, editing and deletion
- Seller-owned catalogue management
- Seller-specific order list and order details
- Seller order-status management
- Product ownership via `seller_id`
- Seller attribution retained on order items
- Marketplace schema migration explicitly creates seller profiles and seller ownership columns

### Seller financial engine — Phase 7
- Immutable-style financial ledger records for seller transactions
- Verified-payment settlement into seller earnings
- Configurable platform commission rate
- Separate sale credit and commission debit entries
- Seller credits, debits and available-balance calculations
- Seller earnings/ledger dashboard
- Idempotent paid-order settlement protection
- Payment transaction references carried into financial records

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
| 5 | Seller / multi-vendor marketplace foundation | ✅ Implemented |
| 6 | Seller product management + seller order views + schema reconciliation | 🟢 Implemented |
| 7 | Seller financial ledger + commission settlement | 🟢 Implemented |
| 8 | Airtel Money adapter + callback verification | 🔜 Next |
| 9 | Seller payouts + payout ledger + admin approval | Planned |
| 10 | Admin commerce dashboard + financial analytics | Planned |
| 11 | Notifications & delivery management | Planned |
| 12 | Reviews, wishlist, promotions & loyalty | Planned |
| 13 | Production hardening, testing & deployment | Planned |

## 💰 Seller financial architecture

Seller money is now represented through a dedicated ledger rather than calculated from display totals.

**Verified Payment → Seller Sale Credit + Platform Commission Debit → Seller Available Balance → Future Payout**

The default commission rate is configurable with:

```env
PLATFORM_COMMISSION_RATE=10
```

Each financial entry stores seller, order, payment, type, direction, amount, currency, unique reference, description and metadata. The ledger supports audit-friendly transaction history and prevents the application from silently rewriting historical earnings when product/order display values change.

### Settlement rules

- Settlement happens only after a payment is normalized as `successful`.
- Seller earnings are calculated per seller-owned order item.
- Commission is calculated per seller line item.
- A successful payment cannot create duplicate sale/commission entries for the same seller/order/payment combination.
- Seller available balance is credits minus debits.
- Payouts are deliberately not yet deducted until the dedicated payout ledger is implemented.

## 💳 Payment architecture

The payment domain is provider-aware without coupling checkout to one vendor. Each payment belongs to an order and stores provider, method, lifecycle status, merchant reference, provider reference, amount, currency, payer phone and normalized provider response.

The flow is designed as:

**Order → Payment Transaction → Provider Request → Processing → Callback/Status Verification → Successful/Failed → Financial Settlement → Order Fulfilment**

### MTN MoMo

The repository contains an MTN Collections adapter with environment-backed configuration. Production credentials must never be committed.

```env
MTN_MOMO_BASE_URL=https://sandbox.momodeveloper.mtn.com
MTN_MOMO_SUBSCRIPTION_KEY=
MTN_MOMO_API_USER=
MTN_MOMO_API_KEY=
MTN_MOMO_TARGET_ENVIRONMENT=sandbox
```

### Airtel Money

Airtel remains behind the provider contract until its exact merchant API endpoints, credentials and callback requirements are configured. Checkout and financial settlement are already provider-isolated.

## 🏪 Marketplace architecture

**Customer → Order → Order Item → Product → Seller**

A seller submits a store application. An administrator approves or rejects it. Approved sellers can manage their own catalogue and orders containing their products.

Seller operations are ownership-scoped: a seller cannot edit another seller's products or manage an unrelated order.

## 🏗️ Overall architecture direction

**Customer storefront → Cart → Checkout → Order → Payment → Fulfilment**

**Seller → Store → Products → Inventory → Orders → Earnings → Payouts**

**Admin → Users → Seller approval → Products → Orders → Payments → Commissions → Payouts → Analytics**

## 🛠️ Development notes

```bash
composer install
php artisan migrate
php artisan storage:link
php artisan serve
```

After changing payment or commission environment variables:

```bash
php artisan config:clear
php artisan config:cache
```

Never commit production credentials, API keys, signing secrets or payment-provider tokens.

## 📝 Upgrade log

### Phase 7 — Seller Financial Ledger & Commission Engine
**Implemented:**
- Added `financial_ledgers` migration.
- Added `FinancialLedger` model and seller/order/payment relationships.
- Added configurable `PLATFORM_COMMISSION_RATE`.
- Added `CommissionService` for per-seller settlement.
- Added sale credit and commission debit ledger entries.
- Added duplicate-settlement protection.
- Connected verified MTN payment success to financial settlement.
- Added `BalanceService` for credits, debits and available balance.
- Added seller finance route and earnings dashboard.

**Important boundary:** seller payouts are not deducted from available balance yet. Payouts will receive their own workflow and ledger entries so money cannot disappear through an informal balance update.

### Phase 6 — Seller Commerce Centre + Schema Integrity
- Added seller product create/edit/delete operations.
- Added strict seller ownership checks.
- Added seller catalogue and order-management views.
- Added seller order-status workflow.
- Added missing marketplace schema migration and reconciled repository state.

### Phase 5 — Multi-Vendor Marketplace Foundation
- Added seller applications, profiles and approval workflow.
- Added seller ownership and seller attribution to products/order items.

### Phase 4 — MTN MoMo Provider Adapter & Callback Foundation
- Added MTN Collections adapter and environment-backed configuration.
- Added RequestToPay initiation and provider reference storage.
- Added callback processing and successful-payment order transition.

### Phase 3 — Payment Architecture & Mobile-Money Foundation
- Added payments table/model, payment routes and provider gateway contract.

### Phase 2 — Checkout & Orders
- Added transactional checkout, orders, order items and stock deduction.

### Phase 1 — Storefront & Cart
- Added product catalogue, search, product details and session shopping cart.

## 🔐 Security principles

- Keep secrets in `.env` and outside version control.
- Validate all customer input server-side.
- Verify payment callbacks/server responses before marking orders paid.
- Make callback processing and financial settlement idempotent.
- Enforce seller ownership on seller operations.
- Keep seller approval behind authenticated admin authorization.
- Use HTTPS for production payment callbacks.
- Treat the financial ledger as an audit trail; do not mutate historical entries to fake balances.

## 📌 Development principle

Every significant platform upgrade must include:
1. Focused implementation.
2. Clear Git commit message.
3. GitHub development/comment log.
4. README update.
5. Repository verification.
6. Reconciliation of every failed/partial write before moving forward.

## License

This project is proprietary unless otherwise stated by its owner. The Laravel framework and dependencies remain subject to their respective licenses.
