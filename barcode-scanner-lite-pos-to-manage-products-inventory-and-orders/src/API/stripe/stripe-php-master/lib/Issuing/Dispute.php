<?php


namespace Stripe\Issuing;

class Dispute extends \Stripe\ApiResource
{
    const OBJECT_NAME = 'issuing.dispute';

    use \Stripe\ApiOperations\Update;

    const LOSS_REASON_CARDHOLDER_AUTHENTICATION_ISSUER_LIABILITY = 'cardholder_authentication_issuer_liability';
    const LOSS_REASON_ECI5_TOKEN_TRANSACTION_WITH_TAVV = 'eci5_token_transaction_with_tavv';
    const LOSS_REASON_EXCESS_DISPUTES_IN_TIMEFRAME = 'excess_disputes_in_timeframe';
    const LOSS_REASON_HAS_NOT_MET_THE_MINIMUM_DISPUTE_AMOUNT_REQUIREMENTS = 'has_not_met_the_minimum_dispute_amount_requirements';
    const LOSS_REASON_INVALID_DUPLICATE_DISPUTE = 'invalid_duplicate_dispute';
    const LOSS_REASON_INVALID_INCORRECT_AMOUNT_DISPUTE = 'invalid_incorrect_amount_dispute';
    const LOSS_REASON_INVALID_NO_AUTHORIZATION = 'invalid_no_authorization';
    const LOSS_REASON_INVALID_USE_OF_DISPUTES = 'invalid_use_of_disputes';
    const LOSS_REASON_MERCHANDISE_DELIVERED_OR_SHIPPED = 'merchandise_delivered_or_shipped';
    const LOSS_REASON_MERCHANDISE_OR_SERVICE_AS_DESCRIBED = 'merchandise_or_service_as_described';
    const LOSS_REASON_NOT_CANCELLED = 'not_cancelled';
    const LOSS_REASON_OTHER = 'other';
    const LOSS_REASON_REFUND_ISSUED = 'refund_issued';
    const LOSS_REASON_SUBMITTED_BEYOND_ALLOWABLE_TIME_LIMIT = 'submitted_beyond_allowable_time_limit';
    const LOSS_REASON_TRANSACTION_3DS_REQUIRED = 'transaction_3ds_required';
    const LOSS_REASON_TRANSACTION_APPROVED_AFTER_PRIOR_FRAUD_DISPUTE = 'transaction_approved_after_prior_fraud_dispute';
    const LOSS_REASON_TRANSACTION_AUTHORIZED = 'transaction_authorized';
    const LOSS_REASON_TRANSACTION_ELECTRONICALLY_READ = 'transaction_electronically_read';
    const LOSS_REASON_TRANSACTION_QUALIFIES_FOR_VISA_EASY_PAYMENT_SERVICE = 'transaction_qualifies_for_visa_easy_payment_service';
    const LOSS_REASON_TRANSACTION_UNATTENDED = 'transaction_unattended';

    const STATUS_EXPIRED = 'expired';
    const STATUS_LOST = 'lost';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_UNSUBMITTED = 'unsubmitted';
    const STATUS_WON = 'won';

    public static function create($params = null, $options = null)
    {
        self::_validateParams($params);
        $url = static::classUrl();

        list($response, $opts) = static::_staticRequest('post', $url, $params, $options);
        $obj = \Stripe\Util\Util::convertToStripeObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }

    public static function all($params = null, $opts = null)
    {
        $url = static::classUrl();

        return static::_requestPage($url, \Stripe\Collection::class, $params, $opts);
    }

    public static function retrieve($id, $opts = null)
    {
        $opts = \Stripe\Util\RequestOptions::parse($opts);
        $instance = new static($id, $opts);
        $instance->refresh();

        return $instance;
    }

    public static function update($id, $params = null, $opts = null)
    {
        self::_validateParams($params);
        $url = static::resourceUrl($id);

        list($response, $opts) = static::_staticRequest('post', $url, $params, $opts);
        $obj = \Stripe\Util\Util::convertToStripeObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }

    public function submit($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/submit';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }
}
