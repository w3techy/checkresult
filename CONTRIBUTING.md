# Contributing to Result Checker

First off, thank you for considering contributing to Result Checker! We welcome any help to make this project better. Whether it's reporting a bug, proposing a new feature, or writing code, your input is valuable.

## Table of Contents
- [Getting Started](#getting-started)
  - [Prerequisites](#prerequisites)
  - [Setup](#setup)
- [How to Contribute](#how-to-contribute)
  - [Reporting Bugs](#reporting-bugs)
  - [Suggesting Enhancements](#suggesting-enhancements)
  - [Submitting Pull Requests](#submitting-pull-requests)
- [Coding Conventions](#coding-conventions)
- [Testing](#testing)
- [Code of Conduct](#code-of-conduct)
- [Questions](#questions)

## Getting Started

### Prerequisites
Before you begin, ensure you have the following installed and configured:
- PHP (version 7.x or higher recommended, with `mysqli` extension)
- MySQL (version 5.x or higher recommended)
- A web server (e.g., Apache, Nginx)
- Git (for version control)

### Setup
1.  **Fork the repository** (if contributing via GitHub/GitLab).
2.  **Clone your fork** locally: `git clone [your-fork-url]`
3.  **Navigate to the project directory**: `cd checkresult`
4.  **Run the installer**: Access `install.php` via your web browser and follow the on-screen instructions to set up your `config.php`, `site_meta.php`, and database tables.
    - Ensure your web server has write permissions to the project root during installation.
    - After successful installation, it's recommended to delete `install.php` for security.
5.  You should now have a working local copy of the application.

## How to Contribute

### Reporting Bugs
If you find a bug, please ensure the bug was not already reported by searching existing issues. If you're unable to find an open issue addressing the problem, open a new one. Be sure to include:
- A clear and descriptive title.
- Steps to reproduce the bug.
- What you expected to happen.
- What actually happened (including any error messages and relevant screenshots).
- Your local setup (PHP version, MySQL version, Web Server, Browser).

### Suggesting Enhancements
If you have an idea for a new feature or an improvement to an existing one:
- Check if there's an existing issue or discussion about your idea.
- If not, open a new issue, providing:
  - A clear and descriptive title.
  - A detailed description of the proposed enhancement and its benefits.
  - Any potential drawbacks or alternative solutions.
  - Mockups or examples if applicable.

### Submitting Pull Requests
1.  **Fork the repository and create your branch from `main` (or the primary development branch).**
    - Example: `git checkout -b feature/my-new-feature` or `fix/bug-i-am-fixing`
2.  **Make your changes.** Adhere to the coding conventions (see below).
3.  **Test your changes thoroughly.** (See [Testing](#testing) section).
4.  **Commit your changes** with a clear and concise commit message.
    - Example: `feat: Add user profile page` or `fix: Correct result calculation error`
5.  **Push your branch to your fork.**
    - `git push origin feature/my-new-feature`
6.  **Open a Pull Request** against the original repository's `main` branch.
    - Provide a clear description of the problem and solution. Include the relevant issue number if applicable.

## Coding Conventions
While we don't have a strict, enforced style guide yet, please try to:
- Maintain a consistent coding style with the existing codebase.
- Use clear and descriptive variable and function names.
- Comment your code where necessary, especially for complex logic.
- Ensure your code is well-formatted and readable (e.g., proper indentation).
- For PHP, consider general best practices (e.g., avoiding global variables where possible, using `require_once` for includes).

## Testing
- Currently, testing is primarily manual. Before submitting any changes, please test your code thoroughly to ensure it works as expected and doesn't break existing functionality.
- Describe the manual tests you performed in your Pull Request.
- We aim to introduce automated tests in the future.

## Code of Conduct
We expect all contributors to adhere to a basic standard of respectful and constructive communication. Please be kind and considerate when interacting with others in issues, discussions, and pull requests. (A more formal Code of Conduct may be adopted later, such as the Contributor Covenant).

## Questions
If you have any questions about contributing, feel free to open an issue and tag it as a "question".

---
Thank you for contributing!
