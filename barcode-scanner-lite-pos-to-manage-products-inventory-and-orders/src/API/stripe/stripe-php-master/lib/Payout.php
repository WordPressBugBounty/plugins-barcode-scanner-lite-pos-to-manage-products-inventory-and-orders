<?php


namespace Stripe;

class Payout extends ApiResource
{
    const OBJECT_NAME = 'payout';

    use ApiOperations\Update;

    const METHOD_INSTANT = 'instant';
    const METHOD_STANDARD = 'standard';

    const RECONCILIATION_STATUS_COMPLETED = 'completed';
    const RECONCILIATION_STATUS_IN_PROGRESS = 'in_progress';
    const RECONCILIATION_STATUS_NOT_APPLICABLE = 'not_applicable';

    const STATUS_CANCELED = 'canceled';
    const STATUS_FAILED = 'failed';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_PAID = 'paid';
    const STATUS_PENDING = 'pending';

    const TYPE_BANK_ACCOUNT = 'bank_account';
    const TYPE_CARD = 'card';

    public static function create($params = null, $options = null)
    {
        self::_validateParams($params);
        $url = static::classUrl();

        list($response, $opts) = static::_staticRequest('post', $url, $params, $options);
        $obj = Util\Util::convertToStripeObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }

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

    public static function update($id, $params = null, $opts = null)
    {
        self::_validateParams($params);
        $url = static::resourceUrl($id);

        list($response, $opts) = static::_staticRequest('post', $url, $params, $opts);
        $obj = Util\Util::convertToStripeObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }

    const FAILURE_ACCOUNT_CLOSED = 'account_closed';
    const FAILURE_ACCOUNT_FROZEN = 'account_frozen';
    const FAILURE_BANK_ACCOUNT_RESTRICTED = 'bank_account_restricted';
    const FAILURE_BANK_OWNERSHIP_CHANGED = 'bank_ownership_changed';
    const FAILURE_COULD_NOT_PROCESS = 'could_not_process';
    const FAILURE_DEBIT_NOT_AUTHORIZED = 'debit_not_authorized';
    const FAILURE_DECLINED = 'declined';
    const FAILURE_INCORRECT_ACCOUNT_HOLDER_ADDRESS = 'incorrect_account_holder_address';
    const FAILURE_INCORRECT_ACCOUNT_HOLDER_NAME = 'incorrect_account_holder_name';
    const FAILURE_INCORRECT_ACCOUNT_HOLDER_TAX_ID = 'incorrect_account_holder_tax_id';
    const FAILURE_INSUFFICIENT_FUNDS = 'insufficient_funds';
    const FAILURE_INVALID_ACCOUNT_NUMBER = 'invalid_account_number';
    const FAILURE_INVALID_CURRENCY = 'invalid_currency';
    const FAILURE_NO_ACCOUNT = 'no_account';
    const FAILURE_UNSUPPORTED_CARD = 'unsupported_card';

    public function cancel($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/cancel';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }

    public function reverse($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/reverse';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }
}
