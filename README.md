# Criminal Minds Blog Platform

A PHP-based blog platform inspired by the Criminal Minds TV series, featuring CRUD operations, search functionality, and admin panel for content management.

** This project was merely made for educational purposes as an introduction to PHP and SQL Database. I am not responsible for any low-quality code as this is merely meant to showcase the final result. **

## Features

- Blog post management (Create, Read, Update, Delete)
- Search functionality across posts
- Admin panel for content moderation
- SQLite and MySQL database support
- Responsive design with CSS styling
- Dutch language interface

## Technology Stack

- PHP (backend)
- MySQL/SQLite (database)
- CSS (styling)
- JavaScript (interactivity)
- Apache (URL rewriting)

## ⚠️ Security Notice

This code was developed for educational/portfolio purposes only and contains hardcoded credentials for local development.

**DO NOT use this code in production environments.** Database credentials and configuration are intentionally simplified for demonstration purposes only.

For production use, implement proper:
- Environment variables
- Input validation  
- Authentication/authorization
- Error handling

## Installation

1. Set up a local web server (XAMPP/WAMP/MAMP)
2. Create a database named `criminal_minds` in PHPMyAdmin
3. Update database credentials in `includes/db_config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'criminal_minds');
   define('DB_USER', 'your_db_user');
   define('DB_PASS', 'your_db_password');
   ```
4. Run the setup script by visiting `setup_db.php` in your browser
5. Access the application via your local server

**SQLite Alternative:**
No setup needed - the app will automatically create a SQLite database at `database/criminal_minds.sqlite` if MySQL is unavailable.

## Project Structure

```
├── admin/          # Admin panel files
├── css/           # Stylesheets
├── js/            # JavaScript files
├── includes/      # Core functionality
├── database/      # Database files (local only, not in repo)
└── data/          # JSON data files (local only, not in repo)
```

---

*Educational project showcase - not intended for production use*
