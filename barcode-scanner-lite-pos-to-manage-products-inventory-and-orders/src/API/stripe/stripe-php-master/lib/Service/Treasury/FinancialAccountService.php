<?php


namespace Stripe\Service\Treasury;

class FinancialAccountService extends \Stripe\Service\AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/treasury/financial_accounts', $params, $opts);
    }

    public function close($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/treasury/financial_accounts/%s/close', $id), $params, $opts);
    }

    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/treasury/financial_accounts', $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/treasury/financial_accounts/%s', $id), $params, $opts);
    }

    public function retrieveFeatures($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/treasury/financial_accounts/%s/features', $id), $params, $opts);
    }

    public function update($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/treasury/financial_accounts/%s', $id), $params, $opts);
    }

    public function updateFeatures($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/treasury/financial_accounts/%s/features', $id), $params, $opts);
    }
}
