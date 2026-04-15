<?php


namespace Stripe\Service\TestHelpers;

class TestHelpersServiceFactory extends \Stripe\Service\AbstractServiceFactory
{
    private static $classMap = [
        'confirmationTokens' => ConfirmationTokenService::class,
        'customers' => CustomerService::class,
        'issuing' => Issuing\IssuingServiceFactory::class,
        'refunds' => RefundService::class,
        'terminal' => Terminal\TerminalServiceFactory::class,
        'testClocks' => TestClockService::class,
        'treasury' => Treasury\TreasuryServiceFactory::class,
    ];

    protected function getServiceClass($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}
