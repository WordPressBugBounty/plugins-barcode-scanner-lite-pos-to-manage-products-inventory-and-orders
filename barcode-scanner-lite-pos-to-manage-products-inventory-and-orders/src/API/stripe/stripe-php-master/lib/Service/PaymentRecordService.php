<?php


namespace Stripe\Service;

class PaymentRecordService extends AbstractService
{
    public function reportPayment($params = null, $opts = null)
    {
        return $this->request('post', '/v1/payment_records/report_payment', $params, $opts);
    }

    public function reportPaymentAttempt($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/payment_records/%s/report_payment_attempt', $id), $params, $opts);
    }

    public function reportPaymentAttemptCanceled($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/payment_records/%s/report_payment_attempt_canceled', $id), $params, $opts);
    }

    public function reportPaymentAttemptFailed($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/payment_records/%s/report_payment_attempt_failed', $id), $params, $opts);
    }

    public function reportPaymentAttemptGuaranteed($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/payment_records/%s/report_payment_attempt_guaranteed', $id), $params, $opts);
    }

    public function reportPaymentAttemptInformational($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/payment_records/%s/report_payment_attempt_informational', $id), $params, $opts);
    }

    public function reportRefund($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/payment_records/%s/report_refund', $id), $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/payment_records/%s', $id), $params, $opts);
    }
}
