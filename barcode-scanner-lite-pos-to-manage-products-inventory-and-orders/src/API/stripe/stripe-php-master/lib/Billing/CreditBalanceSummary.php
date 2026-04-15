<?php


namespace Stripe\Billing;

class CreditBalanceSummary extends \Stripe\SingletonApiResource
{
    const OBJECT_NAME = 'billing.credit_balance_summary';

    public static function retrieve($opts = null)
    {
        $opts = \Stripe\Util\RequestOptions::parse($opts);
        $instance = new static(null, $opts);
        $instance->refresh();

        return $instance;
    }
}
