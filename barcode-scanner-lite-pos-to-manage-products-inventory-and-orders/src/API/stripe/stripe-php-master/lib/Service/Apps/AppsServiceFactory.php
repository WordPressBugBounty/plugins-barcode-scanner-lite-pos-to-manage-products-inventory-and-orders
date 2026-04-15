<?php


namespace Stripe\Service\Apps;

class AppsServiceFactory extends \Stripe\Service\AbstractServiceFactory
{
    private static $classMap = [
        'secrets' => SecretService::class,
    ];

    protected function getServiceClass($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}
