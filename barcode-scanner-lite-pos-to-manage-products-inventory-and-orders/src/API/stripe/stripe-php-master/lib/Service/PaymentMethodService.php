<?php


namespace Stripe\Service;

class PaymentMethodService extends AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/payment_methods', $params, $opts);
    }

    public function attach($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/payment_methods/%s/attach', $id), $params, $opts);
    }

    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/payment_methods', $params, $opts);
    }

    public function detach($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/payment_methods/%s/detach', $id), $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/payment_methods/%s', $id), $params, $opts);
    }

    public function update($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/payment_methods/%s', $id), $params, $opts);
    }
}
