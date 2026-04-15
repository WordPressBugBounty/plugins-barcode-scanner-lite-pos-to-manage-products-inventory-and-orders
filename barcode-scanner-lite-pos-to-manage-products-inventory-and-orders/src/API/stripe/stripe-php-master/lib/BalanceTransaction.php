<?php


namespace Stripe;

class BalanceTransaction extends ApiResource
{
    const OBJECT_NAME = 'balance_transaction';

    const BALANCE_TYPE_ISSUING = 'issuing';
    const BALANCE_TYPE_PAYMENTS = 'payments';
    const BALANCE_TYPE_REFUND_AND_DISPUTE_PREFUNDING = 'refund_and_dispute_prefunding';

    const TYPE_ADJUSTMENT = 'adjustment';
    const TYPE_ADVANCE = 'advance';
    const TYPE_ADVANCE_FUNDING = 'advance_funding';
    const TYPE_ANTICIPATION_REPAYMENT = 'anticipation_repayment';
    const TYPE_APPLICATION_FEE = 'application_fee';
    const TYPE_APPLICATION_FEE_REFUND = 'application_fee_refund';
    const TYPE_CHARGE = 'charge';
    const TYPE_CLIMATE_ORDER_PURCHASE = 'climate_order_purchase';
    const TYPE_CLIMATE_ORDER_REFUND = 'climate_order_refund';
    const TYPE_CONNECT_COLLECTION_TRANSFER = 'connect_collection_transfer';
    const TYPE_CONTRIBUTION = 'contribution';
    const TYPE_ISSUING_AUTHORIZATION_HOLD = 'issuing_authorization_hold';
    const TYPE_ISSUING_AUTHORIZATION_RELEASE = 'issuing_authorization_release';
    const TYPE_ISSUING_DISPUTE = 'issuing_dispute';
    const TYPE_ISSUING_TRANSACTION = 'issuing_transaction';
    const TYPE_OBLIGATION_OUTBOUND = 'obligation_outbound';
    const TYPE_OBLIGATION_REVERSAL_INBOUND = 'obligation_reversal_inbound';
    const TYPE_PAYMENT = 'payment';
    const TYPE_PAYMENT_FAILURE_REFUND = 'payment_failure_refund';
    const TYPE_PAYMENT_NETWORK_RESERVE_HOLD = 'payment_network_reserve_hold';
    const TYPE_PAYMENT_NETWORK_RESERVE_RELEASE = 'payment_network_reserve_release';
    const TYPE_PAYMENT_REFUND = 'payment_refund';
    const TYPE_PAYMENT_REVERSAL = 'payment_reversal';
    const TYPE_PAYMENT_UNRECONCILED = 'payment_unreconciled';
    const TYPE_PAYOUT = 'payout';
    const TYPE_PAYOUT_CANCEL = 'payout_cancel';
    const TYPE_PAYOUT_FAILURE = 'payout_failure';
    const TYPE_PAYOUT_MINIMUM_BALANCE_HOLD = 'payout_minimum_balance_hold';
    const TYPE_PAYOUT_MINIMUM_BALANCE_RELEASE = 'payout_minimum_balance_release';
    const TYPE_REFUND = 'refund';
    const TYPE_REFUND_FAILURE = 'refund_failure';
    const TYPE_RESERVED_FUNDS = 'reserved_funds';
    const TYPE_RESERVE_TRANSACTION = 'reserve_transaction';
    const TYPE_STRIPE_BALANCE_PAYMENT_DEBIT = 'stripe_balance_payment_debit';
    const TYPE_STRIPE_BALANCE_PAYMENT_DEBIT_REVERSAL = 'stripe_balance_payment_debit_reversal';
    const TYPE_STRIPE_FEE = 'stripe_fee';
    const TYPE_STRIPE_FX_FEE = 'stripe_fx_fee';
    const TYPE_TAX_FEE = 'tax_fee';
    const TYPE_TOPUP = 'topup';
    const TYPE_TOPUP_REVERSAL = 'topup_reversal';
    const TYPE_TRANSFER = 'transfer';
    const TYPE_TRANSFER_CANCEL = 'transfer_cancel';
    const TYPE_TRANSFER_FAILURE = 'transfer_failure';
    const TYPE_TRANSFER_REFUND = 'transfer_refund';

    public static function all($params = null, $opts = null)
    {
        $url = static::classUrl();

        return static::_requestPage($url, Collection::class, $params, $opts);
    }

    public static function retrieve($id, $opts = null)
    {
        $opts = Util\RequestOptions::parse($opts);
        $instance = new static($id, $opts);
        $instance->refresh();

        return $instance;
    }
}
