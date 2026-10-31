# Project Plan: checkresult PHP Application

## 1. Project Overview

**Name:** checkresult  
**Purpose:** A secure PHP-based web application designed to allow students to check their academic results online with an exam number and PIN. It is intended for use by schools and colleges.  
**Current Status:** Production-ready refactoring complete. Features secure installation, Bcrypt password/PIN hashing, parameterized prepared statements, role-based admin authentication, CSRF protection, and modern InnoDB database architecture.  
**Developer:** W3Techy (as per README.md)  

## 2. Key Components

The application consists of several PHP scripts, SQL schemas, and supporting files:

*   **`install.php`**: Secure web installer with admin setup, injection-proof configuration writers (`var_export`), schema execution, and lockfile generation.
*   **`config-template.php`**: Reference configuration template.
*   **`config.php`**: Database connection configuration (MySQL with `utf8mb4`).
*   **`site_meta.php`**: Stores site-specific metadata (`SITE_NAME`, `SITE_DESCRIPTION`). Generated during installation.
*   **`index.php`**: Student portal login page. Handles authentication using `exam_number` and hashed `pin`.
*   **`logout.php`**: Explicit student session termination.
*   **`session.php`**: Validates student sessions and confirms database existence.
*   **`header.php` & `footer.php`**: Responsive HTML5 layouts.
*   **`result.php`**: Displays formatted student examination statement of results with print support.
*   **`style.css`**: Responsive stylesheet supporting desktop, mobile, and print media.
*   **`sql/` directory**:
    *   `user.sql`: Student accounts table (`InnoDB`, hashed PINs).
    *   `subjects.sql`: Academic subjects table (`InnoDB`, unique subject names).
    *   `admin_users.sql`: Administrator credentials table (`InnoDB`, hashed passwords).
    *   `results.sql`: Academic results table (`InnoDB`, foreign keys, composite unique constraint).
*   **`admin/` directory**:
    *   `admin/login.php`: Secure administrator authentication with CSRF tokens.
    *   `admin/logout.php`: Administrator session termination.
    *   `admin/index.php`: Admin overview dashboard with summary counts.
    *   `admin/admin_header.php` & `admin/admin_footer.php`: Admin layout with strict authentication guards.
    *   `admin/manage_subjects.php`: Interface for admins to add and view subjects with CSRF protection and prepared statements.
*   **`composer.json`**: Project definition and CI linting scripts.
*   **`.github/workflows/php.yml`**: GitHub Actions CI workflow for PHP syntax and Composer validation.

## 3. Dependencies & Requirements

*   **PHP:** Version 7.4 or 8.x (tested on PHP 8.2 and 8.3) with `mysqli`, `session`, `password` extensions.
*   **MySQL / MariaDB:** Relational database management system with `InnoDB` engine support.
*   **Web Server:** Apache / Nginx / Caddy with PHP runtime.
*   **Composer:** For dependency management and automated CI linting.

## 4. Security Architecture

1.  **Password & PIN Hashing:**
    *   All passwords and student PINs are hashed using PHP's native `password_hash()` (Bcrypt).
    *   Verification is performed using `password_verify()`.
    *   Automatic rehashing/upgrade is supported for legacy plain-text PINs on login.
2.  **SQL Injection Defense:**
    *   All queries across user login, result lookups, admin authentication, and subject management use parameterized prepared statements (`mysqli_prepare`, `bind_param`).
3.  **Authentication & Session Management:**
    *   The admin portal (`/admin`) is guarded by strict session validation (`$_SESSION['is_admin'] === true`).
    *   `session_regenerate_id(true)` is called upon every successful authentication.
    *   `session.cookie_httponly` and `session.use_only_cookies` are enabled.
4.  **Installer Hardening:**
    *   Installation is permanently locked when `install.lock` or `config.php` exists.
    *   Configuration files are generated using `var_export()` to eliminate remote code execution (RCE) vectors.
    *   Database name inputs are strictly whitelisted (`/^[a-zA-Z0-9_]+$/`).
5.  **CSRF Defense:**
    *   Admin operations use cryptographically secure session tokens (`random_bytes(32)` and `hash_equals()`).

## 5. Completed Milestones

- [x] Admin authentication & session security
- [x] Bcrypt password hashing for students and administrators
- [x] Parameterized prepared statements across the application
- [x] InnoDB engine migration with foreign keys and composite unique constraints
- [x] Installer security hardening & RCE mitigation
- [x] Student and administrator logout endpoints
- [x] PHP 8+ compatibility fixes (resolved undefined variable warnings)
- [x] HTML5 document hierarchy and modern responsive styling
- [x] GitHub Actions CI workflow with automated linting and `composer.json`
