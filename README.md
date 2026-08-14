# Ujuzi Shop Mall

Ujuzi Shop Mall is a Laravel-based commerce platform being developed from an inventory-management foundation into a full online marketplace for customers, sellers and administrators.

## 🚀 Current platform capabilities

### Storefront
- Product catalogue
- Product search
- Category filtering
- Product sorting
- Product detail pages
- Related products
- Responsive shopping experience

### Shopping cart
- Session-based cart
- Add/remove products
- Quantity updates
- Automatic cart totals
- Stock-aware cart operations

### Orders & checkout — Phase 2
- Customer checkout form
- Delivery details
- Persistent orders and order items
- Unique order numbers
- Customer order history
- Order detail/confirmation pages
- Transaction-safe stock validation
- Automatic inventory deduction after order placement
- Stock-out movement records linked to customer orders
- Payment status tracking foundation

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
| 3 | Payments | 🔜 Next |
| 4 | Seller / multi-vendor marketplace | Planned |
| 5 | Admin commerce dashboard | Planned |
| 6 | Notifications & delivery management | Planned |
| 7 | Reviews, wishlist, promotions & loyalty | Planned |
| 8 | Production hardening, testing & deployment | Planned |

## 💳 Next major milestone: payments

The next upgrade will introduce a payment architecture designed for Uganda, with support planned for mobile-money payment flows such as MTN Mobile Money and Airtel Money. Payment callbacks, transaction records, verification and order-payment state transitions will be added without coupling the commerce layer to a single provider.

## 🏗️ Architecture direction

The platform is intentionally being developed in layers:

**Customer storefront → Cart → Checkout → Order → Payment → Fulfilment**

and on the management side:

**Inventory → Orders → Payments → Sellers → Analytics**

The existing inventory system is being retained as the back office rather than discarded.

## 🛠️ Development notes

After pulling the latest changes, run your normal Laravel setup commands and migrations for the environment. Phase 2 introduces the `orders` and `order_items` tables, so database migrations must be executed before using checkout.

Typical local commands:

```bash
composer install
php artisan migrate
php artisan storage:link
php artisan serve
```

Use the project's configured `.env` database and application settings. Never commit production credentials or payment-provider secrets.

## 📝 Upgrade log

### Phase 2 — Checkout & Orders
**Implemented:**
- Added `Order` and `OrderItem` models.
- Added `orders` and `order_items` migrations.
- Added transactional checkout processing.
- Added stock locking and stock deduction during order creation.
- Added stock movement records for customer orders.
- Added customer order history and order detail pages.
- Connected the cart directly to checkout.
- Added responsive checkout/order styling.

**Important implementation detail:** checkout validates current inventory inside a database transaction and locks the affected products before creating the order and deducting stock. This reduces the risk of overselling when multiple customers attempt to buy the same product.

### Phase 1 — Storefront & Cart
**Implemented:**
- Added customer product catalogue.
- Added product search, category filtering and sorting.
- Added product detail pages and related products.
- Added session shopping cart.
- Added cart quantity updates/removal.
- Added Shop and Cart navigation.
- Added responsive storefront styling.

## 🔐 Security

- Keep secrets in `.env` and outside version control.
- Validate all customer input server-side.
- Use authenticated routes for customer commerce operations.
- Keep payment credentials out of source code.
- Review authorization rules before exposing administrative order operations.

## 📌 Development principle

Every significant platform upgrade should include:
1. A focused implementation.
2. A clear Git commit message.
3. A GitHub development/comment log where appropriate.
4. An update to this README.
5. A review before the next major module begins.

## License

This project is proprietary unless otherwise stated by its owner. The Laravel framework and its dependencies remain subject to their respective licenses.
