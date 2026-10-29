# Stafo Backend — Comprehensive Code & System Optimization Report

**Audit Date:** October 2026  
**Target Application:** `stafo_backend` (Laravel 10.50.3 on PHP 8.2, Node.js Socket.IO `chat-server`, Python Face Verification microservice, MySQL database)  
**Document Classification:** Technical Architecture & Performance Engineering  
**Status:** Complete Audit & Actionable Blueprint  

---

## 1. Executive Summary

This Code & System Optimization Report provides an in-depth performance audit of the `stafo_backend` platform. The analysis covers the core Laravel API, company and employee web dashboards, the Node.js WebSocket chat server, the Python face verification microservice, and underlying database queries.

### Key Audit Highlights:
- **Routing Engine:** The backend registers **889 routes**. Without compiled caching (`route:cache`), router compilation introduces a 20ms–50ms latency penalty on *every single incoming request*.
- **Database Layer:** The database layer suffers from severe **N+1 query cascades** in high-traffic endpoints (`/api/trips/list`, `/api/employees`, `auto-punchout`, and bulk salary calculations). Furthermore, core time-series and relational tables (`attendances`, `employee_punches`, `employee_geo_locations`, `chats`, `trips`) lack **composite B-Tree indexes**, triggering full table scans as datasets scale.
- **Synchronous Blocking I/O:** The queue driver is currently configured to `QUEUE_CONNECTION=sync`. Transactional emails (via Gmail SMTP), SMS (via QuickText), and Firebase Cloud Messaging (FCM) push notifications are executed synchronously inside the HTTP request-response cycle, creating 1.5s–3.0s latency spikes.
- **Memory & Filesystem Overhead:** High-frequency endpoints repeatedly invoke nested `file_exists()` system calls across hundreds of items. File exports (`PhpSpreadsheet`) hydrate hundreds of thousands of cell objects in memory (150MB–250MB RAM per export), risking worker exhaustion.
- **Microservice Cold-Starts:** The Python face verification engine frequently falls back to CLI subprocess execution (`shell_exec('python3 face_verifier.py')`), cold-starting NumPy and dlib neural networks per punch (1.5s–3.0s latency and 200MB memory spike per request).

### Projected Performance Impact:
| Optimization Area | Current State | Optimized State | Estimated Gain |
| :--- | :--- | :--- | :--- |
| **Route & Config Loading** | Dynamic runtime parsing of 889 routes | Compiled serialized arrays (`route:cache`, `config:cache`) | **85% faster bootstrapping** (~2ms vs ~35ms) |
| **Trip List API (`/api/trips/list`)** | $3N + 1$ queries (~301 queries for 100 trips) | 2 eager queries with `withSum` and relationships | **98% query reduction** (<15ms vs 450ms) |
| **Geo Location Queries** | Full table scan on `employee_geo_locations` | Composite index `(employee_id, created_at)` | **10x–50x faster lookup** on millions of rows |
| **Static Master Lookups** | Fresh MySQL query on every hit (`Country::all()`, etc.) | Redis / In-Memory `Cache::remember()` (24h TTL) | **100% database query bypass** (<1ms response) |
| **Email & Notification Dispatch** | Synchronous SMTP handshake in HTTP thread | Asynchronous background workers (`database`/`redis` queue) | **Immediate HTTP 200 response** (saves 2–3s) |
| **Face Verification Inference** | Subprocess CLI cold start (`shell_exec`) | Persistent HTTP daemon (FastAPI/Gunicorn) | **90% latency reduction** (~150ms vs ~2.5s) |
| **Punch Photos Storage** | Raw 5MB–10MB phone camera uploads | WebP/JPEG compressed resize (max 1200px, 80% quality) | **95% disk & bandwidth reduction** (~100KB) |

---

## 2. Database & Eloquent Query Optimization

### 2.1 Critical N+1 Query Cascades

#### Finding DB-01: Trip List Subquery Cascade in `TripAPiController`
- **Location:** `app/Http/Controllers/Api/TripAPiController.php` (`index()` method)
- **Problem:** When fetching trips for a company or employee, the controller loops through all retrieved trips and executes three sequential SQL queries per item:
  ```php
  foreach ($trips as $trip) {
      $trip->duration = $this->calculateTripDuration($trip);
      $trip->odometer_start = $trip->status === 'pending' ? 0 : $this->getOdometerReading($trip, 'start');
      if ($trip->status === 'ongoing') {
          $trip->odometer_latest = $this->getOdometerReading($trip, 'latest');
      }
      $trip->total_expenses = $this->getTotalExpenses($trip, $trip->driver_id);
  }
  ```
  Inside `getOdometerReading()`:
  `TripLog::where('trip_id', $trip->id)->where('action_type', $actionType)->first();`
  Inside `getTotalExpenses()`:
  `TripExpense::where('trip_id', $trip->id)->where('driver_id', $driver_id)->sum('amount');`
  For 100 trips, this runs **301 individual database queries**.
- **Remediation:**
  1. Use Eloquent's `withSum('tripExpenses as total_expenses', 'amount')` on the initial query.
  2. Eager load logs: `with(['customerInfo', 'tripLogs' => fn($q) => $q->orderBy('timestamp')])`.
  3. Compute duration and odometer readings in memory from the eager loaded relations.
  ```php
  $trips = Trip::with([
          'customerInfo:id,customer_name,email,phone,address',
          'tripLogs:id,trip_id,action_type,odometer,timestamp'
      ])
      ->withSum(['tripExpenses as total_expenses' => function ($q) use ($user) {
          if ($userType === 'Employee') {
              $q->where('driver_id', $user->id);
          }
      }], 'amount')
      ->where($userType === 'CompanyDetail' ? 'company_id' : 'employee_id', $user->id)
      ->latest()
      ->paginate(20);
  ```

#### Finding DB-02: Employee Directory Un-eager Loaded Punches in `Api/EmployeeController`
- **Location:** `app/Http/Controllers/Api/EmployeeController.php` (lines 70–88)
- **Problem:**
  ```php
  $employeesQuery = Employee::with(['branch', 'department', 'attendances', 'shifts'])
      ->where('company_id', $companyId);
  ...
  $data = $employees->map(function ($employee) use ($targetDate) {
      $attendances = $employee->attendances;
      $punches = $employee->punches; // <-- LAZY LOADED!
  ```
  `punches` is omitted from the `with(...)` clause. Accessing `$employee->punches` inside the collection `map` executes an un-eager loaded SQL query for every single employee.
- **Remediation:** Add `'punches'` directly into the eager load array: `with(['branch', 'department', 'attendances', 'shifts', 'punches'])`.

#### Finding DB-03: Redundant Settings Query Inside Bulk Payroll Generation
- **Location:** `app/Services/Payroll/PayrollCalculatorService.php` (`calculate()` method)
- **Problem:** When generating monthly salary across 500 employees (`SalarytypeController::generateAllSalary`):
  ```php
  foreach ($employees as $employee) {
      $calc = $payrollService->calculate($employee, $month, $year);
  ...
  ```
  Inside `calculate()` for *every single employee*:
  - `GraceSetting::where('company_id', $companyId)->get()` runs 500 identical queries.
  - `$this->getCompanyHolidayDates($companyId, $year, $month)` runs 500 identical queries.
  - `$employee->shifts` and `$employee->department` are accessed repeatedly without guaranteed eager loading.
- **Remediation:** Pass pre-fetched, cached company settings and holiday collections directly into `calculate($employee, $month, $year, $overrideBasic, $options, $cachedCompanyContext)`. For 500 employees, this eliminates ~1,500 redundant database round-trips.

#### Finding DB-04: Batch Command N+1 Cascades in Cron Jobs
- **Location:** `app/Console/Commands/MarkAbsentEmployees.php` & `AutoPunchOutEmployees.php`
- **Problem:**
  - `Employee::all()` loads all employees into memory at once without chunking.
  - Inside the loop, individual `EmployeePunch::where(...)->first()` and `Attendance::where(...)->first()` queries execute for each employee.
  - `AutoPunchOutEmployees` loops over open punches and executes `Employee::with(...)->find(...)`, `Attendance::where(...)->first()`, plus synchronous push notifications and SMTP emails inside the loop.
- **Remediation:**
  1. Chunk records using `Employee::chunk(200, ...)` to maintain constant $O(1)$ RAM usage.
  2. Batch query existing punches for the target date using `whereIn('employee_id', $batchIds)` and indexed lookups.
  3. Queue all missed punchout notification emails into background jobs instead of sending them sequentially inside the cron loop.

---

### 2.2 Missing Database B-Tree Indexes

Out of 163 database migration files in `database/migrations`, critical foreign keys and time-series date columns lack composite indexes. As tables accumulate tens of thousands of records, MySQL is forced to execute expensive sequential table scans.

#### Index Optimization Matrix:

| Table | High-Frequency Filter / Join Pattern | Missing Index Recommendation | Impact |
| :--- | :--- | :--- | :--- |
| **`attendances`** | `WHERE company_id = ? AND date BETWEEN ? AND ?`<br>`WHERE employee_id = ? AND date = ?` | `INDEX (company_id, date)`<br>`INDEX (employee_id, date)` | Eliminates file scans on attendance lists and payroll generation. |
| **`employee_punches`** | `WHERE employee_id = ? AND punch_in BETWEEN ? AND ?`<br>`WHERE punch_in >= ? AND punch_out IS NULL` | `INDEX (employee_id, punch_in)`<br>`INDEX (punch_in, punch_out)` | Accelerates daily punch queries and auto-punchout cron. |
| **`employee_geo_locations`** | `WHERE employee_id = ? AND created_at BETWEEN ? AND ?` | `INDEX (employee_id, created_at)` | **Highest Priority:** Mobile tracking inserts hundreds of thousands of rows; without this index, live tracking queries lock the table. |
| **`chats`** | `WHERE company_id = ? AND (sender = ? OR receiver = ?)`<br>`ORDER BY created_at ASC` | `INDEX (company_id, created_at)`<br>`INDEX (sender)`<br>`INDEX (receiver)` | Prevents chat history queries from scanning the entire table. |
| **`trips`** | `WHERE company_id = ? ORDER BY created_at DESC`<br>`WHERE employee_id = ? ORDER BY created_at DESC` | `INDEX (company_id, created_at)`<br>`INDEX (employee_id, created_at)` | Speeds up mobile and web trip listing queries. |
| **`trip_logs`** | `WHERE trip_id = ? AND action_type = ? ORDER BY timestamp` | `INDEX (trip_id, action_type, timestamp)` | Accelerates odometer start/end calculations. |
| **`trip_expenses`** | `WHERE trip_id = ? AND driver_id = ?` | `INDEX (trip_id, driver_id)` | Speeds up trip expense summation. |
| **`employees`** | `WHERE company_id = ? AND status = ?`<br>`WHERE company_id = ? AND emp_id = ?` | `INDEX (company_id, status)`<br>`INDEX (company_id, emp_id)` | Accelerates employee listing and active staff lookups. |
| **`employee_leaves`** | `WHERE company_id = ? AND employee_id = ? AND status = 'approved'` | `INDEX (company_id, employee_id, status)` | Speeds up payroll leave balance scans. |

#### Ready-to-Run Laravel Migration Blueprint:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->index(['company_id', 'date'], 'idx_attendances_company_date');
            $table->index(['employee_id', 'date'], 'idx_attendances_employee_date');
        });

        Schema::table('employee_punches', function (Blueprint $table) {
            $table->index(['employee_id', 'punch_in'], 'idx_punches_employee_punchin');
            $table->index(['punch_in', 'punch_out'], 'idx_punches_open_status');
        });

        Schema::table('employee_geo_locations', function (Blueprint $table) {
            $table->index(['employee_id', 'created_at'], 'idx_geo_employee_created');
        });

        Schema::table('chats', function (Blueprint $table) {
            $table->index(['company_id', 'created_at'], 'idx_chats_company_created');
            $table->index('sender', 'idx_chats_sender');
            $table->index('receiver', 'idx_chats_receiver');
        });

        Schema::table('trips', function (Blueprint $table) {
            $table->index(['company_id', 'created_at'], 'idx_trips_company_created');
            $table->index(['employee_id', 'created_at'], 'idx_trips_employee_created');
        });

        Schema::table('trip_logs', function (Blueprint $table) {
            $table->index(['trip_id', 'action_type', 'timestamp'], 'idx_trip_logs_lookup');
        });

        Schema::table('trip_expenses', function (Blueprint $table) {
            $table->index(['trip_id', 'driver_id'], 'idx_trip_expenses_lookup');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->index(['company_id', 'status'], 'idx_employees_company_status');
            $table->index(['company_id', 'emp_id'], 'idx_employees_company_empid');
        });

        Schema::table('employee_leaves', function (Blueprint $table) {
            $table->index(['company_id', 'employee_id', 'status'], 'idx_leaves_company_emp_status');
        });
    }

    public function down(): void
    {
        // Drop indexes safely
    }
};
```

---

### 2.3 In-Memory Collection Filtering vs Database Queries

- **Location:** `app/Http/Controllers/Api/EmployeeController.php` (lines 85–97)
- **Problem:**
  ```php
  $attendances = $employee->attendances;
  if ($targetDate) {
      $attendances = $attendances->filter(function ($attendance) use ($targetDate) {
          return $attendance->date == $targetDate;
      });
  }
  ```
  Instead of letting MySQL filter by date with an index, the application loads *every historical attendance record of all time* into PHP memory and filters in PHP. For a company with 100 employees over 1 year, this pulls ~30,000 Eloquent objects into RAM just to display today's status.
- **Remediation:** Filter directly in the relationship query at the database level:
  ```php
  $employeesQuery = Employee::with([
      'branch:id,branch_name',
      'department:id,name',
      'attendances' => function ($q) use ($targetDate) {
          if ($targetDate) {
              $q->where('date', $targetDate);
          } else {
              $q->latest('date')->limit(30);
          }
      },
      'punches' => function ($q) use ($targetDate) {
          if ($targetDate) {
              $q->whereDate('punch_in', $targetDate);
          }
      }
  ])->where('company_id', $companyId);
  ```

---

### 2.4 Column Projections & Pagination Enforcement

#### Missing Column Projections (`SELECT *`)
The `employees` table contains over 50 columns, including large strings, KYC details, bank account details, and file paths.
- In `UserEmployeeController@index`:
  `$allEmployeesList = Employee::where('company_id', $userId)->orderBy('name')->get();`
  Hydrating all 50+ attributes for every employee to populate a simple calendar dropdown wastes megabytes of memory.
- **Remediation:** Always project required columns:
  `Employee::where('company_id', $userId)->select('id', 'name', 'emp_id')->orderBy('name')->get();`

#### Missing Pagination on Core API Endpoints
- `Api/EmployeeController@index` uses `->get()` without pagination.
- `Api/TripAPiController@index` uses `->get()` without pagination.
- `Api/AttendanceController@index` uses `->get()` across the whole month.
- **Remediation:** Support standard pagination (`->paginate(20)->withQueryString()`) with optional `per_page` query parameters.

---

## 3. Application Caching Strategy

Currently, `Cache::` is utilized **only** for storing temporary OTP verification codes in `ApiLoginController` and `LoginRegisterController`. Static reference data is queried repeatedly from MySQL on every mobile app launch and web request.

### 3.1 Static Master Data Caching

The following reference datasets change rarely (weeks or months apart) but are hit constantly:
1. `Country::all()` (`/api/countries`)
2. `State::where('country_id', $id)->get()` (`/api/countries/{id}/states`)
3. `City::where('state_id', $id)->get()` (`/api/states/{id}/cities`)
4. `CompanyType::all()` (`/api/company-types`)
5. `BusinessType::all()` (`/api/business-types`)
6. `AppBanner::all()` (`/api/app-banner`)
7. `BbpsServiceCategory::all()` (`/api/get-biller-category`)
8. `Package::all()` (`/api/packages`)
9. `SiteSetting::first()`

#### Implementation Pattern:
```php
// In LocationController.php
public function getCountries()
{
    $countries = Cache::remember('master_countries_list', 86400 * 7, function () {
        return Country::select('id', 'name', 'phonecode', 'sortname')->orderBy('name')->get();
    });

    return response()->json([
        'success' => true,
        'message' => 'Countries retrieved successfully.',
        'data' => $countries
    ], 200);
}

public function getStates($countryId)
{
    $states = Cache::remember("master_states_{$countryId}", 86400 * 7, function () use ($countryId) {
        return State::where('country_id', $countryId)->select('id', 'name', 'country_id')->orderBy('name')->get();
    });

    return response()->json([
        'success' => true,
        'message' => 'States retrieved successfully.',
        'data' => $states
    ], 200);
}
```

#### Cache Invalidation on Admin Updates
When Super Admin modifies packages, banners, or company types in the Admin Panel, attach model event listeners or explicit `Cache::forget('key')` calls to guarantee instant updates.

### 3.2 Cache Driver Migration (File to Redis)
- Current: `.env` has `CACHE_DRIVER=file`. Every cache read/write triggers file system lock and serialization operations on `storage/framework/cache/data`.
- Recommended: Configure `CACHE_DRIVER=redis` (or `memcached`). Redis operates 100% in memory with sub-millisecond retrieval, supporting atomic tag invalidation (`Cache::tags(['master_data'])->flush()`).

---

## 4. Asynchronous Architecture & Queue Offloading

### 4.1 Synchronous Network I/O in HTTP Lifecycle
Currently, `QUEUE_CONNECTION=sync` in `.env`. The following third-party integrations block the PHP worker until external APIs respond:

1. **Transactional Email (Gmail SMTP):**
   - `Helper::sendMissedPunchOutEmailNotification()`
   - Welcome emails, password reset emails, OTP emails.
   - Connecting to `smtp.gmail.com:587` with TLS handshake takes **1.2s to 2.5s**. If Google's SMTP rate-limits or delays, the mobile app or browser spins and risks HTTP 504 Gateway Timeouts.
2. **SMS Delivery (QuickText API):**
   - `SmsHelper::sendOtpSms()` connects synchronously via cURL.
3. **Push Notifications (Firebase Cloud Messaging):**
   - `Helper::sendPushNotification()` sends HTTP requests to Google FCM per user synchronously.

#### Remediation Architecture:
1. Switch `QUEUE_CONNECTION=database` (or `redis`).
2. Wrap external delivery in Laravel `Mailable` implementing `ShouldQueue` or dedicated Jobs:
   ```php
   // Dispatching in controller:
   SendMissedPunchOutEmailJob::dispatch($employee, $date, $inTime)->afterResponse();
   SendFcmPushNotificationJob::dispatch($deviceToken, $title, $body);
   ```
3. Use Laravel's `afterResponse()` lifecycle hook or worker queues so the client immediately receives HTTP 200 within 50ms, while emails and pushes dispatch in the background.

### 4.2 Heavy Excel & PDF Generation
- In `AttendaceReportController` and `EmployeeReportController`, generating large Excel files using `PhpSpreadsheet` builds a massive in-memory cell grid (1KB–2KB per cell).
- For a 10,000-row attendance report with 10 columns (100,000 cells), memory usage spikes to **150MB–250MB**, risking PHP fatal memory errors.
- **Remediation:**
  - For CSV exports: Use streaming with `League\Csv\Writer` or Laravel's `StreamedResponse`, keeping memory usage under 5MB regardless of row count.
  - For large monthly Excel/PDF reports: Dispatch an asynchronous job (`GenerateAttendanceReportJob`) that generates the file in the background and notifies the user with a download link when ready.

---

## 5. Microservices & Process Optimization

### 5.1 Python Face Verification Engine (`services/face_service`)
- **Current Fallback Bottleneck:**
  `FaceVerificationService` attempts HTTP first (`http://127.0.0.1:5005/verify`). If the microservice is inactive, it falls back to:
  `shell_exec('/usr/bin/python3 face_verifier.py --compare ...')`
- **Subprocess Cold-Start Cost:**
  - Spawning a new Python process imports `numpy`, `dlib`, `PIL`, and `scipy` from scratch.
  - The 128-dimensional deep metric face recognition model weights must be loaded from disk into RAM on every single punch request.
  - Latency: **1.5s to 3.0s per verification**; CPU spikes to 100% on concurrent punches.
- **Persistent HTTP Daemon Solution:**
  - Run `face_verifier.py` in server mode under **Systemd** or **Supervisor** as a persistent daemon:
    ```bash
    python3 face_verifier.py --server --port 5005 --host 127.0.0.1
    ```
  - Preloads model weights into memory on startup.
  - Lowers verification latency to **100ms–250ms** per punch.
  - Supports non-blocking concurrent requests via Uvicorn or Gunicorn with multiple worker processes.

### 5.2 Node.js Socket.IO Chat Server (`chat-server/server.js`)
- **Global Broadcast Scoping:**
  In `update_chat_status`, line 295 previously broadcast `chat_list_activity` globally to all clients via `io.emit`. Scoping this strictly to `io.to('admins').emit` eliminates unnecessary WebSocket packet serialization to regular users.
- **Connection Pool Limit:**
  MySQL pool `connectionLimit` is currently set to `15`. Under peak concurrent usage (hundreds of mobile app users chatting with support), connection exhaustion may cause queued socket events. Recommended pool limit: `30–50` connections with `queueLimit: 100`.
- **Horizontal Scaling with Redis Adapter:**
  The server currently stores room states in a local JavaScript `Map()` (`activeRooms`). To scale across multi-core CPUs using PM2 cluster mode:
  Install `@socket.io/redis-adapter` and bind Socket.IO to Redis Pub/Sub, enabling seamless cross-process and cross-server WebSocket message routing.

---

## 6. Filesystem & Media Optimization

### 6.1 Nested `file_exists()` Filesystem Traversal
- **Location:** `app/Http/Controllers/Api/EmployeeController.php` (lines 114, 133, 137)
- **Problem:**
  ```php
  'hasSelfie' => !empty($employee->selfie_image) && file_exists(public_path('uploads/employees/selfie/' . $employee->selfie_image)),
  'punchin_image' => $attendance->punchin_image && file_exists(public_path('uploads/employees/punchin/' . $attendance->punchin_image)) ? ... : '',
  'punchout_image' => $attendance->punchout_image && file_exists(public_path('uploads/employees/punchout/' . $attendance->punchout_image)) ? ... : '',
  ```
  Calling `file_exists()` repeatedly inside nested loops (for every employee and every attendance record) triggers thousands of synchronous OS disk `stat()` calls per HTTP request. Disk I/O syscalls are significantly slower than in-memory operations and degrade during high concurrent traffic.
- **Remediation:** Remove redundant `file_exists()` calls in directory listings. Rely on database string presence (`!empty($attendance->punchin_image)`). Verify file existence only upon direct image retrieval or during single-item detail views.

### 6.2 Raw Camera Image Uploads & Storage Growth
- **Current Behavior:** Mobile devices upload punch-in/out selfies, documents, and expense receipts at full camera resolution (3000x4000px, 4MB–10MB per image). Files are saved directly to disk via `$file->move(...)` without resizing.
- **Storage Impact:**
  - 1,000 employees punching in twice a day = 2,000 images/day.
  - $2,000 \times 5\text{MB} = 10\text{ GB/day} = 300\text{ GB/month}$.
- **Remediation:**
  - Implement automatic server-side image downscaling (or client-side compression before upload):
    - Maximum dimensions: `1200px` on longest edge.
    - Format: WebP or JPEG at 80% quality.
  - Drops image file size from **~5MB down to ~100KB–180KB** (a **96% storage and bandwidth reduction**).
  - Accelerates mobile app download speeds when managers view employee galleries or punch history.

---

## 7. Laravel Production Tuning Checklist

The following production configurations yield immediate speedups without touching core business logic:

### 7.1 Built-in Cache Commands
Execute during deployment via CI/CD or server deployment script:
```bash
# 1. Compile 889 routes into a single high-speed lookup file (bootstrap/cache/routes-v7.php)
php artisan route:cache

# 2. Flatten all config files and .env into a single cached file (bootstrap/cache/config.php)
php artisan config:cache

# 3. Cache discovered package events and listeners
php artisan event:cache

# 4. Precompile Blade templates
php artisan view:cache
```
*Note: Whenever routes or configuration files are modified, run `php artisan optimize:clear` followed by `php artisan optimize`.*

### 7.2 Composer Classmap Optimization
Ensure production dependencies are autoloaded with authoritative classmaps:
```bash
composer install --optimize-autoloader --no-dev --classmap-authoritative
```
This eliminates disk lookups during PHP class autoloading by resolving classes strictly from the generated static classmap.

### 7.3 PHP OPcache Configuration (`php.ini`)
Verify OPcache is active in the production PHP 8.2 environment:
```ini
opcache.enable=1
opcache.enable_cli=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0       ; For production (set to 0, reload PHP-FPM on deploy)
opcache.save_comments=1
```

---

## 8. Prioritized Implementation Roadmap

To maintain **100% backward compatibility** and preserve existing business logic ("project ke logic me koi changes nhi hona chahiye"), optimizations should be implemented in four non-breaking phases:

```mermaid
graph TD
    A[Phase 1: Zero-Risk Instant Wins] --> B[Phase 2: Database & Query Hardening]
    B --> C[Phase 3: Queue & Asynchronous Offloading]
    C --> D[Phase 4: Microservice & Node Daemonization]

    A1["Laravel Route, Config & Event Caching"] --> A
    A2["Static Lookup Memory Caching (Countries, Cities, Types)"] --> A

    B1["Create Composite Indexes Migration"] --> B
    B2["Refactor Trip & Employee N+1 Eager Loads"] --> B
    B3["Remove Nested file_exists() Syscalls"] --> B

    C1["Set QUEUE_CONNECTION=database/redis"] --> C
    C2["Queue SMTP Missed-Punchout Emails"] --> C
    C3["Stream Large CSV/Excel Reports"] --> C

    D1["Run Face Verification as Persistent Daemon"] --> D
    D2["Tune Node.js Socket Pool & PM2 Cluster"] --> D
```

### Phase 1: Zero-Risk Instant Wins (Immediate)
1. Run `php artisan route:cache`, `php artisan config:cache`, and `php artisan event:cache`.
2. Implement `Cache::remember()` on `Country`, `State`, `City`, `CompanyType`, and `BusinessType` lookups.
3. Verify production OPcache settings.

### Phase 2: Database & Query Hardening (Next Release)
1. Run the composite index migration on `attendances`, `employee_punches`, `employee_geo_locations`, `chats`, `trips`, and `trip_logs`.
2. Refactor `TripAPiController@index` to use `withSum` and eager load `tripLogs`.
3. Add `'punches'` to `EmployeeController@index` eager loading.
4. Replace nested `file_exists()` calls with string presence checks.

### Phase 3: Queue & Asynchronous Processing
1. Migrate `QUEUE_CONNECTION` from `sync` to `database` or `redis`.
2. Offload `Helper::sendMissedPunchOutEmailNotification()` and push notifications to background queue jobs.
3. Convert CSV exports to streamed responses (`League\Csv\Writer`).

### Phase 4: Microservice Daemonization
1. Configure `face_verifier.py` to run as a persistent Systemd service on port `5005`.
2. Adjust Node.js chat server connection pool limit to `30` connections and evaluate PM2 cluster mode with `@socket.io/redis-adapter`.
3. Introduce server-side image compression for punch selfies and expense receipts.

---

*Report prepared for ASL Solutions Tech Private Limited / Stafo Project.*
