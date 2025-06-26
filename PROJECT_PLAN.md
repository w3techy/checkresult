# Project Plan: checkresult PHP Application

## 1. Project Overview

**Name:** checkresult
**Purpose:** A PHP-based software designed to allow students to check their academic results online. It is intended for use by schools and colleges.
**Current Status:** In development. The application has a basic structure for login and result display, but contains critical flaws in its database schema and data retrieval logic that prevent it from functioning correctly.
**Developer:** W3Techy (as per README.md)

## 2. Key Components

The application consists of several PHP scripts and supporting files:

*   **`index.php`**: User login page. Handles authentication by checking "Exam Number" and "PIN".
*   **`config.php`**: Database connection configuration (MySQL).
*   **`session.php`**: Manages user sessions, ensuring users are logged in to access protected pages.
*   **`header.php` & `footer.php`**: Common HTML header and footer content for pages.
*   **Subject-Specific PHP Files (`chemistry.php`, `commerce.php`, `english.php`, `mathematics.php`):** Scripts responsible for fetching and displaying results for individual subjects. These are included in `result.php`.
*   **`result.php`**: Displays the consolidated results for all subjects for the logged-in user.
*   **`style.css`**: CSS file for basic styling of the HTML pages.
*   **`sql/` directory**: Contains SQL dump files for creating database tables:
    *   `user.sql`: User table schema and sample data.
    *   `chemistry.sql`, `commerce.sql`, `english.sql`, `mathematics.sql`: Schemas and sample data for respective subject result tables.

## 3. Dependencies

*   **PHP:** Server-side scripting language (version supporting `mysqli` extension).
*   **MySQL:** Relational database management system.
*   **Web Server:** A server capable of running PHP applications (e.g., Apache, Nginx).
*   **Web Browser:** For user interaction.
*   No external libraries (Composer packages, JavaScript frameworks, etc.) are currently used.

## 4. Core Functionality

1.  **User Authentication:**
    *   Users log in via `index.php` using an "Exam Number" and "PIN".
    *   Credentials (PIN) are checked against the `user` table.
    *   On success, the "Exam Number" is stored in a session, and the user is redirected to `result.php`.
    *   On failure, an error message is shown.
2.  **Result Display:**
    *   `result.php` is intended to display subject-wise scores, grades, and remarks for the logged-in student.
    *   It includes individual subject PHP files which query their respective database tables.
    *   **Critical Flaw:** The current implementation cannot correctly fetch or display results for specific students due to schema design and query logic errors (see Section 5).
3.  **Print Results:**
    *   A "Print" button on `result.php` uses browser functionality (`window.print()`) to print the displayed results.

## 5. Data Structures (Database Tables)

*   **`user` Table:**
    *   `id`: `INT AUTO_INCREMENT PRIMARY KEY`
    *   `pin`: `VARCHAR(11)`
    *   *Issue:* The login process stores the user-provided "Exam Number" (string) in the session, while `user.id` is an integer. `session.php` attempts to validate this string Exam Number against the integer `user.id`, which is problematic.

*   **Subject Tables (`chemistry`, `commerce`, `english`, `mathematics`):**
    *   Each has a similar structure:
        *   `id`: `INT AUTO_INCREMENT PRIMARY KEY` (for the subject record itself)
        *   `grade`: `VARCHAR(50)`
        *   `remarks`: `VARCHAR(50)`
        *   `score`: `INT(100)`
    *   **Critical Flaw:** These tables **lack a foreign key** to link results to a specific user in the `user` table (e.g., `user_id` or `exam_number`).
    *   **Consequence:** The queries in subject PHP files (e.g., `SELECT * FROM chemistry WHERE ID='$user_check'`) attempt to match the student's "Exam Number" (string, from session) against the subject table's own auto-incrementing integer `id`. This will not retrieve correct student-specific results and is a fundamental design flaw.

## 6. User Interactions

1.  User navigates to `index.php`.
2.  User enters "Exam Number" and "PIN" and submits the form.
3.  **If PIN is valid (matches any PIN in `user` table):**
    *   User is redirected to `result.php`.
    *   `result.php` attempts to display results. Due to the data structure flaws, the displayed results will likely be incorrect or empty.
    *   User can click a "Print" button.
4.  **If PIN is invalid:**
    *   An error message is shown on `index.php`.
5.  Accessing `result.php` without a session redirects to `index.php`. No explicit logout functionality exists.

## 7. Build and Deployment

*   **Build:** No build process is required (PHP is interpreted).
*   **Deployment:**
    1.  Set up a web server with PHP and MySQL.
    2.  Enable the `mysqli` PHP extension.
    3.  Create a MySQL database (e.g., `result`).
    4.  Create a database user and grant permissions.
    5.  Import table schemas and data from the `.sql` files in the `sql/` directory.
    6.  Copy all project files to the web server's document root.
    7.  Configure `config.php` with the correct database credentials for the environment.
    *   **Note:** The application is not functionally correct for deployment in its current state.

## 8. Testing Strategy

*   **Current State:** No formal automated testing (unit, integration, or E2E tests) is present in the codebase.
*   **Inferred Method:** Testing is likely performed manually by:
    *   Setting up the environment.
    *   Testing login with valid/invalid credentials.
    *   Checking the `result.php` page (which would reveal the data fetching issues).
    *   Testing the print functionality.
*   **Limitations:** Manual testing is time-consuming, error-prone, and lacks regression safeguards.

## 9. Proposed Enhancements

### I. Critical Fixes & Refactoring (Highest Priority)

1.  **Correct Database Schema & Result Logic:**
    *   Add a foreign key (`user_id` or `exam_number`) to all subject tables, linking them to the `user` table.
    *   Modify data insertion and result fetching queries to use this correct linkage.
2.  **Consistent User Identifier:** Standardize whether `user.id` (integer) or `exam_number` (string) is the primary student identifier stored in the session and used in queries. Adjust `user` table schema if `exam_number` needs to be a unique key.
3.  **Robust Input Validation & Sanitization:** Implement stricter server-side validation for all inputs beyond current `mysqli_real_escape_string`.
4.  **Improved Error Handling:** Replace `die()` calls with graceful error handling (log errors, show user-friendly messages).
5.  **Secure `config.php`:** Move database credentials out of the webroot or restrict direct access.

### II. Feature Enhancements & Usability

6.  **Admin Panel:** For managing users, results, and subjects.
7.  **Dynamic Subject Handling:** Move from hardcoded subject files to a database-driven subject and result system (e.g., `subjects` table, unified `results` table).
8.  **UI/UX Modernization:** Improve visual design and responsiveness (e.g., using a CSS framework).
9.  **PIN/Password Security:** Hash PINs in the database (e.g., `password_hash()`). Consider a "Forgot PIN" feature.
10. **User Profile Information:** Display student's name/details on the result page.
11. **Explicit Logout Functionality.**

### III. Development & Maintenance Improvements

12. **Automated Testing:** Introduce unit tests (PHPUnit) and potentially integration tests.
13. **Version Control:** Adhere to Git best practices (branches, meaningful commits).
14. **Templating Engine (Optional):** Consider for larger UI/logic separation (e.g., Twig).

This plan provides a snapshot of the project's current state and a roadmap for necessary corrections and potential improvements. Addressing the critical fixes in Section 9.I is essential for the application to fulfill its core purpose.
