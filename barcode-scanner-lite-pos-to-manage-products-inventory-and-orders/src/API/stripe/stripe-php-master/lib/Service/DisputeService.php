<?php


namespace Stripe\Service;

class DisputeService extends AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/disputes', $params, $opts);
    }

    public function close($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/disputes/%s/close', $id), $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/disputes/%s', $id), $params, $opts);
    }

    public function update($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/disputes/%s', $id), $params, $opts);
    }
}
