<?php


namespace Stripe\Service\TestHelpers\Issuing;

class IssuingServiceFactory extends \Stripe\Service\AbstractServiceFactory
{
    private static $classMap = [
        'authorizations' => AuthorizationService::class,
        'cards' => CardService::class,
        'personalizationDesigns' => PersonalizationDesignService::class,
        'transactions' => TransactionService::class,
    ];

    protected function getServiceClass($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}
