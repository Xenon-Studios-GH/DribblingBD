# Order Lifecycle Overhaul + System Controller

## Overview
Complete the order-to-payment lifecycle with proper advance payment tracking, a packing confirmation step, finance category management, and a centralized System Controller page for operations.

## Order Lifecycle (New)
```
Created (pending/on_hold/out_of_stock)
  │
  ├──► on_hold (admin confirms)
  │     ├── Record advanced_payment → FinanceTransaction (income, "Advanced Payment")
  │     └── Order is active
  │
  ├──► packed (admin marks packed)
  │     ├── Stock deducted immediately
  │     ├── packing_confirmed_at = null
  │     └── Order appears on Packed Pending page
  │           ├── Confirm ✅ → packing_confirmed_at = now(), stays packed
  │           └── Reject ❌ → stock returned, order back to on_hold
  │
  ├──► picked → delivered
  │     └── PendingOrderTransaction created with full breakdown
  │         (advance already recorded; remaining: product, DTF, patch)
  │
  ├──► return → stock returned
  └──► refund → terminal
```

## Changes

### 1. Packed Pending Page
- **Route:** `GET /controlPanel/orders/packed-pending`  
- **Controller:** `PackingConfirmationController` (new)
- **DB columns on `orders`:** `packing_confirmed_at` (datetime, nullable), `packing_confirmed_by` (FK→users, nullable)
- Shows orders where `status = 'packed'` AND `packing_confirmed_at IS NULL`
- Actions: Confirm (sets timestamps, order stays packed) / Reject (stockIn for all products, status → on_hold)

### 2. Advanced Payment Recording
- **Trigger:** Only on MANUAL admin action (OrderStatusController, OrderEditController, OrderCreateController). NOT on StockCheckService auto-restoration.
- **DB column on `orders`:** `advance_recorded_at` (datetime, nullable)
- Creates FinanceTransaction for `advanced_payment` amount with category "Advanced Payment"
- Guard: only records once (`advance_recorded_at IS NULL`)

### 3. Finance Category Mapping (replaces hardcoded IDs)
Current code uses `category_id = 1, 2, 3` hardcoded in `PendingOrderTransactionController::confirm()`.
- Store which category is used for what purpose via SiteSetting keys:
  - `finance_category_advanced_payment`
  - `finance_category_product_sales`
  - `finance_category_dtf_sales`
  - `finance_category_patch_sales`
- Each stores the `finance_categories.id` value
- System Controller page has dropdowns to select which category maps to each purpose

### 4. System Controller Page
- **Route:** `GET /controlPanel/system-controller`
- **Route prefix:** `system-controller.*`
- **3 tabs:**

  **Tab 1: Finance Categories** — Full CRUD for `finance_categories` table (name, type, description, is_active) + mapping dropdowns (which category is used for Advanced Payment, Product Sales, DTF Sales, Patch Sales)

  **Tab 2: Fixed Amounts** — Editable fields for: DTF fee, Patch quantity, Dhaka rate, Outside rate, Free threshold, Patch name query. Saved to `SiteSetting`.

  **Tab 3: Monitor** — Order counts by status, pending packed count, pending payment count, stock alerts, recent work logs

### 5. Pending Payment Display Update
Pending Orders page now shows an "Advanced Paid" column with the advance amount and subtracts it from the displayed remaining total.

### 6. System Controller Menu Link
Add a prominent link in the admin sidebar navigation to the System Controller page.

## New Files
| File | Purpose |
|------|---------|
| `app/Http/Controllers/Admin/SystemController.php` | System Controller page (3 tabs) |
| `app/Http/Controllers/Admin/PackingConfirmationController.php` | Packed pending page CRUD |
| `resources/views/system-controller/index.blade.php` | System Controller view |
| `resources/views/orders/packed.blade.php` | Packed pending view |

## Modified Files
| File | Changes |
|------|---------|
| `app/Models/Order.php` | Add `packing_confirmed_at`, `packing_confirmed_by`, `advance_recorded_at` to fillable/casts |
| `app/Http/Controllers/Admin/BaseOrderController.php` | On status → on_hold: record advance payment |
| `app/Http/Controllers/Admin/OrderStatusController.php` | After status → packed: return view packing page ref; after status → on_hold: trigger advance recording |
| `app/Http/Controllers/Admin/OrderEditController.php` | Same advance recording check |
| `app/Http/Controllers/Admin/Finance/PendingOrderTransactionController.php` | Use SiteSetting for category mapping instead of hardcoded IDs |
| `app/Http/Controllers/Admin/Website/CustomizationController.php` | Pass system amounts to settings (or keep all in SystemController) |
| `resources/views/finance/pending-orders/index.blade.php` | Show advance as paid column |
| `resources/views/components/layouts/app.blade.php` | Add System Controller nav link |

## Migration
`2026_06_24_update_orders_for_packing_advance.php`
- Add `packing_confirmed_at` (datetime, nullable)
- Add `packing_confirmed_by` (bigint FK→users, nullable)
- Add `advance_recorded_at` (datetime, nullable)
- For existing `packed` orders: set `packing_confirmed_at = updated_at` to skip the new pending page
- Seed "Advanced Payment" finance category (income) if not exists

## Data Flow
```
Order → on_hold
  ├── advance_recorded_at IS NULL?
  ├── Yes → FinanceTransaction.create(income, "Advanced Payment", advance_amount)
  └── Set advance_recorded_at = now()

Order → packed (by admin)
  ├── StockService::stockOut() for each product
  ├── Status = 'packed', packing_confirmed_at = null
  └── Appears on /controlPanel/orders/packed-pending

Packing Confirmed:
  ├── packing_confirmed_at = now()
  └── Order proceeds through picked → delivered

Packing Rejected:
  ├── StockService::stockIn() for each product
  └── Status = 'on_hold'

Order → delivered:
  ├── PendingOrderTransaction created (existing logic)
  └── Pending page shows advance as already-paid credit

Pending Payment Confirmed:
  ├── FinanceTransaction for product_sales (using mapped category)
  ├── FinanceTransaction for dtf_sales (using mapped category)
  ├── FinanceTransaction for patch_sales (using mapped category)
  └── Status = 'confirmed'
```

## Future Considerations
- Queue driver is `sync` — advance recording and stock operations are synchronous. No issues.
- If queue driver changes in future, advance recording and stock operations should be wrapped in queued jobs.
