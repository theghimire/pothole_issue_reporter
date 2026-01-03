# Pothole Issue Reporter 🛣️

A professional infrastructure management portal built with Laravel 11, designed to help citizens report potholes and administrative staff track and manage repairs.

## Features
- **Multilingual Support**: Fully localized in English and **नेपाली**.
- **Issue Tracking**: Citizens can report potholes with descriptions and photos.
- **Admin Dashboard**: Comprehensive management tools with status filtering and stats.
- **Anti-Fraud**: Built-in location verification comparing reported vs. captured GPS coordinates.

## How to Set Up and Run

If you have just downloaded or cloned this project, follow these steps to get it running:

### 1. Install Dependencies
Open your terminal in the project folder and run:
```bash
composer install
npm install && npm run build
```

### 2. Environment Setup
Copy the example environment file and generate a security key:
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Configuration
1. Create a database in your local environment (e.g., via phpMyAdmin).
2. Open the `.env` file and update these lines with your database details:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Run Migrations & Seeders
This will create all the necessary tables and a default admin account.
```bash
php artisan migrate --seed
```
*Note: The default admin credentials are provided in the documentation or check `database/seeders/AdminSeeder.php`.*

### 5. Start the Application
Run the built-in development server:
```bash
php artisan serve
```
Your application will be live at: `http://127.0.0.1:8000`

## Legacy Files
The original PHP-based scripts have been moved to the `/legacy` folder for historical reference.

## License
Open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
