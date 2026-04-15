<?php


namespace Stripe\Service\Tax;

class TransactionService extends \Stripe\Service\AbstractService
{
    public function allLineItems($id, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/v1/tax/transactions/%s/line_items', $id), $params, $opts);
    }

    public function createFromCalculation($params = null, $opts = null)
    {
        return $this->request('post', '/v1/tax/transactions/create_from_calculation', $params, $opts);
    }

    public function createReversal($params = null, $opts = null)
    {
        return $this->request('post', '/v1/tax/transactions/create_reversal', $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/tax/transactions/%s', $id), $params, $opts);
    }
}
