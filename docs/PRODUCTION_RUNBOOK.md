# Ujuzi Shop Mall — Production Release Runbook

## 1. Pre-release

- Confirm `.env` contains production-only credentials and is not committed.
- Set `APP_ENV=production` and `APP_DEBUG=false`.
- Configure `APP_URL` and HTTPS.
- Configure database, cache, session and queue connections.
- Configure mail and payment-provider credentials.
- Run `php artisan config:cache` and `php artisan route:cache` after validating environment values.

## 2. Database

Before deployment:

```bash
php artisan migrate --force
php artisan app:production-readiness
```

Take a database backup before destructive schema changes. Test restoration periodically in a non-production environment.

## 3. Application workers

Run a persistent queue worker using the production process manager:

```bash
php artisan queue:work --sleep=3 --tries=3 --timeout=120
```

Restart workers after deployments when application code changes:

```bash
php artisan queue:restart
```

If scheduled jobs are used, configure the host scheduler to run Laravel's scheduler every minute.

## 4. Deployment verification

Check:

- Landing/storefront responds over HTTPS.
- Customer login/register works.
- Staff password + OTP flow works.
- Product listing and cart work.
- Checkout validation works.
- Payment initiation/callbacks are functioning.
- Customer order history is isolated by ownership.
- Seller dashboard/product management is restricted to authorized sellers.
- Admin review/promotion/payout controls are restricted to authorized staff.
- Queue worker is processing notification jobs.
- Application and web-server logs contain no unexpected exceptions.

## 5. Health/readiness

Run:

```bash
php artisan app:production-readiness
php artisan test
```

A deployment is not considered ready if either command fails.

## 6. Rollback

- Stop/restart application workers if necessary.
- Restore the previous application release.
- Restore the database only when the release introduced an incompatible/destructive migration and the rollback plan explicitly requires it.
- Never perform an unplanned production database rollback against a live payment/order system.
- Re-run readiness and smoke checks after rollback.

## 7. Backups

At minimum:

- Daily database backup.
- Retain multiple restore points.
- Encrypt backups at rest.
- Keep a geographically separate backup where operationally appropriate.
- Perform periodic restoration drills.

## 8. Incident evidence

When investigating a production incident, preserve:

- Application logs.
- Web-server logs.
- Queue failure records.
- Payment callback/request identifiers.
- Order/payment IDs.
- Deployment commit SHA.

Do not record passwords, OTP values, API secrets, payment credentials or other sensitive personal data in logs.

## 9. Release principle

**Backup → migrate → readiness check → tests → workers → smoke test → monitor.**

Do not announce a release as production-ready until all critical checks pass.
