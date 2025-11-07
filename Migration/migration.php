<?php
/**
 * Stand-alone migrations master script
 */
require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Does database exist before connecting?
$host = $_ENV['DB_HOST'];
$dbName = $_ENV['DB_NAME'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];
$charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
$collation = 'utf8mb4_unicode_ci';

try {
    // Connect to MySQL without specifying a database
    $pdo = new PDO("mysql:host=$host;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET $charset COLLATE $collation");
    echo "Database `$dbName` is ready.\n";

} catch (PDOException $e) {
    echo "Failed to connect or create database: " . $e->getMessage() . "\n";
    exit(1);
}

// DB exists, create the connection
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $host,
    'database' => $dbName,
    'username' => $user,
    'password' => $pass,
    'charset' => $charset,
    'collation' => $collation,
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

echo "Running migrations...\n";

// Helper function for table creation with existence check
function runMigrationIfMissing(string $tableName, callable $migration): void
{
    if (Capsule::schema()->hasTable($tableName)) {
        echo "Table `$tableName` already exists. Skipping...\n";
    } else {
        $migration();
        echo "Table `$tableName` created successfully.\n";
    }
}

runMigrationIfMissing('categories', [CreateCategories::class, 'up']);
runMigrationIfMissing('products', [CreateProducts::class, 'up']);
runMigrationIfMissing('users', [CreateUsers::class, 'up']);

echo "All migrations ran successfully.\n";

fclose($handle);

