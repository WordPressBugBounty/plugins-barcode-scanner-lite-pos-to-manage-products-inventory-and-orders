<?php


namespace Stripe\Service\TestHelpers\Terminal;

class TerminalServiceFactory extends \Stripe\Service\AbstractServiceFactory
{
    private static $classMap = [
        'readers' => ReaderService::class,
    ];

    protected function getServiceClass($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}
