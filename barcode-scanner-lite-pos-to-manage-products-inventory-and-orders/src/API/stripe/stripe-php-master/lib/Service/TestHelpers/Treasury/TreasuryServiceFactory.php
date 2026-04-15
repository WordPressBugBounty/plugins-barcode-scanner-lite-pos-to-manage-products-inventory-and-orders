<?php


namespace Stripe\Service\TestHelpers\Treasury;

class TreasuryServiceFactory extends \Stripe\Service\AbstractServiceFactory
{
    private static $classMap = [
        'inboundTransfers' => InboundTransferService::class,
        'outboundPayments' => OutboundPaymentService::class,
        'outboundTransfers' => OutboundTransferService::class,
        'receivedCredits' => ReceivedCreditService::class,
        'receivedDebits' => ReceivedDebitService::class,
    ];

    protected function getServiceClass($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}
