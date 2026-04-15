<?php


namespace Stripe;

class CustomerCashBalanceTransaction extends ApiResource
{
    const OBJECT_NAME = 'customer_cash_balance_transaction';

    const TYPE_ADJUSTED_FOR_OVERDRAFT = 'adjusted_for_overdraft';
    const TYPE_APPLIED_TO_PAYMENT = 'applied_to_payment';
    const TYPE_FUNDED = 'funded';
    const TYPE_FUNDING_REVERSED = 'funding_reversed';
    const TYPE_REFUNDED_FROM_PAYMENT = 'refunded_from_payment';
    const TYPE_RETURN_CANCELED = 'return_canceled';
    const TYPE_RETURN_INITIATED = 'return_initiated';
    const TYPE_TRANSFERRED_TO_BALANCE = 'transferred_to_balance';
    const TYPE_UNAPPLIED_FROM_PAYMENT = 'unapplied_from_payment';
}
