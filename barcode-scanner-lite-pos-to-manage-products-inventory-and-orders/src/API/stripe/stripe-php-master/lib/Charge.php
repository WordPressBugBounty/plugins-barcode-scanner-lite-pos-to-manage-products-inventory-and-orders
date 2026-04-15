<?php


namespace Stripe;

class Charge extends ApiResource
{
    const OBJECT_NAME = 'charge';

    use ApiOperations\NestedResource;
    use ApiOperations\Update;

    const STATUS_FAILED = 'failed';
    const STATUS_PENDING = 'pending';
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

    const DECLINED_AUTHENTICATION_REQUIRED = 'authentication_required';
    const DECLINED_APPROVE_WITH_ID = 'approve_with_id';
    const DECLINED_CALL_ISSUER = 'call_issuer';
    const DECLINED_CARD_NOT_SUPPORTED = 'card_not_supported';
    const DECLINED_CARD_VELOCITY_EXCEEDED = 'card_velocity_exceeded';
    const DECLINED_CURRENCY_NOT_SUPPORTED = 'currency_not_supported';
    const DECLINED_DO_NOT_HONOR = 'do_not_honor';
    const DECLINED_DO_NOT_TRY_AGAIN = 'do_not_try_again';
    const DECLINED_DUPLICATED_TRANSACTION = 'duplicate_transaction';
    const DECLINED_EXPIRED_CARD = 'expired_card';
    const DECLINED_FRAUDULENT = 'fraudulent';
    const DECLINED_GENERIC_DECLINE = 'generic_decline';
    const DECLINED_INCORRECT_NUMBER = 'incorrect_number';
    const DECLINED_INCORRECT_CVC = 'incorrect_cvc';
    const DECLINED_INCORRECT_PIN = 'incorrect_pin';
    const DECLINED_INCORRECT_ZIP = 'incorrect_zip';
    const DECLINED_INSUFFICIENT_FUNDS = 'insufficient_funds';
    const DECLINED_INVALID_ACCOUNT = 'invalid_account';
    const DECLINED_INVALID_AMOUNT = 'invalid_amount';
    const DECLINED_INVALID_CVC = 'invalid_cvc';
    const DECLINED_INVALID_EXPIRY_YEAR = 'invalid_expiry_year';
    const DECLINED_INVALID_NUMBER = 'invalid_number';
    const DECLINED_INVALID_PIN = 'invalid_pin';
    const DECLINED_ISSUER_NOT_AVAILABLE = 'issuer_not_available';
    const DECLINED_LOST_CARD = 'lost_card';
    const DECLINED_MERCHANT_BLACKLIST = 'merchant_blacklist';
    const DECLINED_NEW_ACCOUNT_INFORMATION_AVAILABLE = 'new_account_information_available';
    const DECLINED_NO_ACTION_TAKEN = 'no_action_taken';
    const DECLINED_NOT_PERMITTED = 'not_permitted';
    const DECLINED_OFFLINE_PIN_REQUIRED = 'offline_pin_required';
    const DECLINED_ONLINE_OR_OFFLINE_PIN_REQUIRED = 'online_or_offline_pin_required';
    const DECLINED_PICKUP_CARD = 'pickup_card';
    const DECLINED_PIN_TRY_EXCEEDED = 'pin_try_exceeded';
    const DECLINED_PROCESSING_ERROR = 'processing_error';
    const DECLINED_REENTER_TRANSACTION = 'reenter_transaction';
    const DECLINED_RESTRICTED_CARD = 'restricted_card';
    const DECLINED_REVOCATION_OF_ALL_AUTHORIZATIONS = 'revocation_of_all_authorizations';
    const DECLINED_REVOCATION_OF_AUTHORIZATION = 'revocation_of_authorization';
    const DECLINED_SECURITY_VIOLATION = 'security_violation';
    const DECLINED_SERVICE_NOT_ALLOWED = 'service_not_allowed';
    const DECLINED_STOLEN_CARD = 'stolen_card';
    const DECLINED_STOP_PAYMENT_ORDER = 'stop_payment_order';
    const DECLINED_TESTMODE_DECLINE = 'testmode_decline';
    const DECLINED_TRANSACTION_NOT_ALLOWED = 'transaction_not_allowed';
    const DECLINED_TRY_AGAIN_LATER = 'try_again_later';
    const DECLINED_WITHDRAWAL_COUNT_LIMIT_EXCEEDED = 'withdrawal_count_limit_exceeded';

    public function capture($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/capture';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }

    public static function search($params = null, $opts = null)
    {
        $url = '/v1/charges/search';

        return static::_requestPage($url, SearchResult::class, $params, $opts);
    }

    const PATH_REFUNDS = '/refunds';

    public static function allRefunds($id, $params = null, $opts = null)
    {
        return self::_allNestedResources($id, static::PATH_REFUNDS, $params, $opts);
    }

    public static function retrieveRefund($id, $refundId, $params = null, $opts = null)
    {
        return self::_retrieveNestedResource($id, static::PATH_REFUNDS, $refundId, $params, $opts);
    }
}
