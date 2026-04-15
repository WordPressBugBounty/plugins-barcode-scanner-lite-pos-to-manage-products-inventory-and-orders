<?php


namespace Stripe\Service;

class ApplicationFeeService extends AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/application_fees', $params, $opts);
    }

    public function allRefunds($parentId, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/v1/application_fees/%s/refunds', $parentId), $params, $opts);
    }

    public function createRefund($parentId, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/application_fees/%s/refunds', $parentId), $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/application_fees/%s', $id), $params, $opts);
    }

    public function retrieveRefund($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/application_fees/%s/refunds/%s', $parentId, $id), $params, $opts);
    }

    public function updateRefund($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/application_fees/%s/refunds/%s', $parentId, $id), $params, $opts);
    }
}
