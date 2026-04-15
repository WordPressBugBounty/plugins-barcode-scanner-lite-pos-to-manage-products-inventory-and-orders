<?php


namespace Stripe\Service\V2\Core\Accounts;

class PersonService extends \Stripe\Service\AbstractService
{
    public function all($id, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/v2/core/accounts/%s/persons', $id), $params, $opts);
    }

    public function create($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v2/core/accounts/%s/persons', $id), $params, $opts);
    }

    public function delete($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/v2/core/accounts/%s/persons/%s', $parentId, $id), $params, $opts);
    }

    public function retrieve($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v2/core/accounts/%s/persons/%s', $parentId, $id), $params, $opts);
    }

    public function update($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v2/core/accounts/%s/persons/%s', $parentId, $id), $params, $opts);
    }
}
