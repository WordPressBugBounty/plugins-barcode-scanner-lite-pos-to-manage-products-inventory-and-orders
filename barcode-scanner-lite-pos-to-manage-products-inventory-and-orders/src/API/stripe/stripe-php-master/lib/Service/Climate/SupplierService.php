<?php


namespace Stripe\Service\Climate;

class SupplierService extends \Stripe\Service\AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/climate/suppliers', $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/climate/suppliers/%s', $id), $params, $opts);
    }
}
