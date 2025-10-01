# ITC Stock Management System — Phase 2: System Design (Mermaid‑Safe, With Simple Explanations)

**Tech:** Laravel 12 + Filament · MySQL · Spatie Roles + Policies  
**UI:** Single Filament Admin Panel  
**Anchors:** Branch scoped · Stock Ledger as truth · No Negative Stock · Pay Before Deliver

> Mermaid‑safe version: all flowchart node labels use plain ASCII (no colons, slashes, emojis, or special symbols) so GitHub renders correctly.  
> Each flow includes a diagram **and** your simple explanation style (Steps + Example).

---

## 1) Inputs (from Phase 1)
- Roles: Super Admin, Admin (Branch Manager), Distributor  
- Modules: Branches, Users/Roles, Distributors, Products/Categories, Purchases (IN), Orders (OUT), Transfers, Stock Counts, Adjustments/Returns, Stock Ledger, Reports, Settings, Audit Log  
- Rules: Branch scoped access, No Negative Stock, Pay Before Deliver, Ledger append only  
- Location Master: Provinces, Districts

---

## 2) Entity List (Tables)
- provinces, districts, branches, users (+ Spatie tables)  
- product_categories, products, product_images  
- distributors, suppliers  
- purchase_orders, purchase_items  
- sales_orders, order_items  
- payments  
- transfers, transfer_items  
- stock_counts, stock_count_items  
- stock_adjustments  
- stock_ledger (append only)  
- stock_levels (fast cache)  
- audit_logs, settings

---

## 3) ERD (Entity Relationship Diagram)
```mermaid
erDiagram
  PROVINCES ||--o{ DISTRICTS : has
  PROVINCES ||--o{ BRANCHES : located_in
  DISTRICTS ||--o{ BRANCHES : located_in
  PROVINCES ||--o{ DISTRIBUTORS : located_in
  DISTRICTS ||--o{ DISTRIBUTORS : located_in

  BRANCHES ||--o{ USERS : has
  BRANCHES ||--o{ DISTRIBUTORS : manages
  BRANCHES ||--o{ SALES_ORDERS : has
  BRANCHES ||--o{ PURCHASE_ORDERS : has
  BRANCHES ||--o{ TRANSFERS : from_or_to
  BRANCHES ||--o{ STOCK_LEDGER : logs
  BRANCHES ||--o{ STOCK_LEVELS : aggregates

  PRODUCTS ||--o{ ORDER_ITEMS : used
  PRODUCTS ||--o{ PURCHASE_ITEMS : used
  PRODUCTS ||--o{ TRANSFER_ITEMS : moved
  PRODUCTS ||--o{ STOCK_LEDGER : logs
  PRODUCTS ||--o{ STOCK_LEVELS : aggregates
  PRODUCTS }o--|| PRODUCT_CATEGORIES : in

  SALES_ORDERS ||--o{ ORDER_ITEMS : contain
  PURCHASE_ORDERS ||--o{ PURCHASE_ITEMS : contain
  TRANSFERS ||--o{ TRANSFER_ITEMS : contain

  SALES_ORDERS ||--o{ PAYMENTS : receives
  PURCHASE_ORDERS ||--o{ PAYMENTS : pays

  STOCK_COUNTS ||--o{ STOCK_COUNT_ITEMS : contain
  STOCK_ADJUSTMENTS ||--o{ STOCK_LEDGER : posts

  USERS ||--o{ AUDIT_LOGS : acts
```
**In simple:** Branches own users, orders, transfers, and stock. Products link to order lines and stock. Ledger logs **every** movement; Stock Levels store the **current** balance per branch × product.

---

## 4) Table Sketches (key fields)
- products: id, sku, name, category_id, unit, base_price, min_stock, is_active  
- sales_orders: id, branch_id, distributor_id, so_no, status, totals, confirmed_at, fulfilled_at  
- order_items: id, sales_order_id, product_id, qty, unit_price, line_total  
- stock_ledger: id, branch_id, product_id, ref_type, ref_id, qty_in, qty_out, unit_cost, occurred_at  
- stock_levels: PK(branch_id, product_id), qty_current

---

## 5) Invariants and Policies
- No Negative Stock: check stock_levels before any OUT movement.  
- Pay Before Deliver: block delivery if not fully paid.  
- Ledger Immutable: insert only; corrections via adjustments.  
- Transfers Double Entry: OUT at source branch, IN at target branch.

---

## 6) Core Flows (15 Mermaid‑safe diagrams with your simple explanations)

### 1) Purchasing (IN)
```mermaid
flowchart LR
  A[Create PO] --> B[Approve PO]
  B --> C[Receive Items]
  C --> L[Ledger IN]
  L --> S[Update Stock]
  S --> P[Record Payment]
```
👉 When company buys from supplier.  
**Steps:** Create PO → Approve → Receive → Ledger IN → Stock increases → Payment recorded.  
**Example:** HQ orders 1,000 Shampoo from Unilever → receives them → stock at HQ goes up by 1,000.

---

### 2) Sales (OUT, Pay Before Deliver)
```mermaid
flowchart LR
  A[Create SO] --> B[Confirm SO]
  B --> C{Stock OK}
  C -- No --> A
  C -- Yes --> P[Record Payment]
  P --> D{Paid In Full}
  D -- No --> W[Wait Or Partial]
  D -- Yes --> X[Deliver Items]
  X --> L[Ledger OUT]
  L --> S[Update Stock]
```
👉 When branch sells to distributor.  
**Steps:** Create SO → Confirm → Check stock → Payment → If fully paid → Deliver → Ledger OUT → Stock decreases.  
**Example:** Lucky Mart orders 50 Shampoo from Phnom Penh branch. They pay, branch delivers, stock decreases by 50.

---

### 3) Transfers (HQ ↔ Branch)
```mermaid
flowchart LR
  A[Create Transfer] --> B[Approve Transfer]
  B --> SH[Ship From Source]
  SH --> L1[Ledger OUT Source]
  SH --> RV[Receive At Target]
  RV --> L2[Ledger IN Target]
  L1 --> S1[Update Source Stock]
  L2 --> S2[Update Target Stock]
```
👉 Move stock between locations.  
**Steps:** Create Transfer → Approve → Ship → Ledger OUT at source → Receive → Ledger IN at target.  
**Example:** HQ ships 200 Shampoo to Siem Reap branch. HQ stock goes down 200, Siem Reap stock goes up 200.

---

### 4) Stock Count (Variance)
```mermaid
flowchart LR
  A[Draft Count] --> B[Enter Qty Found]
  B --> C[Compare With System]
  C --> D{Variance Exists}
  D -- No --> E[Close Count]
  D -- Yes --> F[Post Adjustment]
  F --> L[Ledger Adjust]
  L --> S[Update Stock]
```
👉 Check physical stock vs system stock.  
**Steps:** Draft count → Count items → Compare → If mismatch → Post adjustment → Update ledger.  
**Example:** System says 100 Cream, but staff counts 95 → -5 adjustment added to ledger.

---

### 5) Stock Adjustment (Manual)
```mermaid
flowchart LR
  A[New Adjustment] --> B[Enter Reason And Qty]
  B --> AP[Approve Adjustment]
  AP --> L[Ledger Adjust]
  L --> S[Update Stock]
```
👉 Fix errors, damages, or corrections.  
**Steps:** Create adjustment → Add reason + qty → Approve → Ledger updates stock.  
**Example:** 3 Shampoo bottles are damaged → Adjustment OUT -3 → Stock reduced by 3.

---

### 6) Payments (Partial Or Full)
```mermaid
flowchart LR
  A[Order Created] --> B[Create Invoice Or Payable]
  B --> C[Record Payment]
  C --> D{Paid In Full}
  D -- No --> E[Mark Partial Balance Due]
  D -- Yes --> F[Mark Paid]
```
👉 Record money paid by distributor or paid to supplier.  
**Steps:** Order created → Payment recorded → If partial, mark balance due → If full, mark paid.  
**Example:** Lucky Mart’s order is $1,000. They first pay $600 (partial). Later pay $400 → marked paid.

---

### 7) Invoice Or Receipt Printing
```mermaid
flowchart LR
  A[Confirm Order] --> B[Generate Template]
  B --> C[Fill Party And Lines]
  C --> D[Render PDF Or Print]
  D --> E[Attach To Order]
```
👉 Generate invoice/receipt for orders.  
**Steps:** Confirm order → Generate invoice template → Fill details → Print/Export PDF → Attach to order.  
**Example:** Distributor buys 50 Shampoo. System generates invoice showing items, price, totals, and payment.

---

### 8) User And Role Management
```mermaid
flowchart LR
  A[Create User] --> B[Assign Role]
  B --> C[Role Type]
  C -- Super Admin --> D[Access All]
  C -- Admin --> E[Access Own Branch]
  C -- Distributor --> F[Access Own Orders]
```
👉 Who can log in and what they can do.  
**Steps:** Super Admin creates user → Assigns role (HQ/Admin/Distributor).  
**Example:** Super Admin adds Admin for Battambang branch → That Admin sees only Battambang data.

---

### 9) Branch Scoping Enforcement
```mermaid
flowchart LR
  A[Request Data] --> B[Check Branch Policy]
  B --> C[Allowed]
  C -- No --> D[Forbidden]
  C -- Yes --> E[Return Data]
```
👉 Admins only see their branch data.  
**Steps:** Every request checks branch_id → If not allowed, deny access.  
**Example:** Phnom Penh Admin tries to view Siem Reap stock → blocked.

---

### 10) Audit Logging
```mermaid
flowchart LR
  A[User Action] --> B[Capture Details]
  B --> C[Write Audit Log]
  C --> D[View By Super Admin]
```
👉 Track who did what in the system.  
**Steps:** User action → Capture before/after → Save audit log.  
**Example:** Admin changes distributor phone number → Old and new values saved in audit log.

---

### 11) Settings Management
```mermaid
flowchart LR
  A[Open Settings] --> B[Update Config]
  B --> C[Validate And Save]
  C --> D[Apply System Wide]
```
👉 Global system settings.  
**Steps:** Open settings → Update currency, tax, invoice prefix → Save → Apply system‑wide.  
**Example:** Change invoice number prefix from “INV-” to “ITC-” → New invoices start with ITC-.

---

### 12) Dashboards (KPIs)
```mermaid
flowchart LR
  A[Fetch KPIs] --> B[Aggregate By Branch]
  B --> C[Render Charts]
  C --> D[Drill Down]
```
👉 Quick summary at a glance.  
**Example cards:** Inventory Value, Top Sellers, Low Stock, Order Fulfillment %.  
**Example:** Dashboard shows: Stock Value = $100k, Top Seller = Shampoo, Low Stock = 3 items.

---

### 13) Reports
```mermaid
flowchart LR
  A[Choose Report And Filters] --> B[Query Ledger And Orders]
  B --> C[Aggregate And Format]
  C --> D[Render Or Export]
```
👉 Detailed data with filters and exports.  
**Steps:** Select report → Query data → Show table or export.  
**Example:** Sales report for August: Shampoo 1,000 pcs, Cream 500 pcs. Exported to CSV.

---

### 14) Purchase Returns (to Supplier)
```mermaid
flowchart LR
  A[Create Purchase Return] --> B[Approve Return]
  B --> C[Ship To Supplier]
  C --> L[Ledger OUT]
  L --> S[Update Stock]
  C --> P[Record Refund Or Credit]
```
👉 Send stock back to supplier.  
**Steps:** Create purchase return → Approve → Ship → Ledger OUT → Stock decreases.  
**Example:** 20 damaged Shampoo returned to Unilever → HQ stock goes down 20.

---

### 15) Sales Returns (Distributor → Branch)
```mermaid
flowchart LR
  A[Create Sales Return] --> B[Approve Return]
  B --> C[Receive Items]
  C --> L[Ledger IN]
  L --> S[Update Stock]
  C --> P[Refund Or Exchange]
```
👉 Distributor returns stock to branch.  
**Steps:** Create sales return → Approve → Receive → Ledger IN → Stock increases.  
**Example:** Lucky Mart returns 5 expired Shampoo → Phnom Penh branch stock goes up by 5.

---

## 7) Sequence: Pay Before Deliver
```mermaid
sequenceDiagram
  actor Admin
  participant UI as Filament
  participant API as Laravel Controller
  participant Svc as Service
  participant DB as MySQL
  Admin->>UI: Click Deliver
  UI->>API: POST deliver order
  API->>Svc: deliverOrder
  Svc->>DB: sum payments for order
  DB-->>Svc: total paid
  alt Not Enough
    Svc-->>API: error pay before deliver
    API-->>UI: show error
  else Paid
    Svc->>DB: insert ledger OUT
    Svc->>DB: update stock levels
    DB-->>Svc: ok
    Svc-->>API: success
    API-->>UI: delivered
  end
```

---

## 8) Migration Plan (order)
1. provinces, districts  
2. branches, users (+Spatie)  
3. product_categories, products  
4. distributors, suppliers  
5. purchase_orders, purchase_items  
6. sales_orders, order_items  
7. transfers, transfer_items  
8. stock_counts, stock_count_items  
9. stock_adjustments  
10. payments  
11. stock_ledger, stock_levels  
12. audit_logs, settings

---

## 9) Filament Resource Plan
- Branch (CRUD)  
- Product Category (CRUD)  
- Product (CRUD)  
- Distributor (CRUD)  
- Supplier (CRUD)  
- Purchase Order (Approve, Receive)  
- Sales Order (Confirm, Deliver)  
- Transfer (Approve, Ship, Receive)  
- Stock Count (Post Variance)  
- Stock Adjustment (Approve)  
- Stock Ledger (read only)  
- Stock Levels (read only)  
- Payments (linked to orders)  
- Audit Log (read only)

---

## 10) Exit Checklist
- [x] ERD diagram  
- [x] Table sketches  
- [x] Policies enforced  
- [x] All 15 flows with explanations  
- [x] Migration order  
- [x] Filament resources
