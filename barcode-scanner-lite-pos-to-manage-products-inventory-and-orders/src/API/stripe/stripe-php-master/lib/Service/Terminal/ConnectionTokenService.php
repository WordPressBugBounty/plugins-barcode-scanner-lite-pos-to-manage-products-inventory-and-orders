<?php


namespace Stripe\Service\Terminal;

class ConnectionTokenService extends \Stripe\Service\AbstractService
{
    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/terminal/connection_tokens', $params, $opts);
    }
}
