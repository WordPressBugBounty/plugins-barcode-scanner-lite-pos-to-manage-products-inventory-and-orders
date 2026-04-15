<?php


namespace Stripe\Service\Tax;

class AssociationService extends \Stripe\Service\AbstractService
{
    public function find($params = null, $opts = null)
    {
        return $this->request('get', '/v1/tax/associations/find', $params, $opts);
    }
}
