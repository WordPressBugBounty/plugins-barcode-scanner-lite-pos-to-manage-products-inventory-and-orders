<?php


namespace Stripe\Service\V2;

class V2ServiceFactory extends \Stripe\Service\AbstractServiceFactory
{
    private static $classMap = [
        'billing' => Billing\BillingServiceFactory::class,
        'core' => Core\CoreServiceFactory::class,
    ];

    protected function getServiceClass($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}
