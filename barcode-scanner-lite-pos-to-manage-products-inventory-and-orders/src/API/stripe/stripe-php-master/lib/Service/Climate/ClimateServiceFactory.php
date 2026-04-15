<?php


namespace Stripe\Service\Climate;

class ClimateServiceFactory extends \Stripe\Service\AbstractServiceFactory
{
    private static $classMap = [
        'orders' => OrderService::class,
        'products' => ProductService::class,
        'suppliers' => SupplierService::class,
    ];

    protected function getServiceClass($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}
