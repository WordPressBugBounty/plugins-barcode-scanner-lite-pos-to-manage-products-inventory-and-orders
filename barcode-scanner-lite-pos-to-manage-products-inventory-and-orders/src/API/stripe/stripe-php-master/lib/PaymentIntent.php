<?php


namespace Stripe;

class PaymentIntent extends ApiResource
{
    const OBJECT_NAME = 'payment_intent';

    use ApiOperations\NestedResource;
    use ApiOperations\Update;

    const CANCELLATION_REASON_ABANDONED = 'abandoned';
    const CANCELLATION_REASON_AUTOMATIC = 'automatic';
    const CANCELLATION_REASON_DUPLICATE = 'duplicate';
    const CANCELLATION_REASON_EXPIRED = 'expired';
    const CANCELLATION_REASON_FAILED_INVOICE = 'failed_invoice';
    const CANCELLATION_REASON_FRAUDULENT = 'fraudulent';
    const CANCELLATION_REASON_REQUESTED_BY_CUSTOMER = 'requested_by_customer';
    const CANCELLATION_REASON_VOID_INVOICE = 'void_invoice';

    const CAPTURE_METHOD_AUTOMATIC = 'automatic';
    const CAPTURE_METHOD_AUTOMATIC_ASYNC = 'automatic_async';
    const CAPTURE_METHOD_MANUAL = 'manual';

    const CONFIRMATION_METHOD_AUTOMATIC = 'automatic';
    const CONFIRMATION_METHOD_MANUAL = 'manual';

    const SETUP_FUTURE_USAGE_OFF_SESSION = 'off_session';
    const SETUP_FUTURE_USAGE_ON_SESSION = 'on_session';

    const STATUS_CANCELED = 'canceled';
    const STATUS_PROCESSING = 'processing';
    const STATUS_REQUIRES_ACTION = 'requires_action';
    const STATUS_REQUIRES_CAPTURE = 'requires_capture';
    const STATUS_REQUIRES_CONFIRMATION = 'requires_confirmation';
    const STATUS_REQUIRES_PAYMENT_METHOD = 'requires_payment_method';
    const STATUS_SUCCEEDED = 'succeeded';

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

    public function applyCustomerBalance($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/apply_customer_balance';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }

    public function cancel($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/cancel';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }

    public function capture($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/capture';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }

    public function confirm($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/confirm';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }

    public function incrementAuthorization($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/increment_authorization';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }

    public function verifyMicrodeposits($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/verify_microdeposits';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }

    public static function search($params = null, $opts = null)
    {
        $url = '/v1/payment_intents/search';

        return static::_requestPage($url, SearchResult::class, $params, $opts);
    }

    const PATH_AMOUNT_DETAILS_LINE_ITEMS = '/amount_details_line_items';

    public static function allAmountDetailsLineItems($id, $params = null, $opts = null)
    {
        return self::_allNestedResources($id, static::PATH_AMOUNT_DETAILS_LINE_ITEMS, $params, $opts);
    }
}
