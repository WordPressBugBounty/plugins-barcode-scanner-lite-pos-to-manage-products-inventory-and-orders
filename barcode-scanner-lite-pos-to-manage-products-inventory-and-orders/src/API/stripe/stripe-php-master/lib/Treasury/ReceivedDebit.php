<?php


namespace Stripe\Treasury;

class ReceivedDebit extends \Stripe\ApiResource
{
    const OBJECT_NAME = 'treasury.received_debit';

    const FAILURE_CODE_ACCOUNT_CLOSED = 'account_closed';
    const FAILURE_CODE_ACCOUNT_FROZEN = 'account_frozen';
    const FAILURE_CODE_INSUFFICIENT_FUNDS = 'insufficient_funds';
    const FAILURE_CODE_INTERNATIONAL_TRANSACTION = 'international_transaction';
    const FAILURE_CODE_OTHER = 'other';

    const NETWORK_ACH = 'ach';
    const NETWORK_CARD = 'card';
    const NETWORK_STRIPE = 'stripe';

    const STATUS_FAILED = 'failed';
    const STATUS_SUCCEEDED = 'succeeded';

    public static function all($params = null, $opts = null)
    {
        $url = static::classUrl();

        return static::_requestPage($url, \Stripe\Collection::class, $params, $opts);
    }

    public static function retrieve($id, $opts = null)
    {
        $opts = \Stripe\Util\RequestOptions::parse($opts);
        $instance = new static($id, $opts);
        $instance->refresh();

        return $instance;
    }
}
