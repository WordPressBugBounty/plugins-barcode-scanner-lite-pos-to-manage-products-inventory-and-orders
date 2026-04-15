<?php


namespace Stripe\Service\V2\Core\Accounts;

class PersonTokenService extends \Stripe\Service\AbstractService
{
    public function create($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v2/core/accounts/%s/person_tokens', $id), $params, $opts);
    }

    public function retrieve($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v2/core/accounts/%s/person_tokens/%s', $parentId, $id), $params, $opts);
    }
}
