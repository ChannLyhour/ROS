# Restaurant System Ordering

A premium, high-performance ordering system designed for restaurants, built with Laravel and modern web technologies. This system provides a seamless experience for managing menus, tables, orders, and kitchen workflows.

## 🚀 System Features

- **Digital Menu Interface:** A responsive menu grid and category filtering for rapid order entry.
- **Table Management:** Real-time tracking of table status (Available, Occupied, Reserved).
- **Kitchen Workflows:** Integrated system for managing Kitchen Order Tickets (KOT) and preparation status.
- **Role-Based Access Control:** Granular permissions for Admins, Waiters, and Kitchen Staff.
- **Financial Reporting:** Professional PDF receipt generation and daily sales Excel exports.
- **Modern UI/UX:** A stunning "Bento-Glass" aesthetic powered by Bootstrap 5 and SASS.

## 🛠️ Technology Stack

- **Backend:** Laravel 13 (PHP 8.3+)
- **Frontend:** Bootstrap 5, SASS, Vite
- **Database:** MySQL 
- **Key Libraries:** 
  - `spatie/laravel-permission` (Auth)
  - `barryvdh/laravel-dompdf` (Invoices)
  - `maatwebsite/excel` (Reporting)

---

## 📦 Installation Guide

Follow these steps to set up the project on your local machine.

### Prerequisites

Ensure you have the following installed:
- PHP 8.3 or higher
- Composer
- Node.js & NPM
- MySQL

### Step 1: Clone the Repository

```bash
git clone https://github.com/Hour477/ROS.git
cd ROS
```

### Step 2: Automated Setup (Recommended)

The project includes a built-in setup script that handles dependencies, environment configuration, and migrations.

```bash
composer run setup
```

### Step 3: Manual Setup (Alternative)

If you prefer to run steps individually:

1. **Install Dependencies:**
   ```bash
   composer install
   npm install
   ```

2. **Environment Configuration:**
   ```bash
   cp .env.example .env
   # Update DB_DATABASE, DB_USERNAME, DB_PASSWORD in .env
   ```

3. **Key & Database Setup:**
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   ```

4. **Build Assets:**
   ```bash
   npm run dev
   ```

### Step 4: Start the Application

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`.

---

## 🔐 Default Credentials

After running the migrations and seeders (`php artisan migrate --seed`), you can log in with the following default administrator account:



> [!TIP]
> For security, please change your password immediately after your first login in the User Profile section.

---

## 📄 License 

This project is licensed under the MIT License.

**Contact:** [Hour Dev](https://github.com/Hour477)

## Developer 🧑‍🦱 : Chann Lyhour




