# E-Commerce Website in PHP

A full-featured e-commerce platform built with native PHP, MySQL, and Bootstrap. This project includes essential features for both customers and administrators, such as user management, a product catalog, a shopping cart, order processing, and an admin dashboard.

## Features

### Customer-Facing

*   **User Authentication**: Secure registration and login system for customers.
*   **Password Recovery**: SMS-based password recovery functionality.
*   **Product Catalog**: Browse products by category.
*   **Search**: Find products using a search bar.
*   **Shopping Cart**: Add, update, and remove items from the cart.
*   **Wishlist**: Save products for later (for registered users).
*   **Checkout Process**: Complete billing and shipping details to place an order.
*   **Order History**: View and track past orders.
*   **Product Recommendations**: See recommended items on the homepage.
*   **Contact Page**: A contact form with Google Maps integration.

### Admin Panel

*   **Secure Admin Login**: Separate login for administrators.
*   **Dashboard**: An overview of site activity (structure exists for this).
*   **Product Management**: Add, edit, and delete products, including images and pricing.
*   **Category Management**: Manage product categories.
*   **Order Management**: View and update the status of customer orders.
*   **User Management**: (Implied) Manage customer and admin accounts.
*   **Discount Management**: Set promotional prices for products.

## Technology Stack

*   **Backend**: PHP
*   **Frontend**: HTML, CSS, JavaScript, jQuery, Bootstrap
*   **Database**: MySQL / MariaDB

## Prerequisites

*   A local web server environment like XAMPP, WAMP, or MAMP.
*   PHP (The project uses functions compatible with PHP 8.2 but should work on older versions).
*   MySQL or MariaDB database server.

## Installation and Setup

Follow these steps to get the project up and running on your local machine.

1.  **Clone the repository:**
    ```bash
    git clone <your-repository-url>
    ```

2.  **Move to your web server's root directory:**
    Place the cloned project folder inside your server's root directory (e.g., `C:/xampp/htdocs/` for XAMPP).

3.  **Database Setup:**
    *   Open your database management tool (e.g., phpMyAdmin).
    *   Create a new database and name it `db_ecommerce`.
    *   Select the new database and import the `db_ecommerce.sql` file located in the project's root directory.

4.  **Configure Database Connection:**
    *   Open the file `include/config.php`.
    *   Verify that the database credentials match your local environment. The default is set for a standard XAMPP installation.
    ```php
    defined('server') ? null : define("server", "localhost");
    defined('user') ? null : define ("user", "root") ;
    defined('pass') ? null : define("pass","");
    defined('database_name') ? null : define("database_name", "db_ecommerce") ;
    ```

5.  **Access the Application:**
    *   Open your web browser and navigate to `http://localhost/ecommerce/`.

## Admin Credentials

You can log in to the admin panel by navigating to `http://localhost/ecommerce/admin/`.

*   **Username:** `debby`
*   **Password:** `admin123`

**Note:** The passwords in the database are hashed using SHA1. To use the credentials above, you will need to insert a new user into the `tbluseraccount` table.

#### How to Add a New Admin User

You can run the following SQL query in phpMyAdmin to add an admin user with the credentials `debby` / `admin123`. The SHA1 hash for `admin123` is `01b307acba4f54f55aafc33bb06bbbf6ca803e9a`.

```sql
INSERT INTO `tbluseraccount` (`U_NAME`, `U_USERNAME`, `U_PASS`, `U_ROLE`) VALUES
('Admin User', 'debby', '01b307acba4f54f55aafc33bb06bbbf6ca803e9a', 'Administrator');
```

The database dump also contains the user `debbi111` with the password `amadi111`.

## Project Structure

The project follows a standard PHP web application structure.

```
ecommerce/
├── admin/                # Admin panel files
│   ├── products/         # Product management (Add, Edit, List)
│   ├── theme/            # Admin theme/template files
│   └── ...
├── cart/                 # Shopping cart logic
├── customer/             # Customer account and order logic
├── images/               # Site images (banners, logos, etc.)
├── include/              # Core PHP files (DB, classes, functions)
├── js/                   # JavaScript files
├── css/                  # CSS stylesheets
├── fonts/                # Font files
├── db_ecommerce.sql      # Database dump
├── index.php             # Main entry point for the website
└── ...                   # Other frontend pages (about.php, contact.php, etc.)
```

---

*This README was generated based on the project's file structure and content.*