<?php


namespace Stripe\Service;

class AccountLinkService extends AbstractService
{
    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/account_links', $params, $opts);
    }
}
