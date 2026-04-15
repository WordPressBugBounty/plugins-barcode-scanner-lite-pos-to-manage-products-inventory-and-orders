<?php


namespace Stripe\Service;

class TransferService extends AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/transfers', $params, $opts);
    }

    public function allReversals($parentId, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/v1/transfers/%s/reversals', $parentId), $params, $opts);
    }

    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/transfers', $params, $opts);
    }

    public function createReversal($parentId, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/transfers/%s/reversals', $parentId), $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/transfers/%s', $id), $params, $opts);
    }

    public function retrieveReversal($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/transfers/%s/reversals/%s', $parentId, $id), $params, $opts);
    }

    public function update($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/transfers/%s', $id), $params, $opts);
    }

    public function updateReversal($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/transfers/%s/reversals/%s', $parentId, $id), $params, $opts);
    }
}
