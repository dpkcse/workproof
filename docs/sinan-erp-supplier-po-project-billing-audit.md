# Sinan ERP Supplier Purchase and Project Billing Audit Plan

## Audit boundary and repository finding

This repository does not currently contain the described Sinan ERP Node.js/Express, EJS, or MySQL implementation. The visible application is a Laravel WorkProof codebase with billing-oriented models and migrations, and no purchase order, GRN, inventory stock, supplier bill, supplier payment, project invoice, EJS view, or Express route modules were found in the repository tree. Therefore, this document is an implementation-safe audit blueprint and fix plan for the requested Sinan ERP flows, rather than a direct code patch against missing modules.

All recommendations below are additive, idempotent, and backward-compatible. No destructive database operation is proposed. Existing voucher, inventory, project, CRM, accounting, supplier, and customer flows must remain authoritative where already implemented.

## 1. Current flow map

### 1.1 Existing supplier PO flow

No Supplier Purchase Order flow is present in this repository. The target flow to verify or implement in the Sinan ERP codebase is:

1. Project or Store Material Requisition is created.
2. Requisition is approved.
3. Approved requisition lines are converted partially or fully into one or more Supplier POs.
4. Controlled Direct PO can also be created without requisition, with an explicit source flag and reason.
5. PO is approved.
6. GRN is created from approved PO.
7. GRN posts inventory movement exactly once.
8. Supplier Bill is created from PO and GRN with 3-way matching.
9. Supplier Bill posts AP liability exactly once.
10. Supplier Payment partially or fully settles AP.
11. Supplier ledger and payables aging reflect bill, payment, and balance.

### 1.2 Existing project invoice flow

No Project Invoice flow matching the requested Node/EJS ERP is present in this repository. The target flow to verify or implement in the Sinan ERP codebase is:

1. Project is linked to a customer and contract value where applicable.
2. Project Details exposes Create Invoice and Invoice tab actions.
3. Invoice draft pulls project, customer, billing, payment term, previous billing, contract value, and BOQ/work item context.
4. Invoice supports advance, progress, milestone, final, retention, and retention release types.
5. Invoice is approved and posted to Accounts Receivable exactly once.
6. Client receipt settles receivable partially or fully.
7. Customer ledger, project ledger, receivables aging, and project profitability include the posted revenue and receipts.

## 2. Gap analysis

### 2.1 Supplier PO and credit purchase

#### Working

Cannot be confirmed from this repository because relevant Node.js/Express/EJS/MySQL modules are absent.

#### Partially working

Cannot be confirmed. In the Sinan ERP codebase, partial implementation is likely if PO, GRN, or supplier bill tables exist but lack source tracking, approval controls, idempotent voucher references, or quantity balance columns.

#### Missing or must be verified

- PO source classification: `requisition`, `direct`, `manual`, `quotation`.
- Nullable requisition reference on PO header and line level.
- Direct PO reason or remarks.
- Searchable supplier and item selectors.
- Project, store, and cost center dimensions on PO.
- Payment type, due days, due date, payment terms, and expected delivery date.
- Full lifecycle statuses for PO, receipt, billing, payment, close, and cancellation.
- Partial conversion from requisition to multiple supplier POs.
- Over-conversion prevention.
- GRN restriction for unapproved, cancelled, or closed POs.
- Idempotent inventory movement creation.
- 3-way matching among PO, GRN, and supplier bill.
- Duplicate supplier invoice number prevention per supplier.
- Idempotent AP voucher creation.
- Supplier payment duplicate voucher prevention.
- Trace references among PO, GRN, supplier bill, supplier payment, and voucher.

#### Broken or high-risk conditions

- Creating GRN from unapproved PO can create unauthorized stock and liability exposure.
- Refreshing or resubmitting GRN without idempotency can duplicate stock movement.
- Billing more than received can overstate inventory, expense, or AP.
- Duplicate supplier invoice numbers can duplicate liabilities.
- Hardcoded account IDs can post to wrong ledgers after chart-of-accounts changes.
- Editing posted accounting entries silently can break audit trails.
- Missing period-lock validation can post into closed fiscal periods.

### 2.2 Project invoice and billing

#### Working

Cannot be confirmed from this repository because the requested Sinan ERP project billing implementation is absent.

#### Partially working

Cannot be confirmed. In the Sinan ERP codebase, partial implementation is likely if generic invoices exist but do not enforce project/customer prerequisites, project value overbilling controls, retention, advance adjustment, or project profitability integration.

#### Missing or must be verified

- Project-to-customer prerequisite before posting.
- Contract value and billing term visibility.
- Project invoice tab/action on Project Details.
- Invoice types: advance, progress, milestone, final, retention, retention release.
- Previous/current/cumulative/remaining billing calculations.
- BOQ line-wise progress billing if BOQ exists.
- Manual progress percentage or manual billing when BOQ does not exist.
- Advance adjustment and retention accounting.
- Idempotent AR voucher creation.
- Customer receipt duplicate voucher prevention.
- Project ledger and profitability revenue integration.
- Receivables aging inclusion for posted unpaid project invoices.

#### Broken or high-risk conditions

- Posting invoices for projects without customers creates orphan receivables.
- Overbilling contract value without explicit variation control can overstate revenue.
- Hardcoded revenue, VAT, receivable, or advance account IDs can corrupt financial statements.
- Editing posted invoices without reversal can break statutory audit trails.
- Missing project invoice revenue in profitability reports understates project margin.

## 3. Database audit and additive migration plan

### 3.1 Tables to inspect in the Sinan ERP database

Supplier purchase flow tables:

- `purchase_requisitions`
- `purchase_requisition_items`
- `purchase_orders`
- `purchase_order_items`
- `goods_receipt_notes` or `grns`
- `goods_receipt_note_items` or `grn_items`
- `supplier_bills` or `supplier_invoices`
- `supplier_bill_items`
- `supplier_payments`
- `suppliers`
- `items` or `products`
- `inventory_stock`
- `inventory_movements`
- `vouchers`
- `voucher_entries`
- `account_mappings`
- `accounting_periods` or `fiscal_periods`

Project billing flow tables:

- `projects`
- `customers`
- `project_invoices`
- `project_invoice_items`
- `customer_receipts` or `payments`
- `project_boq_items`
- `project_ledger` if present
- `customer_ledger` if present
- `vouchers`
- `voucher_entries`
- `account_mappings`
- `accounting_periods` or `fiscal_periods`

### 3.2 Missing supplier PO columns and indexes to add if absent

Use `INFORMATION_SCHEMA` guards in the real MySQL migration before every `ALTER TABLE`.

- `purchase_orders.po_source VARCHAR(30) NOT NULL DEFAULT 'manual'`
- `purchase_orders.source_requisition_id BIGINT NULL`
- `purchase_orders.direct_po_reason TEXT NULL`
- `purchase_orders.project_id BIGINT NULL`
- `purchase_orders.store_id BIGINT NULL`
- `purchase_orders.cost_center_id BIGINT NULL`
- `purchase_orders.payment_type VARCHAR(30) NULL`
- `purchase_orders.payment_terms TEXT NULL`
- `purchase_orders.due_days INT NULL`
- `purchase_orders.due_date DATE NULL`
- `purchase_orders.expected_delivery_date DATE NULL`
- `purchase_orders.approval_status VARCHAR(30) NOT NULL DEFAULT 'draft'`
- `purchase_orders.receipt_status VARCHAR(30) NOT NULL DEFAULT 'not_received'`
- `purchase_orders.billing_status VARCHAR(30) NOT NULL DEFAULT 'not_billed'`
- `purchase_orders.payment_status VARCHAR(30) NOT NULL DEFAULT 'unpaid'`
- `purchase_orders.approved_by BIGINT NULL`
- `purchase_orders.approved_at DATETIME NULL`
- `purchase_orders.closed_at DATETIME NULL`
- `purchase_orders.cancelled_at DATETIME NULL`
- `purchase_order_items.source_requisition_item_id BIGINT NULL`
- `purchase_order_items.ordered_qty DECIMAL(18,4) NULL`
- `purchase_order_items.received_qty DECIMAL(18,4) NOT NULL DEFAULT 0`
- `purchase_order_items.billed_qty DECIMAL(18,4) NOT NULL DEFAULT 0`
- `purchase_requisition_items.converted_qty DECIMAL(18,4) NOT NULL DEFAULT 0`
- `supplier_bills.po_id BIGINT NULL`
- `supplier_bills.grn_id BIGINT NULL`
- `supplier_bills.voucher_id BIGINT NULL`
- `supplier_bills.posted_at DATETIME NULL`
- `supplier_payments.voucher_id BIGINT NULL`
- `inventory_movements.po_id BIGINT NULL`
- `inventory_movements.grn_id BIGINT NULL`
- `inventory_movements.supplier_id BIGINT NULL`

Recommended indexes:

- `purchase_orders(po_source, source_requisition_id)`
- `purchase_orders(supplier_id, approval_status)`
- `purchase_orders(project_id, store_id, cost_center_id)`
- `purchase_order_items(source_requisition_item_id)`
- `supplier_bills(supplier_id, supplier_invoice_no)` unique where supported by existing data cleanup policy; otherwise enforce in application first and add a non-unique index until duplicates are resolved.
- `supplier_bills(po_id, grn_id)`
- `supplier_payments(supplier_id, voucher_id)`
- `inventory_movements(grn_id, item_id)` unique idempotency key where one movement per GRN line is the system rule.

### 3.3 Missing project invoice columns and indexes to add if absent

- `projects.customer_id BIGINT NULL`
- `projects.contract_value DECIMAL(18,2) NULL`
- `projects.billing_terms TEXT NULL`
- `projects.payment_terms TEXT NULL`
- `projects.retention_percent DECIMAL(8,4) NULL`
- `project_invoices.invoice_type VARCHAR(30) NOT NULL DEFAULT 'progress'`
- `project_invoices.project_id BIGINT NOT NULL`
- `project_invoices.customer_id BIGINT NOT NULL`
- `project_invoices.invoice_date DATE NOT NULL`
- `project_invoices.due_date DATE NULL`
- `project_invoices.status VARCHAR(30) NOT NULL DEFAULT 'draft'`
- `project_invoices.previous_billed_amount DECIMAL(18,2) NOT NULL DEFAULT 0`
- `project_invoices.current_bill_amount DECIMAL(18,2) NOT NULL DEFAULT 0`
- `project_invoices.cumulative_billed_amount DECIMAL(18,2) NOT NULL DEFAULT 0`
- `project_invoices.retention_percent DECIMAL(8,4) NOT NULL DEFAULT 0`
- `project_invoices.retention_amount DECIMAL(18,2) NOT NULL DEFAULT 0`
- `project_invoices.advance_adjustment_amount DECIMAL(18,2) NOT NULL DEFAULT 0`
- `project_invoices.voucher_id BIGINT NULL`
- `project_invoices.posted_at DATETIME NULL`
- `project_invoice_items.boq_item_id BIGINT NULL`
- `project_invoice_items.description TEXT NULL`
- `project_invoice_items.qty DECIMAL(18,4) NOT NULL DEFAULT 1`
- `project_invoice_items.unit VARCHAR(50) NULL`
- `project_invoice_items.rate DECIMAL(18,4) NOT NULL DEFAULT 0`
- `project_invoice_items.tax_amount DECIMAL(18,2) NOT NULL DEFAULT 0`
- `project_invoice_items.discount_amount DECIMAL(18,2) NOT NULL DEFAULT 0`
- `project_invoice_items.amount DECIMAL(18,2) NOT NULL DEFAULT 0`
- `customer_receipts.project_invoice_id BIGINT NULL`
- `customer_receipts.voucher_id BIGINT NULL`

Recommended indexes:

- `project_invoices(project_id, status)`
- `project_invoices(customer_id, status, due_date)`
- `project_invoices(voucher_id)`
- `project_invoice_items(project_invoice_id, boq_item_id)`
- `customer_receipts(customer_id, project_invoice_id)`

### 3.4 Idempotent MySQL migration pattern

The live Sinan ERP migration should use this non-destructive pattern for each column and index:

```sql
SET @db_name := DATABASE();

SET @column_exists := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @db_name
    AND TABLE_NAME = 'purchase_orders'
    AND COLUMN_NAME = 'po_source'
);

SET @sql := IF(
  @column_exists = 0,
  'ALTER TABLE purchase_orders ADD COLUMN po_source VARCHAR(30) NOT NULL DEFAULT ''manual''',
  'SELECT ''purchase_orders.po_source already exists'''
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
```

Use the same pattern for every additive column. For indexes, query `INFORMATION_SCHEMA.STATISTICS` first. Never drop, rename, truncate, or rewrite existing business data.

## 4. API and route audit

### 4.1 Supplier PO routes to verify or add

- `GET /purchase/orders` list with supplier, date, total, receipt status, billing status, payment status filters.
- `GET /purchase/orders/new` direct PO form.
- `GET /purchase/requisitions/:id/create-po` requisition-based PO form.
- `POST /purchase/orders` create draft or pending approval PO.
- `GET /purchase/orders/:id` PO details.
- `PUT /purchase/orders/:id` edit draft PO only, or create approved revision with audit log.
- `POST /purchase/orders/:id/submit-approval` submit approval.
- `POST /purchase/orders/:id/approve` approve PO.
- `POST /purchase/orders/:id/cancel` cancel PO if not received/billed.
- `POST /purchase/orders/:id/close` close PO.
- `GET /purchase/orders/:id/print` print/PDF.
- `GET /api/purchase/requisitions/:id/available-lines` approved and unconverted balances only.
- `GET /api/purchase/orders/:id/receivable-lines` approved and open balances only.
- `POST /purchase/grns` create GRN from PO with idempotency key.
- `POST /purchase/supplier-bills` create bill from PO/GRN.
- `POST /purchase/supplier-bills/:id/post` post AP voucher once.
- `POST /purchase/supplier-payments` create payment.
- `POST /purchase/supplier-payments/:id/post` post payment voucher once.

All JSON APIs should return empty arrays or explicit validation errors for expected empty states. They should not throw 500 for missing optional project, store, cost center, BOQ, prior invoice, or ledger rows.

### 4.2 Project invoice routes to verify or add

- `GET /projects/:id/invoices` project invoice tab/list.
- `GET /projects/:id/invoices/new` create invoice from project.
- `GET /project-invoices` invoice list with project, customer, date range, status, due, unpaid, and paid filters.
- `POST /project-invoices` create draft.
- `GET /project-invoices/:id` details.
- `PUT /project-invoices/:id` edit draft only.
- `POST /project-invoices/:id/approve` approve invoice.
- `POST /project-invoices/:id/post` post AR voucher once.
- `POST /project-invoices/:id/reverse` reverse posted invoice through controlled reversal if needed.
- `GET /project-invoices/:id/print` print/PDF.
- `POST /customer-receipts` receive payment.
- `POST /customer-receipts/:id/post` post receipt voucher once.
- `GET /api/projects/:id/billing-context` customer, project value, terms, previous billing, BOQ, warnings.
- `GET /api/project-invoices/:id/ledger` invoice ledger details.

## 5. Frontend audit

### 5.1 Supplier PO views and scripts to verify or add

- PO list view with supplier, PO date, expected delivery date, total, approval status, receipt status, billing status, payment status, and quick actions.
- PO form with searchable supplier and item selectors.
- Separate visible paths for Direct PO and Create PO from Requisition.
- Direct PO form must require `direct_po_reason` unless a privileged setting permits blank reason.
- Requisition conversion form must show ordered, converted, remaining, and requested quantities per line.
- GRN form must show ordered quantity, already received quantity, balance quantity, current receiving quantity, project/store destination, and stock movement warning.
- Supplier bill form must show PO quantity/rate, GRN received quantity, previously billed quantity, bill quantity/rate, tax, freight, discount, and match warnings.
- Buttons should use non-inline event listeners where practical and submit forms with CSRF protection.

### 5.2 Project invoice views and scripts to verify or add

- Project Details Invoice tab with Create Invoice, View Invoices, View Ledger, and warning cards.
- Project invoice form with project selector that auto-loads customer, billing address, payment terms, previous invoice summary, contract value, and BOQ/work items.
- Invoice type selector for advance, progress, milestone, final, retention, and retention release.
- Progress billing calculator showing previous billed, current bill, cumulative billed, and remaining balance.
- Retention and advance adjustment fields with calculated totals.
- Draft, approve/post, receive payment, print, and view ledger quick actions.
- Warning messages for no linked customer, missing contract value, invoice exceeding project value, locked accounting period, and missing account mapping.

## 6. Accounting integration audit

### 6.1 Supplier bill posting

Expected credit purchase posting:

- Debit Inventory, Purchase, or Project Expense based on item type and destination.
- Debit VAT Input if applicable.
- Debit Freight/landing cost if policy maps freight separately.
- Credit Discount Received or reduce expense/inventory if discount is configured that way.
- Credit Supplier Payable.

Controls:

- Resolve accounts through the centralized account mapping resolver only.
- Validate fiscal year and accounting period before posting.
- Store `voucher_id` and `posted_at` on supplier bill.
- If `voucher_id` already exists, return the existing voucher reference and do not repost.
- Never silently edit posted voucher rows; use reversal or adjustment vouchers.

### 6.2 Supplier payment posting

Expected posting:

- Debit Supplier Payable.
- Credit Bank, Cash, or MFS account selected by payment method.

Controls:

- Support partial payment and remaining payable balance.
- Store `voucher_id` on payment.
- Block duplicate voucher creation.
- Respect period lock and voucher numbering rules.

### 6.3 Project invoice posting

Expected posting:

- Debit Customer Receivable.
- Credit Project Revenue or Service Income.
- Credit VAT Output if applicable.
- Credit Retention Payable or retention-specific account only if the local accounting policy treats retention that way; otherwise debit retention receivable and reduce trade receivable presentation through mapped accounts.

Controls:

- Block posting when project lacks customer.
- Block or warn when contract value is missing depending on billing type.
- Block overbilling beyond contract value unless variation/extra-work setting allows it.
- Store `voucher_id` and `posted_at` on project invoice.
- Prevent duplicate posting.

### 6.4 Client receipt posting

Expected posting:

- Debit Bank, Cash, or MFS account selected by payment method.
- Credit Customer Receivable.

Advance adjustment, if implemented:

- Debit Client Advance.
- Credit Customer Receivable or the mapped invoice adjustment account according to the existing account mapping resolver.

## 7. Inventory integration audit

- GRN must call the existing stock service or inventory movement service instead of directly mutating `inventory_stock` in controller code.
- Each GRN line should create one inventory movement with PO, GRN, supplier, item, quantity, rate, store, project, and cost center references.
- Use a durable idempotency key such as `GRN:{grn_id}:LINE:{grn_line_id}` to prevent duplicate stock movement on browser refresh, retries, or double submission.
- Store-first flow: received material increases store stock, then later issue/transfer moves material to project.
- Direct-to-project flow: received material should either create project stock/consumption movement or project expense according to existing business policy. It must still preserve PO, GRN, supplier, and project references.
- Weighted average cost, if currently used, must be recalculated only by the centralized stock service and not by ad hoc controller math.

## 8. Safe implementation sequence

1. Inventory existing tables, columns, indexes, routes, controllers, services, views, and account mapping keys.
2. Add idempotent schema columns and non-unique indexes first.
3. Add account mapping keys for supplier payable, purchase/inventory/project expense, VAT input, customer receivable, project revenue, VAT output, client advance, retention receivable, and bank/cash/MFS only through existing resolver configuration.
4. Implement PO source fields and direct PO reason validation.
5. Implement requisition conversion balance logic and over-conversion validation.
6. Implement PO approval gates before GRN.
7. Implement GRN idempotent inventory movement through stock service.
8. Implement supplier bill 3-way match and duplicate supplier invoice check.
9. Implement idempotent AP posting and supplier payment posting.
10. Implement project invoice prerequisites and billing context API.
11. Implement project invoice types, progress billing, advance adjustment, and retention fields.
12. Implement idempotent AR invoice posting and receipt posting.
13. Wire ledgers, aging reports, and profitability reports.
14. Add UI warnings, filters, quick actions, and print/PDF templates.
15. Run reconciliation test checklist before production deployment.

## 9. Test checklist

### 9.1 Supplier purchase tests

- Direct credit PO: create direct PO with `po_source = direct`, reason, credit terms, supplier, item, project/store, and expected delivery date.
- Requisition-based PO: approve requisition and convert selected lines into a PO.
- Partial requisition conversion: convert part of a line and confirm remaining quantity.
- Multi-supplier requisition conversion: convert different lines or quantities to different suppliers without over-conversion.
- Rejected/cancelled requisition conversion blocked.
- Unapproved PO GRN blocked unless an explicit system setting allows it.
- Cancelled/closed PO GRN blocked.
- Partial GRN: receive less than ordered and confirm PO status becomes partially received.
- Full GRN: receive balance and confirm PO status becomes received.
- GRN refresh/resubmit: confirm no duplicate stock movement.
- Supplier Bill from GRN: bill less than or equal to received quantity.
- Supplier Bill over-received quantity blocked unless setting allows it.
- Duplicate supplier invoice number for same supplier blocked.
- Supplier Bill posting creates one AP voucher only.
- Reposting supplier bill returns existing voucher and does not duplicate.
- Partial Supplier Payment reduces payable and leaves open balance.
- Full Supplier Payment closes payable.
- Duplicate payment voucher prevention.
- Supplier ledger reconciles bill, payment, and balance.
- Payables aging includes unpaid posted bills.
- Period locked supplier bill/payment posting blocked.

### 9.2 Project billing tests

- Project invoice without customer: show clear warning and block posting.
- Link existing customer from project warning path and create invoice.
- Create customer safely from project warning path and create invoice.
- Project invoice with linked customer auto-loads customer and billing data.
- Progress invoice calculates previous, current, cumulative, and remaining amounts.
- Progress invoice blocks overbilling beyond contract value unless variation setting allows it.
- BOQ progress billing bills line-wise progress and prevents line overbilling.
- Manual progress billing works when no BOQ exists.
- Advance invoice posts receivable and revenue or advance liability according to configured accounting policy.
- Advance adjustment reduces receivable or uses mapped adjustment account.
- Retention invoice calculates retention amount.
- Retention release posts using mapped retention accounts.
- Invoice posting creates one AR voucher only.
- Reposting invoice returns existing voucher and does not duplicate.
- Client receipt partially settles invoice.
- Client receipt fully settles invoice and marks paid.
- Period locked invoice/receipt posting blocked.
- Customer ledger reconciles invoice, receipt, and balance.
- Project ledger shows revenue, cost, payment, and margin.
- Receivables aging includes posted unpaid invoices.
- Project profitability includes project invoice revenue.
- Invoice print/PDF shows project, customer, invoice, payment, tax, retention, advance adjustment, and totals.

## 10. Final implementation summary for this repository change

### Changed files

- Added this audit and implementation plan document at `docs/sinan-erp-supplier-po-project-billing-audit.md`.

### Database changes

- No database migration was applied in this repository because the requested Sinan ERP Node.js/Express/MySQL modules and schemas are not present here.
- The document provides additive, idempotent MySQL migration patterns and column/index recommendations for the real Sinan ERP codebase.

### New or updated routes

- No executable routes were changed in this repository.
- The document lists safe route/API additions and route mismatch checks for the real Sinan ERP codebase.

### New or updated UI behavior

- No executable UI was changed in this repository.
- The document lists PO, GRN, supplier bill, project invoice, receipt, print/PDF, warning, filter, and quick-action UI changes for the real Sinan ERP codebase.

### Risks

- The actual Sinan ERP implementation may use different table names, service names, route naming, voucher design, stock service behavior, or account mapping conventions.
- Existing live data may include duplicates that prevent immediate unique index creation; enforce duplicates in application code first, then add unique indexes after cleanup approval.
- Retention accounting differs by jurisdiction and company policy; account mapping must be validated by finance before posting rules are enabled.

### Rollback notes

- This repository change is documentation-only and can be rolled back by removing `docs/sinan-erp-supplier-po-project-billing-audit.md`.
- For the real Sinan ERP database, rollback should disable new routes/features and leave additive nullable columns in place rather than dropping them from live production tables.
