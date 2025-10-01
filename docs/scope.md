# ITC Stock Management System — Phase 1: Scope & Requirements

**Tech:** Laravel 12 + Filament · MySQL · Docker  
**Access:** Spatie Roles + Policies  
**UI:** One Filament Admin Panel (internal)  
**Main Rules:** Branch-scoped · Stock Ledger as truth · No Negative Stock · Pay-Before-Deliver  

---

## 1) Project Description  
👉 *(What the project is about)*  
A system to manage stock across multiple branches with one central HQ.  

- **Super Admin (HQ):** manages global product catalog and central stock.  
- **Admins (Branch Managers):** handle branch stock, process distributor orders.  
- **Distributors:** customers who place orders from branches.  
- Every stock movement is saved in the **Stock Ledger** (full history).  
- **Stock Levels** show the current balance per branch × product.  

*Example: HQ buys 1,000 Shampoo → sends 300 to Phnom Penh branch → distributor Lucky Mart orders 50 → all steps logged in ledger.*  

---

## 2) Goals & Success Criteria  

### Goals  
👉 *(What we want to achieve)*  
- Always know the real stock in each branch.  
- Control purchases (IN), sales (OUT), and transfers.  
- Enforce rules: **No Negative Stock** and **Pay-Before-Deliver**.  
- Give managers clear reports and dashboards.  

### Success Criteria  
👉 *(How we check if goals are achieved)*  
- Ledger records every IN/OUT/ADJUST/TRANSFER.  
- Reports always match ledger totals (no mismatch).  
- Role-based access enforced (HQ sees all, Admins see branch only).  
- Reports/dashboards load quickly (<2s).  

*Example: If stock is 100 Shampoo and 50 are sold, ledger shows -50, stock levels = 50. Report also shows 50 left. That’s success.*  

---

## 3) Non-Negotiables (Must-Follow Rules)  
👉 *(Hard rules that cannot be broken)*  
- Ledger = append-only (never edited/deleted, only new rows).  
- Stock cannot go negative.  
- Orders must be paid before delivery.  
- Branch scoping: Admins see only their branch; HQ sees all.  
- Only HQ manages the product catalog.  
- All documents (PO, SO, Transfer, Count, Adjustment) have unique numbers.  

*Example: If branch tries to deliver an order of 200 Shampoo but stock = 150, system blocks it (no negative stock).*  

---

## 4) Roles & Access  
👉 *(Who uses the system and what they can do)*  

- **Super Admin (HQ):** full control of branches, users, products, suppliers, transfers, and global reports.  
- **Admin (Branch Manager):** manage branch stock, purchases, sales, distributors, and branch reports.  
- **Distributor (Customer):** place and view their own orders only.  

*Example: Distributor Lucky Mart can only see their own orders; Admin Phnom Penh can see only Phnom Penh stock; Super Admin can see everything.*  

---

## 5) Main Features  
👉 *(The main functions of the system)*  

- **Master Data:** Branches, Users/Roles, Distributors, Suppliers, Products, Categories  
- **Purchasing:** Create PO → approve → receive → payments  
- **Sales:** Create SO → confirm → pay → deliver → invoice  
- **Transfers:** HQ ↔ Branch, Branch ↔ Branch  
- **Stock Controls:** Stock Counts, Stock Adjustments/Returns  
- **Finance Lite:** Payments linked to sales/purchases  
- **Core Tables:** Stock Ledger (history), Stock Levels (current balance)  
- **Governance:** Audit Log, Settings  
- **Reports & Dashboards:** inventory value, sales/purchase summaries, top sellers, low stock alerts, adjustments, transfers, fulfillment KPIs  

*Example: HQ creates Purchase Order from Unilever → receives stock → later transfers to branch → branch sells to distributor → all logged.*  

---

## 6) Non-Functional Requirements  
👉 *(How the system should behave, not features)*  

- **Security:** Safe login, CSRF, password hashing, rate limits.  
- **Reliability:** All stock operations in database transactions.  
- **Performance:** Indexed queries `(branch_id, product_id)` to keep reports fast.  
- **Audit:** Log all CRUD actions.  
- **Time/Locale:** Use Asia/Phnom_Penh; store UTC in DB.  
- **Backups:** Daily database backups.  

*Example: If two Admins try to update stock at once, DB transactions keep data consistent (no double deduction).*  

---

## 7) Reports & KPIs (Explained)  

1. **Inventory Valuation** → shows total stock value (`qty × cost`).  
   - Example: 200 Shampoo @ $2 = $400.  

2. **Sales & Purchase Summaries** → overview of sales (OUT) and purchases (IN).  
   - Example: August sales = $3,700; purchases = $3,500.  

3. **Top Sellers** → most popular products by quantity or revenue.  
   - Example: Shampoo = 1,000 pcs (quantity), Face Cream = $6,000 (revenue).  

4. **Low Stock Alerts** → warn when stock < min_stock.  
   - Example: Shampoo min = 50, current = 40 → alert.  

5. **Adjustment Variance** → mismatch between system stock and actual count.  
   - Example: System = 100, counted = 95 → variance -5.  

6. **Transfer History** → track stock moved HQ ↔ Branch.  
   - Example: TR#101 HQ → Phnom Penh, 300 Shampoo.  

7. **Order Fulfillment Rate** → % of orders delivered fully & on time.  
   - Example: 18/20 orders = 90%.  

---

## 8) Out of Scope  
👉 *(What this project will not include)*  

### 1. Retail POS / barcode scanning  
Not a shop cashier system. No scanning products like in a supermarket.  
- *Example:* At AEON supermarket, a cashier scans each item’s barcode at checkout. Our system won’t do that. Instead, Lucky Mart (a distributor) orders in bulk like “50 Shampoo” → no barcode scanning.  

### 2. Promotions/discounts  
No special pricing (no “Buy 1 Get 1 Free” or 10% off rules).  
- *Example:* In retail, a shop may say “Buy 2 Cream, get 1 free.” Our system won’t support this. Lucky Mart will always buy at the normal fixed price (e.g., $5 each), no automatic discounts.  

### 3. Multi-currency  
Only one currency (e.g., USD). No conversion between USD, KHR, EUR.  
- *Example:* If HQ buys 1,000 Shampoo from Unilever, it’s always recorded in USD (like $2 each). If someone asks to pay in Khmer Riel (KHR), system won’t convert — it only records USD.  

### 4. Public customer portal  
Not a public online shop. Only registered users (HQ, Admins, Distributors) can log in.  
- *Example:* A normal customer (like you or me) cannot go to the website and order Shampoo online. Only registered distributors (e.g., Lucky Mart) can log in to place orders. It’s **B2B only, not e-commerce like Shopee or Lazada.**  


## 9) Assumptions & Risks  
👉 *(What we assume is true, and possible risks)*  

### ✅ Assumptions  
*(Things we believe are true when building the system — if they’re wrong, system may not work as expected)*  

1. **One company**  
   - We assume this system is for a single company only.  
   - *Example:* Company = ITC Distribution Co., with many branches. Not for 10 different companies.  

2. **Many branches**  
   - We assume the company has multiple branches (Phnom Penh, Siem Reap, Battambang, etc.).  
   - *Example:* HQ can send stock to Phnom Penh branch, Siem Reap branch, etc.  

3. **One product catalog**  
   - We assume all branches use the same product list (set by HQ).  
   - *Example:* Shampoo in Phnom Penh = Shampoo in Siem Reap, not two different definitions.  

4. **Stable internet**  
   - We assume users always have a good internet connection to use the system (since it’s web-based).  
   - *Example:* Branch Admin in Siem Reap needs internet to log in and record a sale.  

---

### ⚠️ Risks  
*(Things that might go wrong and cause problems when using the system)*  

1. **Staff training required**  
   - Risk: Branch staff may not know how to use the system properly.  
   - *Example:* An Admin forgets to record a transfer, so the stock report becomes wrong.  

2. **Different branch workflows**  
   - Risk: Branches may have different ways of working, which may not match the system rules.  
   - *Example:* Phnom Penh branch delivers goods before payment, but the system enforces Pay-Before-Deliver → conflict.  

3. **Migrating old stock data**  
   - Risk: Importing historical stock data from old records (Excel, paper) may cause errors.  
   - *Example:* Old stock says 200 Shampoo, but real stock is 150 → mismatch when system starts.*  


## 10) Acceptance Checklist  
👉 *(Final checklist before Phase 1 is approved)*  

- [ ] Roles & permissions agreed  
- [ ] Non-negotiables accepted  
- [ ] Features approved  
- [ ] Reports/KPIs confirmed  
- [ ] Out-of-scope acknowledged  

---
