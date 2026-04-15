<?php


namespace Stripe\Treasury;

class TransactionEntry extends \Stripe\ApiResource
{
    const OBJECT_NAME = 'treasury.transaction_entry';

    const FLOW_TYPE_CREDIT_REVERSAL = 'credit_reversal';
    const FLOW_TYPE_DEBIT_REVERSAL = 'debit_reversal';
    const FLOW_TYPE_INBOUND_TRANSFER = 'inbound_transfer';
    const FLOW_TYPE_ISSUING_AUTHORIZATION = 'issuing_authorization';
    const FLOW_TYPE_OTHER = 'other';
    const FLOW_TYPE_OUTBOUND_PAYMENT = 'outbound_payment';
    const FLOW_TYPE_OUTBOUND_TRANSFER = 'outbound_transfer';
    const FLOW_TYPE_RECEIVED_CREDIT = 'received_credit';
    const FLOW_TYPE_RECEIVED_DEBIT = 'received_debit';

    const TYPE_CREDIT_REVERSAL = 'credit_reversal';
    const TYPE_CREDIT_REVERSAL_POSTING = 'credit_reversal_posting';
    const TYPE_DEBIT_REVERSAL = 'debit_reversal';
    const TYPE_INBOUND_TRANSFER = 'inbound_transfer';
    const TYPE_INBOUND_TRANSFER_RETURN = 'inbound_transfer_return';
    const TYPE_ISSUING_AUTHORIZATION_HOLD = 'issuing_authorization_hold';
    const TYPE_ISSUING_AUTHORIZATION_RELEASE = 'issuing_authorization_release';
    const TYPE_OTHER = 'other';
    const TYPE_OUTBOUND_PAYMENT = 'outbound_payment';
    const TYPE_OUTBOUND_PAYMENT_CANCELLATION = 'outbound_payment_cancellation';
    const TYPE_OUTBOUND_PAYMENT_FAILURE = 'outbound_payment_failure';
    const TYPE_OUTBOUND_PAYMENT_POSTING = 'outbound_payment_posting';
    const TYPE_OUTBOUND_PAYMENT_RETURN = 'outbound_payment_return';
    const TYPE_OUTBOUND_TRANSFER = 'outbound_transfer';
    const TYPE_OUTBOUND_TRANSFER_CANCELLATION = 'outbound_transfer_cancellation';
    const TYPE_OUTBOUND_TRANSFER_FAILURE = 'outbound_transfer_failure';
    const TYPE_OUTBOUND_TRANSFER_POSTING = 'outbound_transfer_posting';
    const TYPE_OUTBOUND_TRANSFER_RETURN = 'outbound_transfer_return';
    const TYPE_RECEIVED_CREDIT = 'received_credit';
    const TYPE_RECEIVED_DEBIT = 'received_debit';

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
