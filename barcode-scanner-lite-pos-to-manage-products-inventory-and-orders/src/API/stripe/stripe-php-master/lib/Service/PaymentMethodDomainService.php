<?php


namespace Stripe\Service;

class PaymentMethodDomainService extends AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/payment_method_domains', $params, $opts);
    }

    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/payment_method_domains', $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/payment_method_domains/%s', $id), $params, $opts);
    }

    public function update($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/payment_method_domains/%s', $id), $params, $opts);
    }

    public function validate($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/payment_method_domains/%s/validate', $id), $params, $opts);
    }
}
