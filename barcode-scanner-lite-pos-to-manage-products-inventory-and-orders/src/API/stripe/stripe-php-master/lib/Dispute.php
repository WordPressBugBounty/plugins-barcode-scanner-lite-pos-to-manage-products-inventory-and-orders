<?php


namespace Stripe;

class Dispute extends ApiResource
{
    const OBJECT_NAME = 'dispute';

    use ApiOperations\Update;

    const REASON_BANK_CANNOT_PROCESS = 'bank_cannot_process';
    const REASON_CHECK_RETURNED = 'check_returned';
    const REASON_CREDIT_NOT_PROCESSED = 'credit_not_processed';
    const REASON_CUSTOMER_INITIATED = 'customer_initiated';
    const REASON_DEBIT_NOT_AUTHORIZED = 'debit_not_authorized';
    const REASON_DUPLICATE = 'duplicate';
    const REASON_FRAUDULENT = 'fraudulent';
    const REASON_GENERAL = 'general';
    const REASON_INCORRECT_ACCOUNT_DETAILS = 'incorrect_account_details';
    const REASON_INSUFFICIENT_FUNDS = 'insufficient_funds';
    const REASON_PRODUCT_NOT_RECEIVED = 'product_not_received';
    const REASON_PRODUCT_UNACCEPTABLE = 'product_unacceptable';
    const REASON_SUBSCRIPTION_CANCELED = 'subscription_canceled';
    const REASON_UNRECOGNIZED = 'unrecognized';

    const STATUS_LOST = 'lost';
    const STATUS_NEEDS_RESPONSE = 'needs_response';
    const STATUS_PREVENTED = 'prevented';
    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_WARNING_CLOSED = 'warning_closed';
    const STATUS_WARNING_NEEDS_RESPONSE = 'warning_needs_response';
    const STATUS_WARNING_UNDER_REVIEW = 'warning_under_review';
    const STATUS_WON = 'won';

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

    public function close($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/close';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }
}
