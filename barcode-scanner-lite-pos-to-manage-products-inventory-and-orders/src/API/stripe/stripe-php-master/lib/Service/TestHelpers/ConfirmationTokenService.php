<?php


namespace Stripe\Service\TestHelpers;

class ConfirmationTokenService extends \Stripe\Service\AbstractService
{
    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/test_helpers/confirmation_tokens', $params, $opts);
    }
}
