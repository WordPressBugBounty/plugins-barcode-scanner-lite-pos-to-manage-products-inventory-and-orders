<?php


namespace Stripe\Service\Issuing;

class AuthorizationService extends \Stripe\Service\AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/issuing/authorizations', $params, $opts);
    }

    public function approve($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/issuing/authorizations/%s/approve', $id), $params, $opts);
    }

    public function decline($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/issuing/authorizations/%s/decline', $id), $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/issuing/authorizations/%s', $id), $params, $opts);
    }

    public function update($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/issuing/authorizations/%s', $id), $params, $opts);
    }
}
