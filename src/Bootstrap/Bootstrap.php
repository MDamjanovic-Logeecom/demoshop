<?php

namespace Demoshop\Local\Bootstrap;

use Demoshop\Local\Business\IProductService;
use Demoshop\Local\Business\ProductService;
use Demoshop\Local\Data\IProductRepository;
use Demoshop\Local\Data\ProductRepository;
use Demoshop\Local\Infrastructure\DI\ServiceRegistry;
use Demoshop\Local\Infrastructure\http\HttpRequest;
use Demoshop\Local\Presentation\controllers\ProductController;
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
    private ServiceRegistry $registry;

    /**
     * Constructor
     *
     * @param ServiceRegistry $registry which bootstrap will fill with concrete classes
     */
    public function __construct(ServiceRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Initializes the service registry.
     *
     * Creates a new ServiceRegistry instance, registers default services
     * and repositories, and returns the registry for use in the application.
     */
    public function init(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $this->bootEloquent();
        $this->initServices();
        $this->initControllers();
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

    /**
     * Initializes and places service class factory functions into the registry
     */
    private function initServices(): void
    {
        $this->registry->register(HttpRequest::class, fn() => new HttpRequest());
        $this->registry->register(IProductRepository::class, fn() => new ProductRepository());
        $this->registry->register(IProductService::class, fn() =>
        new ProductService($this->registry->get(IProductRepository::class))
        );
    }

    /**
     * Initializes and places controller class factory functions into the registry
     */
    private function initControllers(): void
    {
        $this->registry->register(ProductController::class, fn() =>
        new ProductController($this->registry->get(IProductService::class))
        );
    }
}
