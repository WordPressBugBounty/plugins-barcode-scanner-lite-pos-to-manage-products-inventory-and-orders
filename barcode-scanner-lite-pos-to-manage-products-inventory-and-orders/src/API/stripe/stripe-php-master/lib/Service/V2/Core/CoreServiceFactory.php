<?php

namespace Stripe\Service\V2\Core;

class CoreServiceFactory extends \Stripe\Service\AbstractServiceFactory
{
    private static $classMap = [
        'accountLinks' => AccountLinkService::class,
        'accounts' => AccountService::class,
        'accountTokens' => AccountTokenService::class,
        'eventDestinations' => EventDestinationService::class,
        'events' => EventService::class,
    ];

    protected function getServiceClass($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}
