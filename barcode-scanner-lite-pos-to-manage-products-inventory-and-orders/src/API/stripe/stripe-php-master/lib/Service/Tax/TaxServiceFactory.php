<?php


namespace Stripe\Service\Tax;

class TaxServiceFactory extends \Stripe\Service\AbstractServiceFactory
{
    private static $classMap = [
        'associations' => AssociationService::class,
        'calculations' => CalculationService::class,
        'registrations' => RegistrationService::class,
        'settings' => SettingsService::class,
        'transactions' => TransactionService::class,
    ];

    protected function getServiceClass($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}
