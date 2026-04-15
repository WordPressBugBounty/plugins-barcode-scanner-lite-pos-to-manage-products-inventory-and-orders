<?php


namespace Stripe\Service\Forwarding;

class ForwardingServiceFactory extends \Stripe\Service\AbstractServiceFactory
{
    private static $classMap = [
        'requests' => RequestService::class,
    ];

    protected function getServiceClass($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}
