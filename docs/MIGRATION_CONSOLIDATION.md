Migration consolidation proposal

What I found

During test runs with sqlite in-memory the test suite failed due to duplicate/overlapping migration files that attempt to create the same tables or add the same columns. To get tests and CI green I removed several duplicate migration files in the feature branch, but the repository still contains other duplicate migration files that will cause issues for fresh installs, test runs, and other branches.

Duplicates observed (examples):
- otps (multiple create_otps migration files)
- financial_ledgers (2026_08_14_090000_create_financial_ledgers_table.php and 2026_08_14_170001_create_financial_ledgers_table.php)
- wishlists (duplicate create_wishlists)
- promotions / promotion_usages (duplicate migrations)
- reviews / product_reviews (duplicate migrations)

Why this matters

Laravel migrations are meant to be an append-only history; duplicate create/alter migrations create collisions when running them on a clean database (for example CI sqlite in-memory). It is important to consolidate to a single canonical migration set representing the current schema.

Proposed plan

1. Create a single canonical migration per top-level table that represents the current schema (e.g., create_financial_ledgers_table.php with the final column set and indexes).  
2. Remove historical duplicates from the repo (or move them to an archive folder) so fresh runs see only the canonical migrations.  
3. Optionally, create a single "squashed" migration for production after consensus, and keep historical files in a docs/archive/ folder if you need to preserve history.  
4. Run the full test matrix and CI after consolidation to ensure nothing regresses.

Notes and next steps

- I removed some duplicate migration files on the feature branch to make tests pass locally; however, those deletes should be reviewed as part of a consolidation PR so everyone agrees on the canonical set.  
- I can open a follow-up PR that proposes a canonical set and removes the duplicates (with a clear changelog in the PR). If you want that PR now, I can prepare it and open a draft.

If you'd like me to prepare the consolidation PR (draft), reply and I'll include a proposed canonical migration list and the candidate files to remove.