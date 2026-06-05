# DribblingBD Stock Management

A Laravel 12-based inventory management system with role-based access control for apparel stock tracking (S-XXL sizes).

## Features

### Role-Based Access
| Role | Access |
|------|--------|
| **Superadmin** | Full access — Dashboard, Stock Management, Add Product, Recent Activity, Stock In/Out, Workers, Login Logs, Work Logs |
| **Admin** | Dashboard, Stock Management (all), Add Product, Recent Activity, Stock In/Out |
| **Staff** | Stock Management (view only), Stock In, Stock Out |

### Stock Management
- Product catalog with auto-generated product codes (`Dribbling-XXXX`)
- Size-level inventory tracking (S, M, L, XL, XXL)
- Stock In / Stock Out with preview-and-confirm flow
- Auto-confirm after 5-second countdown
- Per-product detail page with size breakdown and activity timeline
- 30-day analytics on each product
- Search by product name or code
- Filter by stock range and sort (newest, oldest, stock high/low)

### Audit & Logging
- **Work Logs** — Full audit trail of all system actions (login, stock changes, user management)
- **Login Logs** — Tracks all login attempts with IP address, user agent, and status
- **Recent Activity** — Stock in/out transaction history with filters
- 90-day retention on activity views; 120-day hard cleanup via scheduled command

### Security
- Role-based middleware (`role:superadmin,admin,staff`)
- Rate-limited login (5 attempts/min)
- Rate-limited stock operations (30 previews/min, 20 confirms/min)
- Forgot / Reset password flow
- Session-based authentication with "remember me" (30-day)
- CSRF protection on all state-changing requests
- Deactivated accounts blocked at middleware level

### Data Retention
- Recent Activity & Login Logs: 90-day filter
- Cleanup command: deletes records older than 120 days (runs daily)

## Requirements

- PHP 8.2+
- Composer
- MySQL 8.0+ (or MariaDB)
- Node.js & NPM (for Vite asset compilation)
- Extensions: PDO, mbstring, xml, bcmath, json

## Installation

```bash
# 1. Clone the repository
git clone https://github.com/Xenon-Studios-GH/Shock-Management.git
cd Shock-Management

# 2. Install PHP dependencies
composer install --no-dev --optimize-autoloader

# 3. Install & build frontend assets
npm install
npm run build

# 4. Environment configuration
cp .env.example .env
php artisan key:generate

# 5. Configure .env with your database
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=your_database
#    DB_USERNAME=your_user
#    DB_PASSWORD=your_password

# 6. Run migrations
php artisan migrate

# 7. Create your first user via tinker
php artisan tinker
> \App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('your-password'),
    'role' => 'superadmin',
    'status' => true,
  ]);
> exit

# 8. Serve the application
php artisan serve
```

## Default Users (Development)

All default users have password: `password`

| Email | Name | Role |
|-------|------|------|
| notmunthasir@dribblingbd.com | Munthasir Rahman | superadmin |
| irfanmahi@dribblingbd.com | Irfan Mahi | admin |
| badshah@dribblingbd.com | Badshah | admin |

## Project Structure

```
app/
├── Console/Commands/
│   └── CleanOldTransactions.php   # 120-day data cleanup
├── Http/
│   ├── Controllers/Admin/
│   │   ├── Auth/
│   │   │   ├── ForgotPasswordController.php
│   │   │   ├── LoginController.php
│   │   │   ├── LogoutController.php
│   │   │   └── ResetPasswordController.php
│   │   ├── DashboardController.php
│   │   ├── LoginLogController.php
│   │   ├── ProductController.php
│   │   ├── StockActivityController.php
│   │   ├── StockFilterController.php
│   │   ├── StockInController.php
│   │   ├── StockManagementController.php
│   │   ├── StockOutController.php
│   │   ├── StockSearchController.php
│   │   ├── WorkerController.php
│   │   └── WorkLogController.php
│   └── Middleware/
│       └── RoleMiddleware.php
├── Models/
│   ├── LoginLog.php
│   ├── Product.php
│   ├── Stock.php
│   ├── StockTransaction.php
│   ├── User.php
│   └── WorkLog.php
└── Services/
    ├── LoginLogService.php
    ├── StockService.php
    └── WorkLogService.php

resources/views/
├── auth/                          # Login & password reset views
├── components/                    # Reusable Blade components
├── dashboard/
├── login-logs/
├── products/                      # Add Product form
├── stock-activity/                # Recent Activity page
├── stock-management/              # Inventory table, detail, filters
├── stockin/                       # Stock In wizard
├── stockout/                      # Stock Out wizard
├── work-logs/
└── workers/                       # User management
```

## Schedules

The following command runs daily via Laravel's scheduler:

```bash
php artisan app:clean-old-transactions
```

Deletes `stock_transactions` and `login_logs` records older than 120 days.

## Account Deactivation

When an account is deactivated (via Workers → Deactivate):
- The user is immediately blocked from logging in
- A "deactivated" message is shown on login attempt
- The role middleware (`RoleMiddleware.php`) checks `$user->status`

## License

MIT
