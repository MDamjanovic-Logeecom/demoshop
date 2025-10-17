<?php

namespace Demoshop\Local\Infrastructure\dependencyInjection;

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
        $this->serviceRegistry = new ServiceRegistry();
        return $this->serviceRegistry;
    }
}