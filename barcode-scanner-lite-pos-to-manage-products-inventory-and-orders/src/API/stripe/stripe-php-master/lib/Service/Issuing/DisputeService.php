<?php


namespace Stripe\Service\Issuing;

class DisputeService extends \Stripe\Service\AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/issuing/disputes', $params, $opts);
    }

    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/issuing/disputes', $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/issuing/disputes/%s', $id), $params, $opts);
    }

    public function submit($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/issuing/disputes/%s/submit', $id), $params, $opts);
    }

    public function update($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/issuing/disputes/%s', $id), $params, $opts);
    }
}
