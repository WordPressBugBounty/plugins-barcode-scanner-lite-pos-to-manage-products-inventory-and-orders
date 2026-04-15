<?php


namespace Stripe\Service\V2\Core;

class AccountLinkService extends \Stripe\Service\AbstractService
{
    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v2/core/account_links', $params, $opts);
    }
}
