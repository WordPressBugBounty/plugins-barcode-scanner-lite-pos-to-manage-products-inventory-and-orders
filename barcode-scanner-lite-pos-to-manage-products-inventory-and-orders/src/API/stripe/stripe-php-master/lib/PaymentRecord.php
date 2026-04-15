<?php


namespace Stripe;

class PaymentRecord extends ApiResource
{
    const OBJECT_NAME = 'payment_record';

    const CUSTOMER_PRESENCE_OFF_SESSION = 'off_session';
    const CUSTOMER_PRESENCE_ON_SESSION = 'on_session';

    const REPORTED_BY_SELF = 'self';
    const REPORTED_BY_STRIPE = 'stripe';

    public static function retrieve($id, $opts = null)
    {
        $opts = Util\RequestOptions::parse($opts);
        $instance = new static($id, $opts);
        $instance->refresh();

        return $instance;
    }

    public static function reportPayment($params = null, $opts = null)
    {
        $url = static::classUrl() . '/report_payment';
        list($response, $opts) = static::_staticRequest('post', $url, $params, $opts);
        $obj = Util\Util::convertToStripeObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }

    public function reportPaymentAttempt($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/report_payment_attempt';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }

    public function reportPaymentAttemptCanceled($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/report_payment_attempt_canceled';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }

    public function reportPaymentAttemptFailed($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/report_payment_attempt_failed';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }

    public function reportPaymentAttemptGuaranteed($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/report_payment_attempt_guaranteed';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }

    public function reportPaymentAttemptInformational($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/report_payment_attempt_informational';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }

    public function reportRefund($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/report_refund';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }
}
