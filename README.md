# Car Web App

A car-themed web application built with the **Yii 2** PHP framework, developed as a university lab project. It follows a clean MVC architecture with controllers, models, views, and database-backed configuration.

## Highlights

- Built on **Yii 2** (PHP), following the framework's MVC conventions
- User authentication (login / logout) and standard page flow
- Database-driven models with validation rules
- Environment-aware configuration (web, console, test)
- Automated tests set up with **Codeception** (unit, functional, acceptance)

## Tech Stack

- PHP, Yii 2 framework
- Composer (dependency management)
- MySQL (or compatible) database
- Codeception (testing)

## Getting Started

```bash
git clone https://github.com/ZinebMEFTAH/car-web-app.git
cd car-web-app
composer install
```

1. Configure your database in `config/db.php`.
2. Serve the application:
   ```bash
   php yii serve
   ```
3. Open `http://localhost:8080` in your browser.

## Project Structure

- `controllers/` — web controllers (request handling)
- `models/` — data models and form models with validation
- `views/` — PHP/HTML templates, organized per controller
- `config/` — application, database, and environment configuration
- `assets/` — CSS/JS/image asset bundles
- `tests/` — Codeception test suites

## Notes

University lab project (TP) — created to practice building a structured, testable web application with a modern PHP framework.
