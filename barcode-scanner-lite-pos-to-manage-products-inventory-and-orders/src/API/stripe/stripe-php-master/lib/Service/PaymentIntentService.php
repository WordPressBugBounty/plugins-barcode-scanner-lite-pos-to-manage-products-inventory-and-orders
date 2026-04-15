<?php


namespace Stripe\Service;

class PaymentIntentService extends AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/payment_intents', $params, $opts);
    }

    public function allAmountDetailsLineItems($parentId, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/v1/payment_intents/%s/amount_details_line_items', $parentId), $params, $opts);
    }

    public function applyCustomerBalance($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/payment_intents/%s/apply_customer_balance', $id), $params, $opts);
    }

    public function cancel($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/payment_intents/%s/cancel', $id), $params, $opts);
    }

    public function capture($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/payment_intents/%s/capture', $id), $params, $opts);
    }

    public function confirm($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/payment_intents/%s/confirm', $id), $params, $opts);
    }

    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/payment_intents', $params, $opts);
    }

    public function incrementAuthorization($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/payment_intents/%s/increment_authorization', $id), $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/payment_intents/%s', $id), $params, $opts);
    }

    public function search($params = null, $opts = null)
    {
        return $this->requestSearchResult('get', '/v1/payment_intents/search', $params, $opts);
    }

    public function update($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/payment_intents/%s', $id), $params, $opts);
    }

    public function verifyMicrodeposits($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/payment_intents/%s/verify_microdeposits', $id), $params, $opts);
    }
}
