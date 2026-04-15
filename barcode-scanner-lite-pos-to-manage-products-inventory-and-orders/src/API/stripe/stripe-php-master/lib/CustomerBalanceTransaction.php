<?php


namespace Stripe;

class CustomerBalanceTransaction extends ApiResource
{
    const OBJECT_NAME = 'customer_balance_transaction';

    const TYPE_ADJUSTMENT = 'adjustment';
    const TYPE_APPLIED_TO_INVOICE = 'applied_to_invoice';
    const TYPE_CHECKOUT_SESSION_SUBSCRIPTION_PAYMENT = 'checkout_session_subscription_payment';
    const TYPE_CHECKOUT_SESSION_SUBSCRIPTION_PAYMENT_CANCELED = 'checkout_session_subscription_payment_canceled';
    const TYPE_CREDIT_NOTE = 'credit_note';
    const TYPE_INITIAL = 'initial';
    const TYPE_INVOICE_OVERPAID = 'invoice_overpaid';
    const TYPE_INVOICE_TOO_LARGE = 'invoice_too_large';
    const TYPE_INVOICE_TOO_SMALL = 'invoice_too_small';
    const TYPE_UNSPENT_RECEIVER_CREDIT = 'unspent_receiver_credit';

    const TYPE_ADJUSTEMENT = 'adjustment';

    public function instanceUrl()
    {
        $id = $this['id'];
        $customer = $this['customer'];
        if (!$id) {
            throw new Exception\UnexpectedValueException(
                "Could not determine which URL to request: class instance has invalid ID: {$id}",
                null
            );
        }
        $id = Util\Util::utf8($id);
        $customer = Util\Util::utf8($customer);

        $base = Customer::classUrl();
        $customerExtn = \urlencode($customer);
        $extn = \urlencode($id);

        return "{$base}/{$customerExtn}/balance_transactions/{$extn}";
    }

    public static function retrieve($_id, $_opts = null)
    {
        $msg = 'Customer Balance Transactions cannot be retrieved without a '
               . 'customer ID. Retrieve a Customer Balance Transaction using '
               . "`Customer::retrieveBalanceTransaction('customer_id', "
               . "'balance_transaction_id')`.";

        throw new Exception\BadMethodCallException($msg);
    }

    public static function update($_id, $_params = null, $_options = null)
    {
        $msg = 'Customer Balance Transactions cannot be updated without a '
               . 'customer ID. Update a Customer Balance Transaction using '
               . "`Customer::updateBalanceTransaction('customer_id', "
               . "'balance_transaction_id', \$updateParams)`.";

        throw new Exception\BadMethodCallException($msg);
    }
}
