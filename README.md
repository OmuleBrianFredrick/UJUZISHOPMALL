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
- Airtel Money gateway adapter with environment-backed credentials
- MTN and Airtel callback endpoints
- Provider-neutral callback normalization and payment/ledger settlement
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

### Seller financial engine
- Dedicated financial ledger
- Verified-payment settlement into seller earnings
- Configurable platform commission rate
- Separate sale credit and commission debit entries
- Seller credits, debits and available-balance calculations
- Seller earnings/ledger dashboard
- Idempotent paid-order settlement protection
- Payment transaction references carried into financial records
- Seller payout requests
- Minimum payout threshold
- Payout reservation against seller balance
- Admin payout approval
- Payout failure reversal
- Seller payout history

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
| 8 | Airtel Money adapter + callback verification | 🟢 Implemented |
| 9 | Seller payouts + payout ledger + admin approval | 🟢 Implemented |
| 10 | Admin commerce dashboard + financial analytics | 🔜 Next |
| 11 | Notifications & delivery management | Planned |
| 12 | Reviews, wishlist, promotions & loyalty | Planned |
| 13 | Production hardening, testing & deployment | Planned |

## 💰 Seller financial architecture

Seller money is represented through a dedicated ledger rather than calculated from display totals.

**Verified Payment → Seller Sale Credit + Platform Commission Debit → Seller Available Balance → Payout Request → Admin Approval → Payout Reservation → Provider Settlement**

The default commission rate is configurable with:

```env
PLATFORM_COMMISSION_RATE=10
```

The default minimum payout threshold is configurable with:

```env
MINIMUM_SELLER_PAYOUT=10000
```

Each financial entry stores seller, order/payment context when applicable, type, direction, amount, currency, unique reference, description and metadata. The ledger supports audit-friendly transaction history and prevents the application from silently rewriting historical earnings when product/order display values change.

### Payout lifecycle

1. Seller submits a payout request.
2. The system checks available balance after other pending/approved/processing reservations.
3. Payout remains `pending` until administrator approval.
4. Approval atomically reserves the requested amount with a debit ledger entry.
5. The payout can be completed with a provider reference after the actual mobile-money disbursement.
6. A failed approved/processing payout creates a compensating credit ledger entry and restores the seller's available balance.
7. Payout records remain immutable in history rather than being deleted.

### Important provider boundary

The payout accounting and approval engine is production-safe, but **automated MTN/Airtel disbursement is deliberately not fabricated**. Collections APIs and disbursement APIs are separate products with separate merchant credentials and provider-specific contracts. Phase 9 therefore records the exact provider reference supplied after the disbursement, while Phase 10/production configuration will connect the approved disbursement adapters once the merchant disbursement credentials and provider-approved endpoints are available.

## 💳 Payment architecture

The payment domain is provider-aware without coupling checkout to one vendor. Each payment belongs to an order and stores provider, method, lifecycle status, merchant reference, provider reference, amount, currency, payer phone and normalized provider response.

The flow is:

**Order → Payment Transaction → Provider Request → Processing → Callback/Status Verification → Successful/Failed → Financial Settlement → Order Fulfilment**

### MTN MoMo

MTN remains configured through environment variables and the existing collections adapter. Its callback continues to feed the common settlement path.

```env
MTN_MOMO_BASE_URL=https://sandbox.momodeveloper.mtn.com
MTN_MOMO_SUBSCRIPTION_KEY=
MTN_MOMO_API_USER=
MTN_MOMO_API_KEY=
MTN_MOMO_TARGET_ENVIRONMENT=sandbox
```

### Airtel Money — Phase 8

Airtel Money now has a concrete gateway adapter, payment-manager registration, environment configuration and callback route. The adapter obtains its access token from configured credentials, submits the payment request, records the provider reference and normalizes provider responses into the same `successful / failed / processing` lifecycle used by MTN.

Required configuration:

```env
AIRTEL_MONEY_BASE_URL=
AIRTEL_MONEY_CLIENT_ID=
AIRTEL_MONEY_CLIENT_SECRET=
AIRTEL_MONEY_COUNTRY=UG
AIRTEL_MONEY_CURRENCY=UGX
```

The exact production base URL and merchant credentials must be supplied from the Airtel Money merchant/API account before live transactions are enabled. The application deliberately fails closed when those values are absent.

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

### Phase 9 — Seller Payout Engine
**Implemented:**
- Added the missing `financial_ledgers` migration after repository reconciliation showed the model existed without its migration.
- Added `payouts` migration and `Payout` model.
- Added seller payout request workflow.
- Added configurable minimum payout threshold.
- Added balance-aware payout reservation so multiple pending requests cannot oversubscribe seller funds.
- Added admin payout queue and approval workflow.
- Added payout reservation debit ledger entry.
- Added paid-state recording with provider reference.
- Added failure-state handling with compensating payout-reversal credit.
- Added seller payout history UI.
- Added admin payout operations UI.
- Added seller and admin routes with role checks.
- Kept automated provider disbursement behind a real provider credential/API boundary instead of inventing endpoints.

### Phase 8 — Airtel Money Provider Integration
- Added `AirtelMoneyGateway` implementing the existing provider contract.
- Added Airtel client ID/secret/base URL configuration.
- Registered `airtel_money` in `PaymentManager`.
- Added Airtel payment initiation and provider-reference capture.
- Added Airtel callback normalization.
- Added `/payments/callback/airtel` endpoint.
- Unified MTN/Airtel callback settlement through one provider-neutral payment path.
- Added provider mismatch protection and idempotent terminal-state handling.
- Reused the existing financial settlement engine after successful Airtel payment.

### Phase 7 — Seller Financial Ledger & Commission Engine
- Added financial ledger model and settlement engine.
- Added configurable platform commission.
- Added seller sale credits and platform commission debits.
- Added duplicate-settlement protection.
- Added seller balance service and finance dashboard.
- Reconciled the repository with a missing financial-ledger migration in Phase 9.

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
- Keep provider credentials and production endpoints out of Git history.
- Never mark a payout paid without recording the actual provider/reference evidence.

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
