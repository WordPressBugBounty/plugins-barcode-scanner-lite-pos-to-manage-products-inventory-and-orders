<?php


namespace Stripe;

class CashBalance extends ApiResource
{
    const OBJECT_NAME = 'cash_balance';

    public function instanceUrl()
    {
        $customer = $this['customer'];
        $customer = Util\Util::utf8($customer);

        $base = Customer::classUrl();
        $customerExtn = \urlencode($customer);

        return "{$base}/{$customerExtn}/cash_balance";
    }

    public static function retrieve($_id, $_opts = null)
    {
        $msg = 'Customer Cash Balance cannot be retrieved without a '
               . 'customer ID. Retrieve a Customer Cash Balance using '
               . "`Customer::retrieveCashBalance('customer_id')`.";

        throw new Exception\BadMethodCallException($msg);
    }

    public static function update($_id, $_params = null, $_options = null)
    {
        $msg = 'Customer Cash Balance cannot be updated without a '
        . 'customer ID. Retrieve a Customer Cash Balance using '
        . "`Customer::updateCashBalance('customer_id')`.";

        throw new Exception\BadMethodCallException($msg);
    }
}
