# Form Prefill Fixes - Field Mapping Reference

## Borrower Form Fixes

### Database Columns (Actual):
- `id` - Primary key
- `first_name` - String
- `last_name` - String  
- `phone` - String, nullable
- `email` - String, nullable
- `address` - String, nullable
- `status` - String (active|inactive)
- `notes` - Text, nullable
- `profile_photo_path` - String, nullable
- `created_at`, `updated_at` - Timestamps

### Form Input Names & Prefill Mapping:
| Input Name | Type | Database Column | Alpine Model | Prefill Method |
|-----------|------|-----------------|--------------|----------------|
| first_name | text | first_name | firstName | old('first_name', $borrower->first_name ?? '') |
| last_name | text | last_name | lastName | old('last_name', $borrower->last_name ?? '') |
| phone | text | phone | phone | old('phone', $borrower->phone ?? '') |
| email | email | email | email | old('email', $borrower->email ?? '') |
| address | text | address | address | old('address', $borrower->address ?? '') |
| status | select | status | status | old('status', $borrower->status ?? 'active') |
| notes | textarea | notes | notes | old('notes', $borrower->notes ?? '') |
| profile_photo | file | profile_photo_path | N/A | Separate photo preview display |

### Files Changed:
1. **resources/views/borrowers/_form.blade.php**
   - Added `x-model="address"` to address input (line 30)
   - Added `x-model="notes"` to notes textarea (line 54)

2. **resources/views/borrowers/edit.blade.php**
   - Simplified x-data initialization to use model values directly instead of old() (lines 14-20)
   - Removed unnecessary Carbon parsing since edit page is not a form submission

---

## Loan Form Fixes

### Database Columns (Actual):
- `id` - Primary key
- `borrower_id` - Foreign key
- `user_id` - Foreign key
- `reference_no` - String, unique
- `principal_amount` - Decimal(12,2)
- `interest_rate` - Decimal(5,2) - percentage
- `term_months` - Unsigned integer
- `start_date` - Date
- `first_due_date` - Date, nullable
- `due_date` - Date
- `frequency` - String (daily|weekly|semi-monthly|monthly)
- `penalty_per_missed_due` - Decimal(12,2)
- `total_payable` - Decimal(12,2)
- `total_paid` - Decimal(12,2)
- `status` - String (pending|active|paid|overdue|cancelled)
- `remarks` - Text, nullable
- `notes` - Text, nullable
- `created_at`, `updated_at` - Timestamps

### Form Input Names & Prefill Mapping:
| Input Name | Type | Database Column | Alpine Model | Prefill Method |
|-----------|------|-----------------|--------------|----------------|
| borrower_id | select | borrower_id | N/A | old('borrower_id', $loan->borrower_id ?? null) |
| start_date | date | start_date | startDate | old('start_date', $loan->start_date->format('Y-m-d')) |
| first_due_date | date | first_due_date | firstDueDate | old('first_due_date', $loan->first_due_date->format('Y-m-d')) |
| principal_amount | number | principal_amount | principal | old('principal_amount', $loan->principal_amount ?? '') |
| interest_rate | number | interest_rate | interestRate | old('interest_rate', $loan->interest_rate ?? '0') |
| term_months | number | term_months | termMonths | old('term_months', $loan->term_months ?? '1') |
| frequency | select | frequency | frequency | old('frequency', $loan->frequency ?? 'monthly') |
| penalty_per_missed_due | number | penalty_per_missed_due | penaltyPerMissedDue | old('penalty_per_missed_due', $loan->penalty_per_missed_due ?? '0.00') |
| remarks | textarea | remarks | N/A | old('remarks', $loan->remarks ?? '') |
| notes | textarea | notes | N/A | old('notes', $loan->notes ?? '') |

### Files Changed:
1. **resources/views/loans/_form.blade.php**
   - Simplified date formatting in start_date input (line 20)
   - Simplified date formatting in first_due_date input (line 26)
   - Added `remarks` textarea field (lines 67-71)
   - `notes` textarea field already present (lines 73-76)

2. **resources/views/loans/edit.blade.php**
   - Fixed x-data initialization with proper type casting (lines 13-21)
   - Changed to use model values directly with type casting instead of old()
   - Ensured dates are formatted as Y-m-d strings
   - Ensured numeric values are parsed to float/int

3. **app/Http/Controllers/LoanController.php**
   - Updated store() validation to include `remarks` field (line 90)
   - Updated update() validation to include `remarks` field (line 170)
   - Both methods now validate and accept remarks in addition to notes

---

## Blade Template Rules Applied

### All Inputs:
✓ Have name attributes matching database/form field names
✓ Use `old('field_name', $model->column_name ?? '')` for fallback
✓ Have x-model bindings for Alpine.js state management
✓ Date inputs formatted as YYYY-MM-DD

### Date Inputs:
✓ Use `.format('Y-m-d')` to ensure proper formatting
✓ Handle nullable dates with ternary operator
✓ Fallback to empty string if null

### Select Inputs:
✓ Use `old('field_name', $model->column ?? 'default')` in @php block
✓ Use `@selected()` Blade directive to mark selected option
✓ Properly cast string comparisons

### File Inputs:
✓ Not prefilled (HTML spec - cannot set file input value)
✓ Show current file preview separately if exists
✓ Existing files retained if no new file uploaded

---

## Controller Rules Applied

### Validation:
✓ All form input names match validation rule keys
✓ All database columns mentioned in form have corresponding validation rules
✓ Nullable fields use `['nullable', 'string']` pattern
✓ Required fields explicitly marked `['required', ...]`

### Update Logic:
✓ Uses mass assignment with $model->update($validated)
✓ Only validated fields are updated
✓ Existing values not overwritten with null unless explicitly cleared
✓ Photo deletion only occurs if new photo uploaded (lines 142-147 in BorrowerController)

---

## Test Verification Checklist

### Borrower Edit Tests:
- [ ] Open existing borrower details page
- [ ] Click "Edit" button
- [ ] Verify all fields are prefilled:
  - [ ] First name shows correct value
  - [ ] Last name shows correct value
  - [ ] Phone shows correct value (or empty if null)
  - [ ] Email shows correct value (or empty if null)
  - [ ] Address shows correct value (or empty if null)
  - [ ] Status shows correct option selected
  - [ ] Notes shows correct value (or empty if null)
  - [ ] Photo preview shows if existing photo
- [ ] Edit one field (e.g., phone)
- [ ] Submit form
- [ ] Verify only changed field updated, others unchanged

### Loan Edit Tests:
- [ ] Open existing loan details page
- [ ] Click "Edit" button
- [ ] Verify all fields are prefilled:
  - [ ] Borrower shows correct selection
  - [ ] Release Date shows as YYYY-MM-DD
  - [ ] First Due Date shows as YYYY-MM-DD
  - [ ] Principal shows correct decimal value
  - [ ] Interest Rate shows correct percentage
  - [ ] Terms shows correct number
  - [ ] Frequency shows correct selection
  - [ ] Penalty per Missed Due shows correct value
  - [ ] Remarks shows correct value (or empty if null)
  - [ ] Notes shows correct value (or empty if null)
- [ ] Edit one field (e.g., interest rate)
- [ ] Submit form
- [ ] Verify calculation preview updates correctly
- [ ] Verify only changed field updated, others unchanged

### Validation Tests:
- [ ] Leave required fields empty and submit - should show validation errors
- [ ] Try to use invalid email format - should show validation error
- [ ] Try to upload non-image file as profile photo - should show validation error
- [ ] Enter negative principal - should show validation error
