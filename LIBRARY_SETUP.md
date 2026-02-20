# Library Management System - Setup Guide

## Overview
The Library Management System has been successfully integrated into your CodeIgniter portfolio application with **separate routes, controllers, models, and views**.

## File Structure

```
application/
├── controllers/
│   ├── Portfolio.php              (existing - your portfolio pages)
│   └── Library.php                (NEW - library system controller)
├── models/
│   ├── Portfolio_model.php        (existing)
│   └── Library_model.php          (NEW - library database operations)
├── views/
│   ├── templates/                 (existing - portfolio templates)
│   │   ├── header.php
│   │   └── footer.php
│   ├── home.php                   (existing - portfolio pages)
│   ├── about.php
│   ├── contact.php
│   ├── projects.php
│   ├── skills.php
│   └── library/                   (NEW - separated library views)
│       ├── templates/
│       │   ├── header.php
│       │   └── footer.php
│       ├── dashboard.php
│       ├── profile.php
│       ├── auth/
│       │   ├── login.php
│       │   └── register.php
│       ├── books/
│       │   └── index.php
│       ├── members/
│       │   └── index.php
│       └── circulation/
│           └── index.php
└── config/
    └── routes.php                 (UPDATED - added library routes)

sql/
└── library_schema.sql             (NEW - database schema and sample data)
```

## Step 1: Configure Database

Edit `application/config/database.php` and configure your database connection:

```php
$db['default'] = array(
    'hostname' => 'localhost',   // Your database host
    'username' => 'root',        // Your database username
    'password' => '',            // Your database password
    'database' => 'portfolio',   // Your database name
    'dbdriver' => 'mysqli',
    // ... rest of config
);
```

## Step 2: Create Database Tables

1. Open **phpMyAdmin** (http://localhost/phpmyadmin)
2. Create a new database called `portfolio` (or your preferred name)
3. Go to the **SQL** tab and paste the contents of `sql/library_schema.sql`
4. Click **Execute** to create all tables and insert sample data

Or use MySQL command line:
```bash
mysql -u root -p portfolio < sql/library_schema.sql
```

## Step 3: Default Login Credentials

After importing the database schema, use these credentials to log in:

**Admin Account:**
- Username: `admin`
- Password: `password123`

**Librarian Account:**
- Username: `librarian`
- Password: `password123`

## Routes

### Portfolio Routes (Existing)
- `http://localhost/portfolio` - Home
- `http://localhost/portfolio/about` - About
- `http://localhost/portfolio/skills` - Skills
- `http://localhost/portfolio/projects` - Projects
- `http://localhost/portfolio/contact` - Contact

### Library Routes (New)
- `http://localhost/portfolio/library` - Dashboard (requires login)
- `http://localhost/portfolio/library/login` - Login page
- `http://localhost/portfolio/library/register` - Register new user
- `http://localhost/portfolio/library/logout` - Logout
- `http://localhost/portfolio/library/profile` - User profile
- `http://localhost/portfolio/library/books` - Books management (coming soon)
- `http://localhost/portfolio/library/members` - Members management (coming soon)
- `http://localhost/portfolio/library/circulation` - Circulation management (coming soon)

## Features Implemented

### Authentication
✅ Login system with secure password hashing (bcrypt)
✅ User registration with validation
✅ Session management
✅ Logout functionality

### Models
✅ User authentication methods
✅ Dashboard statistics (total books, members, borrowed items, overdue items)
✅ Books CRUD methods
✅ Members CRUD methods
✅ Circulation (borrowing/returning) methods
✅ Database transaction support for critical operations

### Views
✅ Responsive Bootstrap 5 UI
✅ Separate header/footer for library system
✅ Sidebar navigation
✅ Login and registration pages
✅ Dashboard with statistics cards
✅ User profile page
✅ Placeholder pages for books, members, and circulation

## Next Steps (To Complete)

To further develop the library system, you can:

1. **Books Module**
   - Create `Library/Books.php` controller
   - Implement add, edit, delete, list views
   - Add search and filter functionality

2. **Members Module**
   - Create `Library/Members.php` controller
   - Implement member management views
   - Add fine calculation logic

3. **Circulation Module**
   - Create `Library/Circulation.php` controller
   - Implement borrow/return transactions
   - Add overdue tracking and notifications

4. **Additional Features**
   - User management and roles (admin, librarian, member)
   - Email notifications for due dates
   - Fine/penalty system
   - Reports and statistics
   - Book reservations
   - Library analytics

## Important Notes

1. **No Portfolio Files Removed**: All existing portfolio files in the `views` folder remain unchanged
2. **Separate Routes**: Library routes don't interfere with portfolio routes
3. **Separate Views**: Library views are in their own directory structure
4. **Separate Controllers & Models**: Library has its own controller and model
5. **Bootstrap Integration**: Uses Bootstrap 5 for responsive design
6. **Security**: Uses bcrypt for password hashing and CodeIgniter's security features

## Troubleshooting

### "Cannot find controller Library"
- Make sure the file is at `application/controllers/Library.php`
- Controller name must match filename exactly (case-sensitive)

### "Database connection error"
- Check database credentials in `application/config/database.php`
- Ensure database tables are created using the SQL schema
- Verify MySQL/MariaDB is running

### "Session not working"
- Make sure sessions are enabled in CodeIgniter config
- Check `application/config/config.php` for session settings

### Login page shows but login fails
- Make sure the database tables exist
- Check browser console for any JavaScript errors
- Verify password is correct: `password123`

## Support

For issues or questions about the library system setup, review the code comments in:
- `application/controllers/Library.php`
- `application/models/Library_model.php`

All code is well-documented with phpDoc comments and inline notes.
