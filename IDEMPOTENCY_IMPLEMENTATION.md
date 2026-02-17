# Idempotency Key Implementation

## Overview
This document describes the idempotency key implementation for the models: Client, Deal, Cashfund, and Repayment.

## What is Idempotency?
Idempotency ensures that multiple identical requests have the same effect as a single request. This prevents duplicate records from being created when a user accidentally submits a form multiple times (e.g., double-clicking the submit button).

## Implementation Details

### 1. Database Schema
A new migration was created: `2026_02_17_173452_add_idempotency_key_to_tables.php`

**What it does:**
- Adds a `idempotency_key` column (UUID type, NOT NULL) to the following tables:
  - `clients`
  - `deals`
  - `cashfunds`
  - `repayments`
- Creates a composite unique index on `[id, idempotency_key]` for each table
- This ensures database-level enforcement that prevents duplicate idempotency keys and guarantees data integrity

**To run the migration:**
```bash
php artisan migrate
```

### 2. Frontend Changes
Each create form now generates a unique UUID as a hidden field using Laravel's `Str::uuid()` helper:

- `resources/views/clients/create.blade.php`
- `resources/views/deals/create.blade.php`
- `resources/views/cashfund/create.blade.php`
- `resources/views/repayments/create.blade.php`

**Implementation:**
```blade
<input type="hidden" name="idempotency_key" value="{{ Str::uuid() }}">
```

This UUID is generated server-side when the form is rendered and remains the same if the user resubmits the same form.

### 3. Backend Changes
All store methods in controllers now use try-catch blocks around the create operation to handle duplicate idempotency keys:

#### ClientsController
- Validates `idempotency_key` as required UUID
- Attempts to create the client record
- If a duplicate idempotency_key constraint violation occurs (error code 23000), finds the existing client and redirects to its show page
- Otherwise, creates a new client with the idempotency key

#### DealsController
- Validates `idempotency_key` as required UUID
- Attempts to create the deal record
- If a duplicate idempotency_key constraint violation occurs (error code 23000), finds the existing deal and redirects to its show page
- Otherwise, creates a new deal with the idempotency key

#### CashfundController
- Validates `idempotency_key` as required UUID
- Attempts to create the cashfund record
- If a duplicate idempotency_key constraint violation occurs (error code 23000), redirects to the cash index page
- Otherwise, creates a new cashfund with the idempotency key

#### RepaymentsController
- Validates `idempotency_key` as required UUID
- Attempts to create the repayment record
- If a duplicate idempotency_key constraint violation occurs (error code 23000), finds the existing repayment and redirects to the associated deal's show page
- Otherwise, creates a new repayment with the idempotency key

## How It Works

1. **User opens form**: Frontend generates a unique UUID and stores it in a hidden field
2. **User submits form**: The UUID is sent along with other form data
3. **Backend validation**: Controller validates that the UUID is present and properly formatted
4. **Idempotency check**: Controller checks if a record with this UUID already exists
5. **Decision**:
   - If exists: Redirect to the existing record (no duplicate created)
   - If not exists: Create new record with the UUID

## Benefits

1. **Prevents duplicate submissions**: Multiple clicks on submit button won't create duplicates
2. **Database-level safety**: Unique constraint ensures no duplicates even in race conditions
3. **User-friendly**: Users are redirected to the existing record instead of seeing errors
4. **Standard practice**: Follows REST API best practices for idempotency

## Testing

To test the implementation:

1. Run the migration: `php artisan migrate`
2. Open any create form (Client, Deal, Cashfund, or Repayment)
3. Fill out the form
4. Submit the form
5. Use browser back button to go back to the form
6. Submit the same form again
7. Verify that no duplicate is created and you're redirected to the existing record

## Notes

- The idempotency key is generated server-side using Laravel's `Str::uuid()`, so refreshing the form page will generate a new key
- The key persists if the user clicks submit multiple times or uses browser back button after submission
- The column is NOT NULL, so all new records must have an idempotency_key
- The composite unique index `[id, idempotency_key]` ensures that each record has a unique combination of id and idempotency_key
- For existing databases with data, you may need to populate idempotency_key values for old records before running the migration
