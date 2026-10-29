# Stafo HRMS & Payroll Backend — Security Audit & Vulnerability Assessment Report

**Target Project:** `stafo_backend` (Laravel 10.x, Node.js Chat Server, Python Face Verifier)  
**Date:** October 2026  
**Auditor:** Automated Senior Security Engineering Review  
**Overall Risk Level:** 🚨 **CRITICAL RISK** (Immediate Action Required)

---

## 1. Executive Summary

A comprehensive source code security assessment of the **Stafo Backend** application was conducted. The assessment identified **16 high-impact vulnerabilities**, including multiple **Critical (CVSS 9.0–10.0)** issues that expose the application to:
1. **Remote Code Execution (RCE)** through unrestricted file uploads executed via Apache handler misconfigurations.
2. **Instant Zero-Credential Account Takeover** via unauthenticated payment callback routes.
3. **Authentication Bypass** due to plaintext OTP transmission in API responses.
4. **Active Financial Exposure** resulting from live RazorpayX, Cashfree, Open Money, and PayU secret keys and payout bank accounts hardcoded in the codebase.
5. **Mass Confidential Data Exfiltration** due to unauthenticated employee dossier and salary endpoints.
6. **Live Debugging Disclosure** with `APP_DEBUG=true`, `display_errors On`, and `phpinfo()` active in production.

---

## 2. Summary Table of Identified Findings

| ID | Title | Severity | CVSS v3.1 | Primary Affected Component |
| :--- | :--- | :---: | :---: | :--- |
| **CRIT-01** | Zero-Auth Direct Account Takeover | 🔴 Critical | **10.0** | `UserPackageController@failure`, `VerifyCsrfToken.php` |
| **CRIT-02** | Plaintext OTP Leak in API Response | 🔴 Critical | **9.8** | `ApiLoginController@sendOtp` |
| **CRIT-03** | Arbitrary File Upload to RCE | 🔴 Critical | **9.8** | `EmployeeDocumentController`, `DocumentController`, `public/.htaccess` |
| **CRIT-04** | Hardcoded Live Gateway & Payout Secrets | 🔴 Critical | **9.8** | `Constant.php`, `PackageController.php`, `SmsHelper.php` |
| **CRIT-05** | Unauthenticated Payment & Package Bypass | 🔴 Critical | **9.1** | `PackageController@paymentUpdate`, `PackageController@success` |
| **CRIT-06** | Unauthenticated Employee Dossier & Salary Dump | 🔴 Critical | **9.1** | `routes/api.php`, `EmployeeReportController`, `SalaryController` |
| **CRIT-07** | Public Root Deployment & `phpinfo()` Exposure | 🔴 Critical | **9.1** | `index.php`, `info.php`, root `.htaccess` |
| **HIGH-01** | Unauthenticated WebSocket Chat & Data Leak | 🟠 High | **8.6** | `chat-server/server.js` |
| **HIGH-02** | BOLA / IDOR in KYC Documents & Support Tickets | 🟠 High | **8.2** | `DocumentController@getDocumentsByCompany`, `UserCompanyDetailController` |
| **HIGH-03** | Mass Assignment on Company Subscription & KYC | 🟠 High | **7.5** | `CompanyDetail.php`, `UserCompanyDetailController@update` |
| **HIGH-04** | Debug Mode & Error Display in Production | 🟠 High | **7.5** | `.env`, `.htaccess` |
| **HIGH-05** | Disabled SSL/TLS Verification on Outbound APIs | 🟠 High | **7.4** | `CommonFunction.php`, `Helper.php` |
| **MED-01** | Stored XSS via SVG File Uploads | 🟡 Medium | **6.5** | `UserEmployeeController`, `ChatController` |
| **MED-02** | Missing Dedicated Rate Limiting on OTP / Login | 🟡 Medium | **6.5** | `RouteServiceProvider.php`, `routes/api.php` |
| **MED-03** | Permanent OTP Persistence in Database | 🟡 Medium | **5.3** | `ApiLoginController`, `LoginRegisterController` |
| **MED-04** | Insecure Session Cookie Configuration | 🟡 Medium | **5.0** | `config/session.php` |

---

## 3. Deep-Dive Vulnerability Analysis & Remediation

---

### [CRIT-01] Zero-Auth Direct Account Takeover via `/failure` and `/success`
- **Location:** `routes/web.php` (lines 115-116), `app/Http/Controllers/User/UserPackageController.php` (lines 119-120, 139-140), `app/Http/Middleware/VerifyCsrfToken.php` (lines 15-16)
- **Mechanics:**
  The `/failure` and `/success` POST routes are completely excluded from CSRF protection and do not require user authentication. Inside `UserPackageController.php`:
  ```php
  public function failure(Request $request){
      // ...
      $user = CompanyDetail::find($request->udf1);
      Auth::login($user); // <-- VULNERABILITY: Immediately logs in user from parameter
      return view('user.packages.failure', compact('request'));
  }
  ```
  Any malicious actor can issue a `POST /failure` request with `udf1=1` and receive a valid authenticated session cookie for Company 1 without providing credentials.
- **Remediation:**
  1. Remove `Auth::login($user)` from unauthenticated payment webhook handlers.
  2. Implement strict cryptographic HMAC signature validation for PayU callbacks before any action is executed.

---

### [CRIT-02] Plaintext OTP Leak in API Response
- **Location:** `app/Http/Controllers/Api/ApiLoginController.php` (lines 383-388)
- **Mechanics:**
  In `sendOtp()`, after sending the SMS, the API returns the OTP in the JSON response payload:
  ```php
  if ($response['success']) {
      return response()->json([
          'success' => true,
          'message' => 'OTP sent successfully to registered number.',
          'otp' => $otp, // <-- VULNERABILITY
          'user_type' => $user instanceof Employee ? 'employee' : 'company',
          'phone' => substr($phone, 0, 2) . '******' . substr($phone, -2),
      ], 200);
  }
  ```
  An attacker knowing any user's mobile number simply calls `/api/send-otp`, reads the OTP from the JSON response, and posts it to `/api/verify-otp` to obtain a Sanctum Bearer token.
- **Remediation:**
  Remove `'otp' => $otp` from all responses in production.

---

### [CRIT-03] Unrestricted File Upload Leading to Remote Code Execution (RCE)
- **Location:**
  - `app/Http/Controllers/Api/EmployeeDocumentController.php` (lines 133-136)
  - `app/Http/Controllers/Api/DocumentController.php` (lines 40-52)
  - `app/Http/Controllers/Api/EmployeeExpenseController.php` (lines 47-56)
  - `app/Http/Controllers/Api/TripApiExpenseController.php` (lines 224-235)
  - `public/.htaccess` (lines 25-27)
- **Mechanics:**
  Multiple controllers write uploaded files using client-supplied extensions without MIME type validation:
  ```php
  $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
  $file->move(public_path('uploads/employee_documents'), $filename);
  ```
  In `public/.htaccess`, Apache has an explicit handler:
  ```apache
  <IfModule mime_module>
    AddHandler application/x-httpd-ea-php81 .php .php8 .phtml
  </IfModule>
  ```
  Uploading a `.phtml` or `.php` file creates an accessible script inside `public/uploads/` that Apache executes as PHP.
- **Remediation:**
  1. Add strict validation: `$request->validate(['file' => 'required|file|mimes:jpeg,png,pdf|max:5120']);`.
  2. Create `public/uploads/.htaccess` with:
     ```apache
     <FilesMatch "(?i)\.(php|php8|phtml|phar|sh|pl|py|cgi)$">
         Require all denied
     </FilesMatch>
     php_flag engine off
     Options -ExecCGI
     ```

---

### [CRIT-04] Live Payment Gateway, Banking & Payout Credentials Committed in Code
- **Location:**
  - `app/Library/Constant.php` (lines 34-43, 68-72, 86-96, 115-117)
  - `app/Http/Controllers/Api/PackageController.php` (lines 114-115)
  - `app/Helpers/SmsHelper.php` (line 23)
- **Mechanics:**
  Repository files contain live credentials:
  - **RazorpayX Live Payout API:** User `rzp_live_EgF7jW4K1KOB76`, password `9UuchMHRv6KXiwVfyccIZBob`, Account `4564567349055059`.
  - **Cashfree Live Payout API:** User `CF161850CG4TUCNP10CMT71UDED0`, Secret `cf82affeb46cbc7cab3c4ad68f3c263d838f6fac`.
  - **Open Money Live PG & Payout:** Token `7617fa10-8c43...`, Account `000405658800`.
  - **PayU Merchant Key & Salt:** In `PackageController.php` (`1AJhSD` / `tBjCq35cgf3f12ya0usuhEtH9IJ7pSyq`).
  - **QuickText SMS API Key:** In `SmsHelper.php`.
- **Remediation:**
  1. Immediately revoke and regenerate all credentials on respective provider dashboards.
  2. Move all credentials to `.env` and read them via `config()`. Never commit secrets to Git.

---

### [CRIT-05] Unauthenticated Payment & Subscription Activation Bypass
- **Location:** `routes/api.php` (lines 78-80), `app/Http/Controllers/Api/PackageController.php` (lines 268-283)
- **Mechanics:**
  `/api/paymentUpdate` and `/api/success` are unauthenticated routes that activate packages based on client request parameters:
  ```php
  if($request->status == 'success'){
      $company = CompanyDetail::find($company_id);
      $company->package_id = $packageId;
      $company->subscription_end = date('Y-m-d', strtotime("+".$duration." days"));
      $company->save();
  }
  ```
  Anyone can send a POST request with arbitrary company and package IDs to activate accounts for free.
- **Remediation:**
  Validate server-to-server gateway webhook signatures or poll the payment gateway API directly before updating subscriptions.

---

### [CRIT-06] Unauthenticated Business Routes Exposing Confidential Employee & Salary Data
- **Location:** `routes/api.php` (lines 393-495)
- **Mechanics:**
  Line 393 closes `Route::middleware('auth:sanctum')`. Consequently, lines 394 to 495 are exposed without any authentication:
  - `POST /api/report/export-employee`: Dumps complete employee dossiers with personal phone numbers, emails, salaries, addresses, and bank accounts for any company.
  - `POST /api/chat/get`: Reads internal company support chats by company ID.
  - `POST /api/chat/send`: Forges support messages.
  - `POST /api/salary/save` & `POST /api/salary/preview`: Unauthenticated payroll calculation.
  - `POST /api/document-verify`: Uses company funds to verify third-party PAN/Aadhaar documents.
  - `POST /api/cron-job/mark-absent`, `/api/autoPunchOut`: Publicly resets attendance.
- **Remediation:**
  Move the closing brace `});` of `auth:sanctum` to the very bottom of `routes/api.php`.

---

### [CRIT-07] Public Web Root Exposure, Directory Listing Risk & `phpinfo()`
- **Location:** Root `index.php`, `info.php`, `.htaccess`, `error_log`
- **Mechanics:**
  - `info.php` in the root calls `phpinfo()`, exposing server architecture, paths, and environment configurations.
  - Root `.htaccess` only denies `.env`, leaving `.git/` (`https://domain/.git/HEAD`), `composer.json`, `composer.lock`, and `package.json` accessible over HTTP.
- **Remediation:**
  1. Point the web server document root strictly to `public/`.
  2. Delete `info.php` and `error_log`.
  3. Deny access to dotfiles and configuration files in `.htaccess`.

---

### [HIGH-01] Unauthenticated WebSocket Chat Server & Cross-Tenant Data Broadcast
- **Location:** `chat-server/server.js` (lines 28-36, 72-99, 167-173)
- **Mechanics:**
  1. Socket.IO connection has no authentication or JWT validation (`cors: { origin: '*' }`).
  2. Any client can join any company room via `join_room` with `{ company_id: X, role: 'admin' }`.
  3. Line 167 broadcasts `chat_list_activity` to **all connected sockets globally**, leaking message snippets across unrelated companies.
- **Remediation:**
  Implement JWT authentication on socket connection and restrict broadcast scopes.

---

### [HIGH-02] Broken Object-Level Authorization (BOLA / IDOR) in Documents & Tickets
- **Location:** `DocumentController@getDocumentsByCompany`, `UserCompanyDetailController@showReplies`
- **Mechanics:**
  1. `GET /api/company/{company_id}/documents` returns company legal and KYC documents without validating that the authenticated user belongs to that company.
  2. `showReplies(Ticket $ticket)` allows any authenticated company to view support tickets belonging to other companies.
- **Remediation:**
  Ensure queries filter by `where('company_id', Auth::id())`.

---

### [HIGH-03] Mass Assignment on Company Subscription & KYC
- **Location:** `app/Models/CompanyDetail.php` (lines 14-53), `UserCompanyDetailController@update`
- **Mechanics:**
  Controller performs `$company->update($request->all())`. In `CompanyDetail.php`, `$fillable` includes `subscription_start`, `subscription_end`, `status`, `max_employee_add`, and `pan_verify`. Any company user can modify their own subscription dates and KYC status.
- **Remediation:**
  Remove privileged fields from `$fillable` and use `$request->only(...)` in controllers.

---

### [HIGH-04] Debug Mode Active in Production
- **Location:** `.env` (line 4), `.htaccess` (lines 40, 52)
- **Mechanics:**
  `APP_DEBUG=true` in `.env` combined with `php_flag display_errors On` causes any unhandled exception to display Spatie Ignition with full database credentials, secrets, and environment dumps.
- **Remediation:**
  Set `APP_DEBUG=false` in `.env` and `php_flag display_errors Off` in `.htaccess`.

---

### [HIGH-05] Disabled SSL/TLS Verification on Outbound APIs
- **Location:** `app/Library/CommonFunction.php` (lines 102, 264, 305, 306, 497), `app/Helpers/Helper.php` (line 158)
- **Mechanics:**
  cURL requests to BBPS, banking APIs, and FCM use:
  ```php
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  ```
  Disabling certificate verification makes outbound transactions vulnerable to MITM interception.
- **Remediation:**
  Remove these options or set `CURLOPT_SSL_VERIFYPEER => true` and `CURLOPT_SSL_VERIFYHOST => 2`.

---

### [MED-01] Stored XSS via SVG Uploads
- **Location:** `UserEmployeeController.php` (line 575), `ChatController.php` (line 112)
- **Mechanics:**
  Allowed upload mime types include `svg`. SVG files can embed malicious JavaScript (`<script>` or `onload`) that executes when viewed in a browser.
- **Remediation:**
  Remove `svg` from image upload validation allowlists.

---

### [MED-02] Missing Dedicated Rate Limiting on OTP & Authentication
- **Location:** `RouteServiceProvider.php`, `routes/api.php`
- **Mechanics:**
  No specific rate limiting exists on `/api/send-otp` or `/api/verify-otp`, exposing the system to SMS toll fraud and brute-force attacks.
- **Remediation:**
  Configure `RateLimiter::for('otp', ...)` with a limit of 5 requests per minute per IP.

---

### [MED-03] Permanent OTP Persistence in Database
- **Location:** `ApiLoginController.php` (line 359), `LoginRegisterController.php` (line 213)
- **Mechanics:**
  Generated OTPs are stored in `company_details.otp` and never cleared upon successful login.
- **Remediation:**
  Store OTPs exclusively in cache with a short TTL (5 minutes) and nullify database fields after use.

---

### [MED-04] Insecure Session Cookie Configuration
- **Location:** `config/session.php` (line 171)
- **Mechanics:**
  `SESSION_SECURE_COOKIE` is unset in `.env`, allowing session cookies to be transmitted over unencrypted HTTP.
- **Remediation:**
  Add `SESSION_SECURE_COOKIE=true` to `.env`.

---

## 4. Remediation Checklist & Sign-Off

- [ ] **Revoke & Rotate Credentials:** Invalidate and replace all keys in `Constant.php` and `PackageController.php`.
- [ ] **Fix Account Takeover:** Remove `Auth::login()` from `UserPackageController.php`.
- [ ] **Sanitize OTP Response:** Remove `'otp' => $otp` from `ApiLoginController.php`.
- [ ] **Enforce Sanctum Scope:** Move the closing brace in `routes/api.php` to protect routes 394–495.
- [ ] **Harden File Uploads:** Add extension and MIME validation; deploy `public/uploads/.htaccess`.
- [ ] **Secure WebSockets:** Add token authentication to `chat-server/server.js` and eliminate global broadcast.
- [ ] **Disable Production Debugging:** Set `APP_DEBUG=false`, `display_errors Off`, and delete `info.php`.
- [ ] **Fix SSL Verification:** Re-enable `CURLOPT_SSL_VERIFYPEER` across all outbound calls.
