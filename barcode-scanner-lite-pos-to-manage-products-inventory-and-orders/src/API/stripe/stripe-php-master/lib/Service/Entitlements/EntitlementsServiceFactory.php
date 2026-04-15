<?php


namespace Stripe\Service\Entitlements;

class EntitlementsServiceFactory extends \Stripe\Service\AbstractServiceFactory
{
    private static $classMap = [
        'activeEntitlements' => ActiveEntitlementService::class,
        'features' => FeatureService::class,
    ];

    protected function getServiceClass($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}
