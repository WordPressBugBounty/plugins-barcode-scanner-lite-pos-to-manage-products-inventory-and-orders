<?php


namespace Stripe\Service\FinancialConnections;

class SessionService extends \Stripe\Service\AbstractService
{
    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/financial_connections/sessions', $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/financial_connections/sessions/%s', $id), $params, $opts);
    }
}
