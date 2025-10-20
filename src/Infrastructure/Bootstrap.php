<?php

namespace Demoshop\Local\Infrastructure;

use Demoshop\Local\Infrastructure\DI\ServiceRegistry;
use Dotenv\Dotenv;
use PDO;

/**
 * Class Bootstrap
 *
 * Responsible for initializing the application's infrastructure, primarily
 * the ServiceRegistry. This class serves as the central bootstrap for
 * wiring services and repositories together.
 */
class Bootstrap
{
    /**
     * @var ServiceRegistry Holds the instance of the service registry
     *                       that creates all service and repository objects.
     */
    private ServiceRegistry $serviceRegistry;

    /**
     * Initializes the service registry.
     *
     * Creates a new ServiceRegistry instance, registers default services
     * and repositories, and returns the registry for use in the application.
     *
     * @return ServiceRegistry The initialized service registry instance.
     */
    public function init(): ServiceRegistry
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $pdo = $this->createPDO();

        $this->serviceRegistry = new ServiceRegistry($pdo);
        return $this->serviceRegistry;
    }

    /**
     * Creates and returns a PDO instance using environment variables (.env file).
     *
     * @return PDO
     */
    private function createPDO(): PDO
    {
        $host = $_ENV['DB_HOST'];
        $db = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASS'];
        $charset = $_ENV['DB_CHARSET'];

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        return new PDO($dsn, $user, $pass, $options);
    }
}
