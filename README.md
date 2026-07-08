<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-F9322C?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS 4">
  <img src="https://img.shields.io/badge/Alpine.js-3-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white" alt="Alpine.js 3">
</p>

<h1 align="center">⚽ DriddlingBD</h1>

<p align="center">
  <strong>Sportswear E-Commerce & Inventory Management System</strong>
  <br>
  Laravel-powered platform for jersey manufacturing, sales, inventory, and multi-platform tracking
</p>

<p align="center">
  <a href="#features">Features</a> •
  <a href="#tech-stack">Tech Stack</a> •
  <a href="#screenshots">Screenshots</a> •
  <a href="#getting-started">Getting Started</a> •
  <a href="#architecture">Architecture</a>
</p>

---

## ✨ Features

### 🛍️ Shop (Customer-Facing)

- Product catalog with jersey customization
- Cart & wishlist with persistent state (localStorage + Alpine.$persist)
- Checkout with COD / bKash / Nagad options
- Customer profiles & order history

### 📦 Inventory Management

- Size-level stock tracking (S, M, L, XL, XXL)
- Stock In / Stock Out with preview-and-confirm workflow
- Pessimistic locking for race-condition-safe mutations
- Per-product analytics with 30-day trends
- Auto-generated product codes

### 🔗 Kronx Platform Integration

- Bidirectional product & stock sync with external Kronx API
- Webhook receiver with HMAC signature verification
- Delivery logging & retry logic
- Manual sync commands & admin log viewer

### 📊 Order Lifecycle

- Status machine: `pending → on_hold → packed → delivered → refund/return`
- Packing confirmation workflow
- Auto-generated sequential order numbers
- Draft order support

### 💰 Finance Tracking

- Income / expense recording with category management
- Versioned financial data
- Transaction history & reconciliation tools

### 🔍 SEO & Marketing

- Polymorphic SEO meta management with template-based generation
- JSON-LD structured data
- 301 redirect manager (database-driven, cached)
- SEO health dashboard & scoring

### 📡 Tracking & Analytics

- Meta Conversions API (CAPI) — server-side events
- Google Analytics 4 / Google Tag Manager
- Google Ads conversion tracking
- Microsoft Clarity session recording
- Tracking pixel diagnostics & health checks

### 🛡️ Security

- Role-based access control (Superadmin / Admin / Staff)
- Rate-limited login, stock, and checkout endpoints
- Login trap for brute-force mitigation
- Session-based auth with HTTP-only cookies
- CSRF protection on all state-changing routes
- Deactivated accounts blocked at middleware level

### 📋 Audit & Monitoring

- Universal WorkLog — every action is recorded
- Login attempt logs with IP & user agent
- System controller for configuration management
- System changelog (versioned config updates)
- Polling manager for real-time dashboard updates
- Automated cleanup commands (120-day retention)

---

## 🛠 Tech Stack

| Layer        | Technology                               |
| ------------ | ---------------------------------------- |
| **Backend**  | Laravel 12, PHP 8.2                      |
| **Frontend** | Blade, Alpine.js 3, Tailwind CSS 4       |
| **Database** | MySQL 8.0+                               |
| **Assets**   | Vite 6, FontAwesome 6, Chart.js          |
| **Queue**    | Database-driven (sync dev / worker prod) |
| **Cache**    | File-based                               |
| **External** | Kronx API, Meta CAPI                     |

---

## 🗺 Architecture

```
                    ┌──────────────┐
                    │   Shop (Public) │
                    │  /shop, /cart   │
                    │  /checkout, /faq│
                    └──────┬───────┘
                           │
         ┌─────────────────┼──────────────────┐
         │                 │                  │
    ┌────▼────┐    ┌──────▼───────┐    ┌─────▼──────┐
    │  Auth    │    │  Admin Panel │    │  Tracking   │
    │/login    │    │/controlPanel │    │ CAPI Bridge │
    │/register │    │  Dashboard   │    │/__tracking  │
    └─────────┘    │  Stock       │    └────────────┘
                   │  Orders      │    ┌────────────────┐
                   │  Finance     │    │  Kronx API     │
                   │  Website     │    │ (external sync)│
                   │  SEO         │    │ POST /api/     │
                   │  Kronx Logs  │    │  kronx/webhook │
                   │  Monitoring  │    └────────────────┘
                   │  System Ctrl │
                   └──────────────┘
```

**Pattern:** Monolithic Laravel with Service Layer architecture
**33 Models · 60 Migrations · 37+ Controllers · 13 Services · 10 Scheduled Commands**

---

## 🚀 Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- MySQL 8.0+ (or MariaDB)
- Node.js & NPM
- Extensions: PDO, mbstring, xml, bcmath, json

### Installation

```bash
# 1. Clone & enter
git clone https://github.com/your-username/driddlingbd.git
cd driddlingbd

# 2. Install PHP dependencies
composer install

# 3. Install & build frontend
npm install
npm run build

# 4. Environment setup
cp .env.example .env
php artisan key:generate
# Edit .env with your database credentials

# 5. Run migrations
php artisan migrate

# 6. Create admin user
php artisan tinker
> \App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('secure-password'),
    'role' => 'superadmin',
    'status' => true,
  ]);
> exit
```

### Development

```bash
npm run dev    # Vite hot-reload
php artisan serve  # Development server
```

---

## 📂 Project Structure

```
app/
├── Console/Commands/     → 10 scheduled commands
├── Enums/                → UserRole, StockSize, TransactionType, FinanceType
├── Http/Controllers/     → 37+ controllers (Admin, Shop, Api)
├── Models/               → 33 Eloquent models
├── Services/             → 13 business logic services
└── Observers/            → Product, WebsiteProject, WebsiteCategory

database/
├── migrations/           → 60 migration files
└── factories/            → 3 model factories

resources/
├── css/                  → Tailwind CSS v4
├── js/                   → Alpine.js, Chart.js, PollingManager
└── views/                → 90+ Blade templates

routes/                   → 7 route files (web, shop, seo, website, finance, tracking, console)
tests/                    → Feature tests
```

---

## 🧪 Testing

```bash
php artisan test
```

---

## 📄 License

This project is proprietary software. All rights reserved.
