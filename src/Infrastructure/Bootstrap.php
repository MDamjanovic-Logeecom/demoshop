<?php

namespace Demoshop\Local\Infrastructure;

use Demoshop\Local\Infrastructure\DI\ServiceRegistry;
use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;

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

        $this->bootEloquent();

        $this->serviceRegistry = new ServiceRegistry();
        return $this->serviceRegistry;
    }

    /**
     * Initializes Eloquent ORM using environment variables.
     */
    private function bootEloquent(): void
    {
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
    }
}
