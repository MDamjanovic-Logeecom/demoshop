<?php

namespace Demoshop\Local\Infrastructure\dependencyInjection;

use Demoshop\Local\Business\IProductService;
use Demoshop\Local\Business\ProductService;
use Demoshop\Local\Data\IProductRepository;
use Demoshop\Local\Data\ProductRepository;

/**
 * Class ServiceRegistry
 *
 * A simple service registry / dependency container that holds closures
 * to create service and repository objects. It supports lazy-loading
 * of services and ensures that high-level modules can depend on interfaces
 * rather than concrete implementations.
 */
class ServiceRegistry
{
    /**
     * @var array<string, callable> Stores the mapping of interface/class names
     * to closures (functions) that create concrete objects or the objects themselves.
     */
    private array $services = [];

    /**
     * ServiceRegistry constructor.
     *
     * Initializes the registry and registers the default services and repositories.
     */
    public function __construct()
    {
        $this->registerDefaults();
    }

    /**
     * Registers default service and repository closure functions.
     *
     * This method maps interface names to closures that know how to
     * instantiate the concrete implementations. Lazy loading is used,
     * so the service/repository is only created when requested.
     */
    private function registerDefaults(): void
    {
        $this->services[IProductRepository::class] = function () { // Closure function stored in asoc. array

            return new ProductRepository();                        // each time value for key called-> new obj created
        };

        $this->services[IProductService::class] = function () {
            $repository = $this->get(IProductRepository::class);

            return new ProductService($repository); // For now, returns the only concrete class there is
        };

    }

    /**
     * Returns an instantiated service or repository by interface/class name.
     *
     * If the service is registered as a closure, it is executed once and
     * replaced by its result for future calls (singleton behavior).
     *
     * @param string $className Fully qualified interface or class name
     *
     * @return callable|object The instantiated service or repository
     */
    public function get(string $className): callable|object
    {
        if (!isset($this->services[$className])) {
            throw new \RuntimeException("Class not registered: {$className}");
        }

        $service = $this->services[$className];

        // If closure function is in array, replace it with the value got from the closure
        if (is_callable($service)) {
            $this->services[$className] = $service = $service();
        }

        return $service;
    }
}