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

### Customer notifications & delivery — Phase 11
- Order confirmation email queued after checkout
- Payment success/failure email queued after provider callback
- Order-status email notifications for fulfilment changes
- Delivery-specific messages for `shipped` and `delivered`
- Customer-facing delivery timeline: confirmed → processing → ready → shipped → delivered
- Notification audit log storing recipient, type, order, status and failure reason
- Queued mail delivery so email problems do not block checkout/order state changes
- Failed queued notifications are recorded for operational review
- Customer email is persisted on the order and used as the notification destination
- Seller delivery transitions are sequentially enforced
- Seller notifications use the centralized notification service

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

### Admin Commerce Command Centre — Phase 10
- Central admin commerce dashboard
- Sales and paid-order KPIs
- Customer and product counts
- Low-stock monitoring
- Seller and pending-seller monitoring
- Pending/processing payment monitoring
- Platform commission totals
- Seller credit totals
- Daily paid-sales reporting
- Recent order operational view
- Payment-method/status health breakdown
- Inventory watch list
- Configurable 7–365 day reporting window
- Authenticated admin-only access boundary

### Staff security — reconciled before Phase 11
- Privileged staff password authentication followed by mandatory email OTP
- OTP applies to `admin` and `inventory_manager` roles only
- Customers are not forced through staff OTP
- OTP is generated only after successful password verification
- OTP is sent to the staff member's registered email address
- OTP is hashed at rest
- OTP expires after 5 minutes
- Previous unconsumed OTP is invalidated when a new code is issued
- Maximum 3 OTP requests per 10-minute window
- OTP is single-use and consumed before the authenticated session is created
- Google sign-in for privileged staff also enters the OTP challenge before access
- Dedicated staff OTP verification screen
- Pending staff login is held in the session until OTP verification succeeds

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
| 10 | Admin commerce dashboard + financial analytics | 🟢 Implemented |
| 10A | Staff email OTP security reconciliation | 🟢 Implemented |
| 11 | Customer notifications + delivery management | 🟢 Implemented |
| 12 | Reviews, wishlist, promotions & loyalty | Planned |
| 13 | Production hardening, testing & deployment | Planned |

## 🔐 Staff authentication architecture

Ujuzi Shop Mall deliberately separates **customer authentication** from **privileged platform access**.

### Customers

Customers can create accounts when they need order tracking, purchase history or account-based checkout. They authenticate normally and are **not required to enter a staff OTP**.

### Inventory managers and administrators

These roles are considered privileged because they can operate the platform's inventory, products, seller, financial or administrative functions.

Their login is:

**Email + Password → Password Verified → Email OTP Sent → OTP Verified → Authenticated Staff Session**

The OTP challenge is also enforced after Google authentication for these roles, so the Google login path cannot bypass the second factor.

The OTP is never stored in plaintext. The database stores a hash, with an expiry timestamp, consumed timestamp and request controls.

## 📦 Checkout and customer identity

A customer account is used to keep cart/order ownership and purchase history separated between people. At checkout the order independently captures:

- Full name
- Email address
- Phone number
- Delivery address
- Optional delivery notes

The customer's email is persisted directly on the order so delivery notifications remain tied to the contact information supplied for that purchase.

## 🔔 Notification architecture

**Order Event → NotificationLog → Queued Mailable → Email Provider → Customer**

Transactional notifications cover:

- Order confirmation
- Payment success
- Payment failure
- Processing
- Ready for dispatch
- Shipped
- Delivered

The notification layer is event-driven and transactional rather than a general marketing-mail system. A notification failure must never undo a successful order or payment transaction.

## 📦 Delivery tracking

The customer order page displays:

**Confirmed → Processing → Ready → Shipped → Delivered**

Seller status transitions are sequentially enforced:

**Pending → Processing → Ready → Shipped → Delivered**

A seller cannot skip directly from pending to shipped or delivered, and a delivered order cannot be moved backwards through the workflow. Seller access remains ownership-scoped to orders containing that seller's products. Customer order pages remain protected by `user_id`.

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

## 💳 Payment architecture

The payment domain is provider-aware without coupling checkout to one vendor. Each payment belongs to an order and stores provider, method, lifecycle status, merchant reference, provider reference, amount, currency, payer phone and normalized provider response.

The flow is:

**Order → Payment Transaction → Provider Request → Processing → Callback/Status Verification → Successful/Failed → Financial Settlement → Order Fulfilment → Notification**

## 🏪 Marketplace architecture

**Customer → Order → Order Item → Product → Seller**

A seller submits a store application. An administrator approves or rejects it. Approved sellers can manage their own catalogue and orders containing their products.

Seller operations are ownership-scoped: a seller cannot edit another seller's products or manage an unrelated order.

## 🏗️ Overall architecture direction

**Customer storefront → Cart → Checkout → Order → Payment → Delivery → Customer Notification**

**Seller → Store → Products → Inventory → Orders → Earnings → Payouts**

**Admin → Users → Seller approval → Products → Orders → Payments → Commissions → Payouts → Analytics**

## 🛠️ Development notes

```bash
composer install
php artisan migrate
php artisan storage:link
php artisan serve
```

For queued email notifications, production must run a queue worker appropriate to the configured queue driver.

Never commit production credentials, API keys, signing secrets or payment-provider tokens.

## 📝 Upgrade log

### Phase 11 — Customer Notifications & Delivery Management — COMPLETE
**Implemented:**
- Added customer order confirmation email.
- Added payment success/failure notifications from provider callbacks.
- Added queued order-status notifications.
- Added notification audit logging.
- Added notification failure capture.
- Added customer delivery timeline.
- Added responsive delivery timeline styling.
- Centralized notification dispatch through `NotificationService`.
- Reconciled the seller order controller after the earlier SHA conflict.
- Removed direct mail dispatch from the seller order controller.
- Enforced sequential seller delivery status transitions.
- Prevented invalid status jumps and backward movement after delivery.
- Preserved seller ownership authorization.
- Preserved customer order ownership authorization.

**Completion boundary:** transactional notifications are active for order, payment and fulfilment events. Non-essential marketing notifications/preferences remain outside this phase by design.

### Phase 10A — Staff Email OTP Security Reconciliation
- Reconciled privileged staff email OTP authentication.
- Limited mandatory OTP to `admin` and `inventory_manager` roles.
- Kept customers outside the staff OTP flow.

### Phase 10 — Admin Commerce Command Centre
- Added centralized commerce KPIs and operational reporting.

### Phase 9 — Seller Payout Engine
- Added payout records, reservations, admin approval and failure reversal.

### Phase 8 — Airtel Money Provider Integration
- Added Airtel Money gateway and callback integration.

### Phase 7 — Seller Financial Ledger & Commission Engine
- Added financial ledger and commission settlement.

### Phase 6 — Seller Commerce Centre + Schema Integrity
- Added seller product/order operations and ownership enforcement.

### Phase 5 — Multi-Vendor Marketplace Foundation
- Added seller applications, profiles and approval workflow.

### Phase 4 — MTN MoMo Provider Adapter & Callback Foundation
- Added MTN Collections adapter and provider reference handling.

### Phase 3 — Payment Architecture & Mobile-Money Foundation
- Added payments table/model, payment routes and provider gateway contract.

### Phase 2 — Checkout & Orders
- Added transactional checkout, orders, order items and stock deduction.

### Phase 1 — Storefront & Cart
- Added product catalogue, search, product details and session shopping cart.

## 🔒 Security principles

- Keep secrets in `.env` and outside version control.
- Validate all customer input server-side.
- Verify payment callbacks/server responses before marking orders paid.
- Make callback processing and financial settlement idempotent.
- Enforce seller ownership on seller operations.
- Keep seller approval behind authenticated admin authorization.
- Use HTTPS for production payment callbacks.
- Treat the financial ledger as an audit trail.
- Keep provider credentials and production endpoints out of Git history.
- Never mark a payout paid without recording actual provider/reference evidence.
- Require email OTP after password/Google authentication for privileged staff.
- Never create an authenticated staff session before the OTP challenge succeeds.

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
