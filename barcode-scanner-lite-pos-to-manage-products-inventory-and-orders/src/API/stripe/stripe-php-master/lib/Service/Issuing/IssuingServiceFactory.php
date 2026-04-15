<?php


namespace Stripe\Service\Issuing;

class IssuingServiceFactory extends \Stripe\Service\AbstractServiceFactory
{
    private static $classMap = [
        'authorizations' => AuthorizationService::class,
        'cardholders' => CardholderService::class,
        'cards' => CardService::class,
        'disputes' => DisputeService::class,
        'personalizationDesigns' => PersonalizationDesignService::class,
        'physicalBundles' => PhysicalBundleService::class,
        'tokens' => TokenService::class,
        'transactions' => TransactionService::class,
    ];

    protected function getServiceClass($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}
