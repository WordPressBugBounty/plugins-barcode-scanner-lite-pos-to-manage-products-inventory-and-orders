<?php


namespace Stripe\Service\FinancialConnections;

class AccountService extends \Stripe\Service\AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/financial_connections/accounts', $params, $opts);
    }

    public function allOwners($id, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/v1/financial_connections/accounts/%s/owners', $id), $params, $opts);
    }

    public function disconnect($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/financial_connections/accounts/%s/disconnect', $id), $params, $opts);
    }

    public function refresh($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/financial_connections/accounts/%s/refresh', $id), $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/financial_connections/accounts/%s', $id), $params, $opts);
    }

    public function subscribe($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/financial_connections/accounts/%s/subscribe', $id), $params, $opts);
    }

    public function unsubscribe($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/financial_connections/accounts/%s/unsubscribe', $id), $params, $opts);
    }
}
