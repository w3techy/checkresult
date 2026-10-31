# checkresult

**checkresult** is an online student result checking portal built with PHP and MySQL. It enables schools, colleges, and examination bodies to allow students to securely check, view, and print their academic results.

---

## Features

- **Secure Student Portal:** Students log in with their Examination Number and PIN (secured with Bcrypt password hashing).
- **Formatted Statement of Results:** Clean, printable results display with responsive design and print stylesheet support.
- **Protected Admin Dashboard:** Dedicated `/admin` interface guarded by session authentication and CSRF protection for managing examination subjects.
- **Hardened Web Installer:** Step-by-step setup wizard (`install.php`) with injection protection and automatic lockfile creation.
- **Enterprise Database Design:** InnoDB storage engine with active foreign keys, composite unique constraints, and `utf8mb4` encoding.
- **PHP 8+ Ready:** Fully compatible with modern PHP versions (PHP 7.4 through 8.3+).

---

## Quick Setup

1. **Clone the repository:**
   ```bash
   git clone https://github.com/w3techy/checkresult.git
   cd checkresult
   ```

2. **Web-Based Installation:**
   - Navigate to `http://localhost/checkresult/install.php` in your browser.
   - Enter your MySQL database credentials, site metadata, and create your Administrator account.
   - Click **Install Result Checker**.
   - After installation, delete `install.php` for security.

3. **Manual Setup (Alternative):**
   - Copy `config-template.php` to `config.php` and fill in your database credentials.
   - Import the schema files in order:
     1. `sql/user.sql`
     2. `sql/subjects.sql`
     3. `sql/admin_users.sql`
     4. `sql/results.sql`

---

## Default Demo Credentials

### Student Portal
- **Exam Number:** `STUDENT001` &nbsp;|&nbsp; **PIN:** `PIN001`
- **Exam Number:** `STUDENT002` &nbsp;|&nbsp; **PIN:** `PIN002`
- **Exam Number:** `STUDENT003` &nbsp;|&nbsp; **PIN:** `PIN003`

### Administrator Portal (`/admin`)
- **Username:** `admin` &nbsp;|&nbsp; **Password:** `admin123` *(Change upon setup)*

---

## License

This project is licensed under the terms of the GNU General Public License v3.0 ([LICENSE](LICENSE)).

Developed by [W3Techy](https://w3techy.com.ng).
