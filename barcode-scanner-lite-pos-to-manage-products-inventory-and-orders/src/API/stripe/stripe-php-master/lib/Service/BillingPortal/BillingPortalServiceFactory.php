<?php


namespace Stripe\Service\BillingPortal;

class BillingPortalServiceFactory extends \Stripe\Service\AbstractServiceFactory
{
    private static $classMap = [
        'configurations' => ConfigurationService::class,
        'sessions' => SessionService::class,
    ];

    protected function getServiceClass($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}
