# StayingHotel ERP — Multi-Branch Enterprise Hotel Management System

**StayingHotel ERP** is a full-stack, production-grade Hotel Business ERP built with **Laravel 12**, **MySQL 8+**, **Tailwind CSS**, **Alpine.js**, and **Chart.js**. It powers multi-branch hotel operations with strict tenant isolation, role-based access control (RBAC), real-time cashier shift reconciliation, reservation & double-booking prevention, housekeeping turnover tracking, maintenance room blocking, expense management with segregation of duties, and organization-wide analytical reporting.

---

## 1. System Requirements & Compatibility

| Component | Required Version | Recommended / Tested Local Setup |
| :--- | :--- | :--- |
| **PHP** | 8.2.0+ or 8.3+ | PHP 8.2.12+ (bundled with XAMPP 8.2+) |
| **PHP Extensions** | `pdo_mysql`, `mbstring`, `openssl`, `bcmath`, `curl`, `ctype`, `fileinfo`, `json`, `xml`, `tokenizer` | Enabled in `php.ini` |
| **Database** | MySQL 8.0+ or MariaDB 10.4+ | XAMPP MySQL daemon |
| **Package Manager** | Composer 2.7+ | Global Composer Setup |
| **Node.js & NPM** | Node.js 18.x+ or 20.x+ | NPM 9.x+ |
| **Web Server** | Apache (XAMPP) / Nginx / Built-in CLI | `php artisan serve` or Apache VirtualHost |

---

## 2. Quick-Start Installation (XAMPP Environment)

### Step 1: Clone or Open Project
Clone the repository to your local directory (e.g. `c:\xampp\htdocs\StayERP` or `c:\StayERP`):
```bash
cd c:\StayERP
```

### Step 2: Install Composer Dependencies
Ensure your terminal uses the XAMPP PHP binary (or your system PHP 8.2+/8.3+):
```bash
composer install --no-interaction
```
*(On Windows PowerShell with XAMPP: `& "C:\xampp\php\php.exe" C:\ProgramData\ComposerSetup\bin\composer.phar install`)*

### Step 3: Create the MySQL Database in phpMyAdmin
1. Start **Apache** and **MySQL** in the **XAMPP Control Panel**.
2. Open your web browser and navigate to: [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
3. Click on **New** (left sidebar).
4. Enter Database Name: `stayinghotel_erp`.
5. Select Collation: `utf8mb4_unicode_ci`.
6. Click **Create**.

*Alternatively via command line:*
```sql
CREATE DATABASE stayinghotel_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Step 4: Configure Environment File (`.env`)
Copy `.env.example` to `.env`:
```bash
cp .env.example .env
```
Ensure your database parameters in `.env` match your local XAMPP settings:
```dotenv
APP_NAME="StayingHotel ERP"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stayinghotel_erp
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

### Step 5: Generate Application Encryption Key
```bash
php artisan key:generate
```

### Step 6: Run Migrations and Seeders
Execute the complete database migration and seed default organization branches, roles, permissions, rooms, staff accounts, reservations, and sample operations:
```bash
php artisan migrate:fresh --seed
```

### Step 7: Install and Compile Frontend Assets
```bash
npm install
npm run build
```
*(For active frontend development with Hot Module Replacement: `npm run dev`)*

### Step 8: Start the Local Development Server
```bash
php artisan serve
```
Open your browser and navigate to: **[http://127.0.0.1:8000](http://127.0.0.1:8000)**.

---

## 3. Demo Accounts & Role Credentials

All demonstration accounts are seeded with the password: **`password`**.

The login page provides **1-Click Quick Login** buttons for instant testing of all roles:

| Role | Email | Password | Scope / Permissions |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `admin@stayinghotel.com` | `password` | Global Organization Access, Branch Switching, Full Configuration |
| **Branch Manager** | `manager.dhaka@stayinghotel.com` | `password` | Dhaka Branch Oversight, Room Rates, Approvals, Staff |
| **Finance Manager** | `finance@stayinghotel.com` | `password` | Invoices, Cash Reconciliation, Expense Approvals, Refunds |
| **Cash Counter / Cashier**| `cashier.dhaka@stayinghotel.com`| `password` | Shift Float Opening, Payments, POS Invoicing, Shift Closing |
| **Receptionist** | `reception.dhaka@stayinghotel.com`| `password` | Front Desk, Walk-Ins, Check-In, Check-Out, Room Folios |
| **Housekeeping Staff** | `housekeeping.dhaka@stayinghotel.com` | `password` | Cleaning Turnovers, Inspection Marks, Room Clean Status |
| **Maintenance Staff** | `maintenance.dhaka@stayinghotel.com` | `password` | Work Orders, Room Blocking, HVAC/Plumbing Repairs |
| **Internal Auditor** | `auditor@stayinghotel.com` | `password` | Read-only Audit Trail, Ledger Inspection, Compliance Reports |

---

## 4. Demonstrating Multi-Branch Features

The system comes pre-seeded with 4 operating branches:
1. **Dhaka Premier Branch** (`DHK-01`)
2. **Chattogram Portview Branch** (`CTG-02`)
3. **Cox's Bazar Beachfront Branch** (`CXB-03`)
4. **Sylhet Tea Garden Resort** (`SYL-04`)

- **Super Admin** can view consolidated metrics or switch between individual branches using the top navigation dropdown.
- **Branch-level employees** are strictly restricted to their assigned branch. Any attempt to modify query parameters to access unauthorized branch data returns an HTTP `403 Forbidden` response.

---

## 5. Running Automated Tests

StayingHotel ERP includes comprehensive automated feature tests covering role access, branch isolation, concurrency double-booking prevention, cashier shift reconciliation, and expense segregation of duties:

```bash
php artisan test
```

### Key Test Suites:
- `Tests\Feature\AuthenticationAndRoleDashboardTest`: Verifies all 8 distinct role dashboards and blocks unauthorized role escalation.
- `Tests\Feature\BranchIsolationTest`: Verifies tenant data isolation and prevents cross-branch tampering.
- `Tests\Feature\ReservationAndBookingTest`: Tests walk-ins, date pricing snapshots, database transactions, concurrency double-booking prevention, check-in, check-out, and folio updates.
- `Tests\Feature\CashierShiftAndPaymentTest`: Tests opening float, payment processing, closing cash count, and discrepancy calculations.
- `Tests\Feature\ExpenseAndRefundWorkflowTest`: Enforces segregation of duties (preventing authors from self-approving restricted expenses) and refund authorization.

---

## 6. Creating a New Super Admin Securely

To create an additional Super Admin account via Laravel Tinker:

```bash
php artisan tinker
```
Execute the following script:
```php
$user = \App\Models\User::create([
    'organization_id' => 1,
    'name' => 'System Administrator',
    'email' => 'admin.new@stayinghotel.com',
    'password' => \Illuminate\Support\Facades\Hash::make('YourStrongPassword123!'),
    'designation' => 'Executive Director',
    'is_active' => true,
]);

$superAdminRole = \App\Models\Role::where('slug', 'super_admin')->first();
$user->roles()->attach($superAdminRole->id);

$branches = \App\Models\Branch::all();
foreach ($branches as $branch) {
    \App\Models\UserBranchAssignment::create([
        'user_id' => $user->id,
        'branch_id' => $branch->id,
        'role_id' => $superAdminRole->id,
        'is_primary' => $branch->code === 'DHK-01',
    ]);
}
```

---

## 7. Database Backup & Restore

### Backup via mysqldump (CLI):
```bash
# Windows / XAMPP Command Prompt
mysqldump -u root -p stayinghotel_erp > stayinghotel_erp_backup.sql
```

### Backup via phpMyAdmin:
1. Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
2. Select database `stayinghotel_erp`.
3. Click the **Export** tab.
4. Choose **Quick** export method and Format **SQL**.
5. Click **Export** to download `stayinghotel_erp.sql`.

### Restore via CLI:
```bash
mysql -u root -p stayinghotel_erp < stayinghotel_erp_backup.sql
```

### Restore via phpMyAdmin:
1. Open `stayinghotel_erp` in phpMyAdmin.
2. Click the **Import** tab.
3. Browse and select your `.sql` backup file.
4. Click **Import** at the bottom of the page.

---

## 8. Troubleshooting Common Errors

### 1. `SQLSTATE[HY000] [2002] Connection refused`
- **Cause**: MySQL daemon is not running in XAMPP.
- **Fix**: Open the **XAMPP Control Panel** and ensure the **MySQL** service status shows "Running" on port 3306.

### 2. `Class "PDO" not found` or `could not find driver`
- **Cause**: The `pdo_mysql` extension is disabled in `php.ini`.
- **Fix**: Open `php.ini` (located at `C:\xampp\php\php.ini`), search for `;extension=pdo_mysql`, remove the leading semicolon `;`, and restart Apache.

### 3. `mbstring` or `sqlite3` Application Control Policy Error on Windows
- **Cause**: A Windows security policy or AppLocker is blocking an unverified PHP binary in User AppData.
- **Fix**: Explicitly use the XAMPP PHP binary at `C:\xampp\php\php.exe` which is verified and includes all built-in extensions.

### 4. `419 Page Expired` on Form Submission
- **Cause**: Missing CSRF token or session expiry.
- **Fix**: Ensure `@csrf` is present in forms and run `php artisan cache:clear && php artisan config:clear`.

### 5. Vite manifest not found (`Unable to locate file in Vite manifest`)
- **Cause**: Frontend build assets haven't been compiled.
- **Fix**: Run `npm run build` to generate compiled scripts and stylesheets in `public/build/`.

---

## 9. System Architecture & Modules

StayingHotel ERP is architected around clean domain boundaries:

```
c:\StayERP\
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Domain controllers (Auth, Billing, Cashier, Operations, Reports, etc.)
│   │   └── Middleware/      # EnforceBranchContext, CheckRole, CheckPermission
│   └── Models/              # Eloquent models with business rules & decimal calculations
├── database/
│   ├── migrations/          # 8 structured relational migrations with foreign keys & indexes
│   └── seeders/             # Complete demo data seeder (branches, staff, folios, inventory)
├── resources/
│   ├── views/               # Clean Blade templates organized by domain & role dashboards
│   └── js/                  # Alpine.js, Chart.js, and Lucide Icons bundle
└── routes/
    └── web.php              # Guarded routes with granular permissions and branch policies
```

### Core Business Rules Implemented:
- **Financial Decimal Precision**: Invoices and payments calculate currency using fixed-point representation to eliminate floating-point rounding errors.
- **Audit Trails**: Critical operations (check-in, check-out, cash float open/close, expense approvals, payment collections) automatically record actor, branch, IP, and event logs.
- **Segregation of Duties**: Expense requesters cannot self-approve restricted financial requests.
- **Double Booking Guard**: Atomic database transactions check `reservations` and `reservation_rooms` using `lockForUpdate` to ensure no two guests can reserve the same room during overlapping dates.
- **Housekeeping Pipeline**: Room checkout automatically triggers dirty status and registers turnaround tasks for housekeeping teams.

---

## 10. License & Support

StayingHotel ERP is developed for enterprise multi-branch hospitality operations. Built with Laravel 12 and modern web standards.
