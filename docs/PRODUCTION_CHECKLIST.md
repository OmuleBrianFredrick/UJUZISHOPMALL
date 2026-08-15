# Ujuzi Shop Mall — Production Release Checklist

Use this checklist before the first production launch and after every material release.

## A. Repository integrity

- [ ] Target release commit is identified and reviewed.
- [ ] No secrets, passwords, OTP values, payment credentials or private keys are committed.
- [ ] `composer.json` and the CI runtime agree on the supported PHP version.
- [ ] Routes, controllers, models, views and migrations have been reconciled.
- [ ] README and upgrade log describe the release accurately.

## B. Environment

- [ ] `APP_ENV=production`.
- [ ] `APP_DEBUG=false`.
- [ ] `APP_KEY` is configured and kept secret.
- [ ] `APP_URL` points to the production HTTPS URL.
- [ ] Production database credentials are configured.
- [ ] Production cache/session drivers are configured.
- [ ] Queue connection is configured.
- [ ] Mail transport and sender address are configured.
- [ ] MTN/Airtel payment credentials and callback URLs are configured and verified.
- [ ] Google authentication credentials are configured if Google login is enabled.

## C. Database

- [ ] Full backup completed before deployment.
- [ ] Backup restoration has been tested recently.
- [ ] `php artisan migrate --force` completes successfully.
- [ ] No SQL-first table is missing a repository migration definition.
- [ ] Foreign keys and unique constraints are present.
- [ ] Indexes required by order, payment, promotion, review, wishlist and loyalty queries are present.

## D. Application verification

- [ ] `php artisan app:production-readiness` passes.
- [ ] `php artisan test` passes.
- [ ] `GET /health` returns HTTP 200.
- [ ] Customer registration/login works.
- [ ] Customer login does not invoke staff OTP.
- [ ] Admin/inventory-manager login invokes email OTP.
- [ ] OTP expires and cannot be reused.
- [ ] Product catalogue and stock display work.
- [ ] Cart operations work.
- [ ] Checkout validates customer email, phone and delivery address.
- [ ] Promotion validation and limits work.
- [ ] Loyalty balance/redemption rules work.
- [ ] Order ownership prevents cross-customer access.
- [ ] Payment ownership prevents cross-customer access.
- [ ] Payment callbacks verify provider responses before settlement.
- [ ] Seller/customer authorization boundaries work.
- [ ] Review moderation and verified-purchase rules work.
- [ ] Delivery notifications are queued and processed.

## E. Workers and scheduler

- [ ] Queue worker is running under a process manager.
- [ ] Failed jobs are monitored/retried according to policy.
- [ ] Laravel scheduler is configured if scheduled commands are used.
- [ ] Workers are restarted after deployments when required.

## F. Observability

- [ ] Application logs are writable.
- [ ] Daily log rotation/retention is configured.
- [ ] Web-server logs are accessible.
- [ ] Payment callback failures are observable without logging secrets.
- [ ] Queue failures are observable.
- [ ] `/health` is monitored by the hosting/monitoring system.
- [ ] Alerting ownership is defined.

## G. Rollback and recovery

- [ ] Previous application release is retained.
- [ ] Database backup is retained independently of the application host.
- [ ] Rollback procedure has an owner.
- [ ] Payment/order database rollback is treated as a controlled incident, not an ordinary code rollback.
- [ ] Restoration drill has been completed and documented.

## H. Final sign-off

The release may be marked **Production Ready** only when:

1. CI has completed successfully on the release commit.
2. Production readiness checks pass.
3. Database migration succeeds.
4. Health check returns healthy.
5. Critical customer, staff, seller, checkout and payment smoke tests pass.
6. Backup/recovery controls are confirmed.
7. Monitoring and queue workers are operational.
