<?php


namespace Stripe\Service\Checkout;

class CheckoutServiceFactory extends \Stripe\Service\AbstractServiceFactory
{
    private static $classMap = [
        'sessions' => SessionService::class,
    ];

    protected function getServiceClass($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}
