<?php

namespace Demoshop\Local\Infrastructure\DI;

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
     * Registers class names and the closures that return the class object
     *
     * @param string $className of the wanted class
     * @param callable $factory closure that creates the class object
     *
     * @return void
     */
    public function register(string $className, callable $factory): void
    {
        $this->services[$className] = $factory;
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

        // If closure function is in array, replace it with the value got from that closure
        if (is_callable($service)) {
            $service = $service();
            $this->services[$className] = $service;
        }

        return $service;
    }
}
