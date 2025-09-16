# Criminal Minds Blog - Database Migration

This document explains how to migrate the Criminal Minds Blog from JSON storage to SQL database storage.

## Prerequisites

1. Access to PHPMyAdmin
2. The SQL file: `database/criminal_minds.sql`

## Migration Steps

### 1. Import Database Schema

1. Open PHPMyAdmin in your browser
2. Create a new database named `criminal_minds`:
   - Click "New" in the left sidebar
   - Enter "criminal_minds" as the database name
   - Select "utf8mb4_unicode_ci" as the collation
   - Click "Create"
3. Select the newly created `criminal_minds` database
4. Click the "Import" tab
5. Choose the file `database/criminal_minds.sql` from this project
6. Click "Go" to import the database structure and sample data

### 2. Configure Database Connection

The database connection is already configured in `includes/db_config.php` with these default settings:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'criminal_minds');
define('DB_USER', 'root');
define('DB_PASS', '');
```

If your PHPMyAdmin setup uses different credentials, update these values accordingly.

### 3. Enable Required PHP Extensions

The application now uses a hybrid approach that supports both PDO and MySQLi. To enable database functionality:

For PDO MySQL:
```ini
extension=pdo_mysql
```

For MySQLi:
```ini
extension=mysqli
```

Add one of these lines to your `php.ini` file and restart your web server.

### 4. Test Database Connection

After importing the database and enabling the required extensions, test the connection by running:

```bash
php test_db.php
```

### 5. Migrate Existing Data (Optional)

If you have existing data in the JSON file (`data/posts.json`), you can migrate it to the database by running:

```bash
php migrate_to_db.php
```

This script will:
- Read all posts from the JSON file
- Insert them into the database
- Create a backup of the original JSON file

## Database Schema

The database contains one table:

### posts
- `id` (INTEGER, PRIMARY KEY, AUTO_INCREMENT) - Unique identifier
- `title` (VARCHAR(255)) - Post title
- `content` (TEXT) - Post content (HTML)
- `status` (ENUM: 'draft', 'published') - Publication status
- `author` (VARCHAR(100)) - Author name
- `date` (DATETIME) - Publication date
- `created_at` (TIMESTAMP) - Record creation timestamp
- `featured` (BOOLEAN) - Whether the post is featured

## Hybrid Approach

The application now uses a hybrid approach:
- If database extensions are available and the database is properly configured, it will use SQL storage
- If database extensions are not available or database connection fails, it will automatically fall back to JSON storage
- This ensures the application works in all environments

## Troubleshooting

### "Database connection failed"

Verify that:
1. The database `criminal_minds` exists
2. The credentials in `includes/db_config.php` are correct
3. Your MySQL service is running
4. Either pdo_mysql or mysqli extension is enabled

### Automatic Fallback

If the database is not available, the application will automatically fall back to JSON storage, ensuring no functionality is lost.

## Files Overview

- `database/criminal_minds.sql` - Database schema and sample data
- `includes/db_config.php` - Database configuration and connection with fallback support
- `includes/functions.php` - Updated data access functions using SQL with JSON fallback
- `migrate_to_db.php` - Script to migrate data from JSON to database
- `test_db.php` - Script to test database connection and functions

## Notes

- The SQL file has been fixed to remove duplicate primary key definitions
- All database operations support both PDO and MySQLi
- The migration script includes transaction support for data integrity
- Error handling has been improved throughout the database functions
- The hybrid approach ensures compatibility with all environments