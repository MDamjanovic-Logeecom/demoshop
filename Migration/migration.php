<?php

/**
 * Stand-alone migrations master script
 */

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Bootstrap Eloquent
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOST'],
    'database' => $_ENV['DB_NAME'],
    'username' => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASS'],
    'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Include migration classes
require __DIR__ . '/migrations/CreateCategories.php';
require __DIR__ . '/migrations/CreateProducts.php';
require __DIR__ . '/migrations/CreateUsers.php';

// User to confirm running migrations
echo "This will create tables in the database `{$_ENV['DB_NAME']}`. Continue? (yes/no): ";
$handle = fopen('php://stdin', 'r');
$line = trim(fgets($handle));
if (strtolower($line) !== 'yes') {
    echo "Migration aborted.\n";
    exit;
}

// Migrations in order
echo "Running migrations...\n";

CreateCategories::up();
echo "Categories table created.\n";

CreateProducts::up();
echo "Products table created.\n";

CreateUsers::up();
echo "Users table created.\n";

echo "All migrations ran successfully.\n";

fclose($handle);

