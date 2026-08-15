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

### Customer engagement — Phase 12
- Customer wishlist with duplicate protection and storefront add/remove/cart actions
- Verified-purchase product reviews
- 1–5 star product ratings
- Review moderation with pending/approved/rejected states
- Admin review moderation screen
- Product average-rating helpers
- Promotion/coupon schema with percentage or fixed discounts
- Promotion validity rules: active window, minimum order and usage limit
- Promotion validation endpoint
- Admin promotion creation and activation/pause interface
- Purchase-based loyalty ledger
- Idempotent loyalty points awarded when an order is delivered
- Customer loyalty balance and transaction history
- Checkout promotion-code and loyalty-redemption controls

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
- Server-side promotion and loyalty validation during checkout

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
| 12 | Reviews, wishlist, promotions & loyalty | 🟡 Integration/testing closure |
| 13 | Production hardening, testing & deployment | Planned |

## 🔐 Staff authentication architecture

Ujuzi Shop Mall deliberately separates **customer authentication** from **privileged platform access**.

### Customers
Customers can create accounts when they need order tracking, purchase history or account-based checkout. They authenticate normally and are **not required to enter a staff OTP**.

### Inventory managers and administrators
These roles are considered privileged because they can operate the platform's inventory, products, seller, financial or administrative functions.

Their login is:

**Email + Password → Password Verified → Email OTP Sent → OTP Verified → Authenticated Staff Session**

## ⭐ Reviews
Reviews are tied to a real delivered order containing the reviewed product. Customers submit a 1–5 rating and written feedback; submissions enter moderation before public display. Admins can approve or reject reviews.

## ❤️ Wishlist
Wishlist entries belong to the authenticated customer and are uniquely constrained by customer + product. Customers can remove saved products or add available products directly to their cart.

## 🎟️ Promotions
Promotion codes support:
- Percentage discounts
- Fixed discounts
- Minimum order amounts
- Start/end dates
- Usage limits
- Active/inactive state

Promotion validation occurs server-side during checkout. Invalid, expired, inactive, exhausted or below-minimum promotions are rejected.

## 🏆 Loyalty
Completed/delivered orders can award loyalty points through an immutable transaction ledger. The base earning rule is **1 point per UGX 1,000 of delivered-order value**. Awarding is idempotent: the same order cannot generate purchase points twice.

The balance is calculated from the ledger. Customers can view their balance/history and submit points for redemption during checkout; redemption is validated inside the checkout transaction.

## 📦 Checkout and customer identity
A customer account is used to keep cart/order ownership and purchase history separated between people. At checkout the order independently captures:

- Full name
- Email address
- Phone number
- Delivery address
- Optional delivery notes
- Optional promotion code
- Optional loyalty points to redeem

## 🔔 Notification architecture
**Order Event → NotificationLog → Queued Mailable → Email Provider → Customer**

Transactional notifications cover order, payment and fulfilment events.

## 📦 Delivery tracking
The customer order page displays:

**Confirmed → Processing → Ready → Shipped → Delivered**

## 💰 Seller financial architecture
Seller money is represented through a dedicated ledger rather than calculated from display totals.

**Verified Payment → Seller Sale Credit + Platform Commission Debit → Seller Available Balance → Payout Request → Admin Approval → Payout Reservation → Provider Settlement**

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

### Phase 12 — Reviews, Wishlist, Promotions & Loyalty — INTEGRATION/TESTING CLOSURE
**Implemented:**
- Added wishlist database schema, model, controller, storefront page and product-page controls.
- Added verified-purchase review schema, model, submission workflow and product-page display.
- Added admin review moderation controller, interface and routes.
- Added product review relationships and average-rating helper.
- Added promotion schema supporting fixed/percentage discounts, dates, limits and minimum order.
- Added promotion validation endpoint and transactional checkout application.
- Added admin promotion controller, creation interface and activation/pause controls.
- Added loyalty transaction ledger and balance service.
- Added idempotent loyalty points for delivered orders.
- Added customer loyalty balance/history interface.
- Added checkout loyalty redemption validation and ledger debit.
- Added checkout customer identity, promotion and loyalty controls.

**Final closure work:**
- Automated business-rule tests for review eligibility, wishlist uniqueness, promotion validity/limits and loyalty redemption/award idempotency.
- Repository-wide route/model/view consistency verification.
- Final production-hardening checks before Phase 13.

### Phase 11 — Customer Notifications & Delivery Management — COMPLETE
- Added customer order confirmation, payment and fulfilment notifications.
- Added queued notification handling and audit logging.
- Added customer delivery timeline and sequential seller delivery transitions.

### Phase 10A — Staff Email OTP Security Reconciliation
- Reconciled privileged staff email OTP authentication.
- Limited mandatory OTP to `admin` and `inventory_manager` roles.
- Kept customers outside the staff OTP flow.

### Phase 10 — Admin Commerce Command Centre
- Added centralized commerce KPIs and operational reporting.

### Phases 1–9
- Storefront, checkout, payments, marketplace, seller finance, Airtel/MTN integration and payouts implemented as documented above.

## 🔒 Security principles
- Keep secrets in `.env` and outside version control.
- Validate all customer input server-side.
- Verify payment callbacks/server responses before marking orders paid.
- Make callback processing and financial settlement idempotent.
- Enforce seller ownership on seller operations.
- Keep seller approval behind authenticated admin authorization.
- Treat the financial and loyalty ledgers as audit trails.
- Never trust client-side discount calculations; recompute promotions server-side.
- Only permit verified purchasers to review delivered products.

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
