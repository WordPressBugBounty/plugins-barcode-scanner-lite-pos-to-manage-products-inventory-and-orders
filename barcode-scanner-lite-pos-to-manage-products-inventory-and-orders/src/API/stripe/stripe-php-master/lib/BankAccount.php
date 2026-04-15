<?php


namespace Stripe;

class BankAccount extends ApiResource
{
    const OBJECT_NAME = 'bank_account';

    public function delete($params = null, $opts = null)
    {
        self::_validateParams($params);

        $url = $this->instanceUrl();
        list($response, $opts) = $this->_request('delete', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }

    const STATUS_NEW = 'new';
    const STATUS_VALIDATED = 'validated';
    const STATUS_VERIFIED = 'verified';
    const STATUS_VERIFICATION_FAILED = 'verification_failed';
    const STATUS_ERRORED = 'errored';

    public function instanceUrl()
    {
        if ($this['customer']) {
            $base = Customer::classUrl();
            $parent = $this['customer'];
            $path = 'sources';
        } elseif ($this['account']) {
            $base = Account::classUrl();
            $parent = $this['account'];
            $path = 'external_accounts';
        } else {
            $msg = 'Bank accounts cannot be accessed without a customer ID or account ID.';

            throw new Exception\UnexpectedValueException($msg, null);
        }
        $parentExtn = \urlencode(Util\Util::utf8($parent));
        $extn = \urlencode(Util\Util::utf8($this['id']));

        return "{$base}/{$parentExtn}/{$path}/{$extn}";
    }

    public static function retrieve($_id, $_opts = null)
    {
        $msg = 'Bank accounts cannot be retrieved without a customer ID or '
               . 'an account ID. Retrieve a bank account using '
               . "`Customer::retrieveSource('customer_id', "
               . "'bank_account_id')` or `Account::retrieveExternalAccount("
               . "'account_id', 'bank_account_id')`.";

        throw new Exception\BadMethodCallException($msg);
    }

    public static function update($_id, $_params = null, $_options = null)
    {
        $msg = 'Bank accounts cannot be updated without a customer ID or an '
               . 'account ID. Update a bank account using '
               . "`Customer::updateSource('customer_id', 'bank_account_id', "
               . '$updateParams)` or `Account::updateExternalAccount('
               . "'account_id', 'bank_account_id', \$updateParams)`.";

        throw new Exception\BadMethodCallException($msg);
    }

    public function save($opts = null)
    {
        $params = $this->serializeParameters();
        if (\count($params) > 0) {
            $url = $this->instanceUrl();
            list($response, $opts) = $this->_request('post', $url, $params, $opts, ['save']);
            $this->refreshFrom($response, $opts);
        }

        return $this;
    }

    public function verify($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/verify';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }
}
