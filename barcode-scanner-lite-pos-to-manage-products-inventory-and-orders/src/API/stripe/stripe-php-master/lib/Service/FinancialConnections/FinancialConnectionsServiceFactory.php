<?php


namespace Stripe\Service\FinancialConnections;

class FinancialConnectionsServiceFactory extends \Stripe\Service\AbstractServiceFactory
{
    private static $classMap = [
        'accounts' => AccountService::class,
        'sessions' => SessionService::class,
        'transactions' => TransactionService::class,
    ];

    protected function getServiceClass($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}
