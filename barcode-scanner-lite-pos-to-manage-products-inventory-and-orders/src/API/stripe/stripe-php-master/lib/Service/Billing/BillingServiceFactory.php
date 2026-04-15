<?php


namespace Stripe\Service\Billing;

class BillingServiceFactory extends \Stripe\Service\AbstractServiceFactory
{
    private static $classMap = [
        'alerts' => AlertService::class,
        'creditBalanceSummary' => CreditBalanceSummaryService::class,
        'creditBalanceTransactions' => CreditBalanceTransactionService::class,
        'creditGrants' => CreditGrantService::class,
        'meterEventAdjustments' => MeterEventAdjustmentService::class,
        'meterEvents' => MeterEventService::class,
        'meters' => MeterService::class,
    ];

    protected function getServiceClass($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}
