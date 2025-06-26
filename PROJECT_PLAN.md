# Project Plan: checkresult PHP Application

## 1. Project Overview

**Name:** checkresult
**Purpose:** A PHP-based software designed to allow students to check their academic results online. It is intended for use by schools and colleges.
**Current Status:** Undergoing major refactoring to correct critical architectural flaws. The database schema and result fetching logic have been significantly improved to support dynamic subject handling and accurate, per-student result display.
**Developer:** W3Techy (as per README.md)

## 2. Key Components

The application consists of several PHP scripts and supporting files:

*   **`index.php`**: User login page. Handles authentication by checking "Exam Number" (now the Primary Key in `user` table) and "PIN".
*   **`config.php`**: Database connection configuration (MySQL).
*   **`session.php`**: Manages user sessions, storing the `exam_number` and ensuring users are logged in to access protected pages.
*   **`header.php` & `footer.php`**: Common HTML header and footer content for pages.
*   **`result.php`**: Displays a consolidated and dynamic list of results for the logged-in user. It fetches data by joining `results` and `subjects` tables based on the user's `exam_number`.
*   **`style.css`**: CSS file for basic styling of the HTML pages.
*   **`sql/` directory**: Contains SQL dump files for creating database tables:
    *   `user.sql`: Defines the `user` table with `exam_number` as PK.
    *   `subjects.sql`: Defines the `subjects` table for dynamic subject management.
    *   `results.sql`: Defines the `results` table, linking users and subjects, and storing scores, grades, etc.
    *   *(Obsolete `chemistry.sql`, `commerce.sql`, `english.sql`, `mathematics.sql` have been removed).*
*   *(Obsolete `chemistry.php`, `commerce.php`, `english.php`, `mathematics.php` have been removed).*

## 3. Dependencies

*   **PHP:** Server-side scripting language (version supporting `mysqli` extension).
*   **MySQL:** Relational database management system (preferably supporting InnoDB for foreign key enforcement, though current SQL dumps are MyISAM compatible).
*   **Web Server:** A server capable of running PHP applications (e.g., Apache, Nginx).
*   **Web Browser:** For user interaction.
*   No external libraries (Composer packages, JavaScript frameworks, etc.) are currently used.

## 4. Core Functionality

1.  **User Authentication:**
    *   Users log in via `index.php` using their unique "Exam Number" and "PIN".
    *   Credentials (`exam_number` and `pin`) are validated against the `user` table.
    *   On success, the `exam_number` is stored in a session, and the user is redirected to `result.php`.
    *   On failure, an error message is shown.
2.  **Result Display:**
    *   `result.php` dynamically displays subject-wise scores, grades, and remarks for the logged-in student.
    *   It fetches data by querying the `results` table, joining with `subjects` to get subject names, filtered by the `exam_number` stored in the session.
    *   Handles cases where a student may have no results.
3.  **Print Results:**
    *   A "Print" button on `result.php` uses browser functionality (`window.print()`) to print the displayed results.

## 5. Data Structures (Database Tables)

The database schema has been significantly refactored:

*   **`user` Table (`sql/user.sql`):**
    *   `exam_number`: `VARCHAR(50) PRIMARY KEY`. Unique identifier for each user/student.
    *   `pin`: `VARCHAR(255)`. Stores the user's PIN (comment included about hashing in production).

*   **`subjects` Table (`sql/subjects.sql`):**
    *   `subject_id`: `INT AUTO_INCREMENT PRIMARY KEY`. Unique identifier for each subject.
    *   `subject_name`: `VARCHAR(100) NOT NULL UNIQUE`. Name of the subject (e.g., "Mathematics").

*   **`results` Table (`sql/results.sql`):**
    *   `result_id`: `INT AUTO_INCREMENT PRIMARY KEY`.
    *   `exam_number`: `VARCHAR(50) NOT NULL`. Foreign key referencing `user.exam_number`.
    *   `subject_id`: `INT NOT NULL`. Foreign key referencing `subjects.subject_id`.
    *   `score`: `INT`. Numerical score.
    *   `grade`: `VARCHAR(10)`. Letter grade.
    *   `remarks`: `VARCHAR(255)`. Additional comments.
    *   *Note on Foreign Keys:* Actual FK constraints are commented in the SQL dump for MyISAM compatibility but are intended conceptually and for InnoDB.

*   **Obsolete Subject Tables:** The previous individual subject tables (`chemistry`, `commerce`, etc.) and their corresponding SQL files have been removed.

## 6. User Interactions

1.  User navigates to `index.php`.
2.  User enters "Exam Number" and "PIN" and submits the form.
3.  **If credentials are valid:**
    *   User is redirected to `result.php`.
    *   `result.php` displays a table of subjects, scores, grades, and remarks for that user.
    *   If the user has no results recorded, an appropriate message is shown.
    *   User can click a "Print" button.
4.  **If credentials are invalid:**
    *   An error message is shown on `index.php`.
5.  Accessing `result.php` without a session redirects to `index.php`. No explicit logout functionality currently exists.

## 7. Build and Deployment

*   **Build:** No build process is required (PHP is interpreted).
*   **Deployment:**
    1.  Set up a web server with PHP and MySQL.
    2.  Enable the `mysqli` PHP extension.
    3.  Create a MySQL database (e.g., `result`).
    4.  Create a database user and grant permissions.
    5.  Import table schemas and data from the new SQL files in order: `sql/user.sql`, `sql/subjects.sql`, then `sql/results.sql`.
    6.  Copy all project files to the web server's document root.
    7.  Configure `config.php` with the correct database credentials for the environment.

## 8. Testing Strategy

*   **Current State:** No formal automated testing is present.
*   **Required Method:** Manual testing is crucial after these significant changes. This involves:
    *   Setting up the environment as per the deployment steps.
    *   Testing login with various valid/invalid credentials and for users with/without results (refer to detailed manual test plan).
    *   Verifying correct display of results on `result.php`.
    *   Testing print functionality and session handling.

## 9. Proposed Enhancements

Many of the initial critical fixes have been addressed by the recent refactoring.

### I. Addressed/In Progress Critical Fixes & Refactoring

1.  **Correct Database Schema & Result Logic:** **(Largely Addressed)** The schema now uses `user`, `subjects`, and `results` tables with proper linkage via `exam_number` and `subject_id`. `result.php` fetches data dynamically.
2.  **Consistent User Identifier:** **(Addressed)** `exam_number` is now consistently used as the primary user identifier and stored in the session.
3.  **Dynamic Subject Handling:** **(Addressed)** The system now supports dynamic subjects via the `subjects` table and a unified `results` table. Old hardcoded subject files removed.

### II. Remaining/Future Enhancements (from original list)

4.  **Robust Input Validation & Sanitization:** Further review and implement stricter server-side validation for all inputs beyond current `mysqli_real_escape_string` (e.g., format, length for PINs, scores).
5.  **Improved Error Handling:** Replace any remaining `die()` calls or basic error messages with more graceful error handling (log errors to a file, show user-friendly messages).
6.  **Secure `config.php`:** Move database credentials out of the webroot or restrict direct access.
7.  **Admin Panel:** For managing users, subjects, and results. (High value addition)
8.  **UI/UX Modernization:** Improve visual design and responsiveness.
9.  **PIN/Password Security:** Hash PINs in the database (e.g., `password_hash()` and use `password_verify()`). Implement a "Forgot PIN" mechanism.
10. **User Profile Information:** Display student's name or other relevant details (from the `user` table, which would need new columns like `full_name`) on the result page.
11. **Explicit Logout Functionality.**

### III. Development & Maintenance Improvements (from original list)

12. **Automated Testing:** Introduce unit tests (PHPUnit) and potentially integration tests.
13. **Version Control:** Adhere to Git best practices (branches, meaningful commits).
14. **Templating Engine (Optional):** Consider for larger UI/logic separation (e.g., Twig).

This plan reflects the significant architectural improvements made and outlines the next steps for stabilizing and enhancing the application.
