# 🏬 Stock Management System (ITC Internship Project)

A full-stack web application for managing products, stock requests, transfers, sales, and payments between HQ, Admin (province), and Distributor (district).  
Built with **Laravel + Filament**, following clean architecture and role-based access control.

---

## 📑 Table of Contents
- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [System Roles](#system-roles)
- [Project Phases](#project-phases)
- [Installation](#installation)
- [Usage](#usage)
- [Screenshots](#screenshots)
- [License](#license)

---

## 🧭 Overview
This project helps manage multi-branch stock distribution from HQ to provinces and districts.  
It ensures accurate stock levels, enforces **Pay-Before-Deliver**, and prevents negative stock issues.

---

## ⚙️ Features
- 🏢 Multi-branch structure: HQ → Admin → Distributor
- 🔐 Role-based access control (Spatie)
- 📦 Stock Request → Approval → Transfer → Receive flow
- 💰 Sales & Payment with Pay-Before-Deliver rule
- 📊 Real-time stock ledger and reports
- 🧮 Adjustments and stock count management
- 🧾 Audit log and branch isolation

---

## 💻 Tech Stack
- **Backend:** Laravel 11, PHP 8+
- **Frontend (Admin):** Filament 3
- **Database:** MySQL 8
- **Auth:** Spatie Roles & Permissions
- **Environment:** Docker, Composer, NPM
- **Others:** Redis (cache), Mailpit (testing)

---

## 👥 System Roles
| Role | Permissions |
|------|--------------|
| **Super Admin** | Manage all users, branches, products, stock, and reports |
| **Admin** | Manage distributors & stock in own province |
| **Distributor** | Request stock, record sales, and payments |

---

## 🧩 Project Phases Summary

## 📘 Overview
This summarizes the 14 development phases of the Stock Management System project — from planning to deployment.

---

## 🚀 Phases

1. **Scope & Requirements** – Define roles, rules, and main stock flow (Admin requests → HQ transfers).  
2. **System Design** – Draw ERD & sequence diagrams for all main processes.  
3. **Repo & Tooling** – Set up GitHub, coding standards, and workflow automation.  
4. **Environment Setup** – Configure Docker, `.env`, and shared dev setup.  
5. **Database & Seeders** – Create all migrations and seed demo data (roles, branches, users, products).  
6. **Authentication & Roles** – Add Spatie roles & secure login per user type.  
7. **Admin UI (CRUD)** – Build Filament pages for branches, users, products, etc.  
8. **Business Engine (Ledger)** – Implement ledger logic to track IN/OUT and block negative stock.  
9. **Stock Request → Transfer** – Admin requests from HQ → HQ approves → transfer recorded.  
10. **Sales & Payments** – Distributor sells, pays, and delivers (Pay-before-deliver rule).  
11. **Stock Controls** – Add stock count and adjustment for real inventory correction.  
12. **Reports & Audit** – Generate stock, sales, and transfer reports + logs.  
13. **Governance & UX Polish** – Add branch deactivate, confirmations, and role-based UI improvements.  
14. **Deployment** – Build Docker image, run CI/CD, seed data, and deploy live.

---

## ✅ Summary
This roadmap ensures smooth progress from system planning and database design to deployment.  
Each phase builds on the previous one — making your project **organized, testable, and production-ready**.

📄 See full details in [`/docs/project_phases.md`](./docs/project_phases.md)

---

## ⚡ Installation

### Option 1 – Manual (Local)
```bash
git clone https://github.com/yourusername/stock-management.git
cd stock-management
cp .env.example .env
composer install
npm install && npm run build
php artisan key:generate
php artisan migrate --seed
php artisan serve
```
Then open: [http://localhost:8000](http://localhost:8000)

### Option 2 – Docker
```bash
docker compose up -d
```
Then open: [http://localhost:8000](http://localhost:8000)

**Default Logins:**
| Role | Email | Password |
|------|--------|-----------|
| Super Admin | superadmin@example.com | password |
| Admin | admin@example.com | password |
| Distributor | distributor@example.com | password |

---

## ▶️ Usage
- **Super Admin:** manages all provinces, branches, users, and products.  
- **Admin:** approves distributor requests and handles province-level stock.  
- **Distributor:** requests stock and records sales + payments.  

---

## 🖼️ Screenshots
*(optional – add your images later)*
```markdown
![Dashboard](./docs/screenshots/dashboard.png)
![Stock Request](./docs/screenshots/stock_request.png)
```

---

## 📚 Credits
Developed by **Sok Masterly**, Final-Year Software Engineering Student at **Institute of Technology of Cambodia (ITC)**.  
Supervisor:  Movsun Kuy
Internship Project – 2025

---

## 🪪 License
MIT License – feel free to use or modify with credit.

---

**Project:** Stock Management System  
**Author:** Sok Masterly  
**Version:** v1.0 – 2025
