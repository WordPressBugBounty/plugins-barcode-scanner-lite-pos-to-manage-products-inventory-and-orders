<?php


namespace Stripe\Service\Terminal;

class TerminalServiceFactory extends \Stripe\Service\AbstractServiceFactory
{
    private static $classMap = [
        'configurations' => ConfigurationService::class,
        'connectionTokens' => ConnectionTokenService::class,
        'locations' => LocationService::class,
        'onboardingLinks' => OnboardingLinkService::class,
        'readers' => ReaderService::class,
    ];

    protected function getServiceClass($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}
