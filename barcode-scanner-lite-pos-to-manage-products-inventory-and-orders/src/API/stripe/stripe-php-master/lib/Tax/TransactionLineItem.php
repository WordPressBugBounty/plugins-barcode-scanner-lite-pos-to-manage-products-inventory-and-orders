<?php


namespace Stripe\Tax;

class TransactionLineItem extends \Stripe\ApiResource
{
    const OBJECT_NAME = 'tax.transaction_line_item';

    const TAX_BEHAVIOR_EXCLUSIVE = 'exclusive';
    const TAX_BEHAVIOR_INCLUSIVE = 'inclusive';

    const TYPE_REVERSAL = 'reversal';
    const TYPE_TRANSACTION = 'transaction';
}
