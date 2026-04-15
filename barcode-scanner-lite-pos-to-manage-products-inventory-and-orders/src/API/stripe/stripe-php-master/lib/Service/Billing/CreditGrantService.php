<?php


namespace Stripe\Service\Billing;

class CreditGrantService extends \Stripe\Service\AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/billing/credit_grants', $params, $opts);
    }

    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/billing/credit_grants', $params, $opts);
    }

    public function expire($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/billing/credit_grants/%s/expire', $id), $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/billing/credit_grants/%s', $id), $params, $opts);
    }

    public function update($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/billing/credit_grants/%s', $id), $params, $opts);
    }

    public function voidGrant($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/billing/credit_grants/%s/void', $id), $params, $opts);
    }
}
