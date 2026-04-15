<?php


namespace Stripe\Service;

class AccountService extends AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/accounts', $params, $opts);
    }

    public function allCapabilities($parentId, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/v1/accounts/%s/capabilities', $parentId), $params, $opts);
    }

    public function allExternalAccounts($parentId, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/v1/accounts/%s/external_accounts', $parentId), $params, $opts);
    }

    public function allPersons($parentId, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/v1/accounts/%s/persons', $parentId), $params, $opts);
    }

    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/accounts', $params, $opts);
    }

    public function createExternalAccount($parentId, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/accounts/%s/external_accounts', $parentId), $params, $opts);
    }

    public function createLoginLink($parentId, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/accounts/%s/login_links', $parentId), $params, $opts);
    }

    public function createPerson($parentId, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/accounts/%s/persons', $parentId), $params, $opts);
    }

    public function delete($id, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/v1/accounts/%s', $id), $params, $opts);
    }

    public function deleteExternalAccount($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/v1/accounts/%s/external_accounts/%s', $parentId, $id), $params, $opts);
    }

    public function deletePerson($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/v1/accounts/%s/persons/%s', $parentId, $id), $params, $opts);
    }

    public function reject($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/accounts/%s/reject', $id), $params, $opts);
    }

    public function retrieveCapability($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/accounts/%s/capabilities/%s', $parentId, $id), $params, $opts);
    }

    public function retrieveExternalAccount($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/accounts/%s/external_accounts/%s', $parentId, $id), $params, $opts);
    }

    public function retrievePerson($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/accounts/%s/persons/%s', $parentId, $id), $params, $opts);
    }

    public function update($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/accounts/%s', $id), $params, $opts);
    }

    public function updateCapability($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/accounts/%s/capabilities/%s', $parentId, $id), $params, $opts);
    }

    public function updateExternalAccount($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/accounts/%s/external_accounts/%s', $parentId, $id), $params, $opts);
    }

    public function updatePerson($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/accounts/%s/persons/%s', $parentId, $id), $params, $opts);
    }

    public function retrieve($id = null, $params = null, $opts = null)
    {
        if (null === $id) {
            return $this->request('get', '/v1/account', $params, $opts);
        }

        return $this->request('get', $this->buildPath('/v1/accounts/%s', $id), $params, $opts);
    }
}
