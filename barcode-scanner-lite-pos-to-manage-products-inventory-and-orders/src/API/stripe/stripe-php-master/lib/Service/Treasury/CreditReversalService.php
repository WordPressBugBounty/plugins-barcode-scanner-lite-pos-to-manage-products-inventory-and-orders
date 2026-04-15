<?php


namespace Stripe\Service\Treasury;

class CreditReversalService extends \Stripe\Service\AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/treasury/credit_reversals', $params, $opts);
    }

    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/treasury/credit_reversals', $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/treasury/credit_reversals/%s', $id), $params, $opts);
    }
}
