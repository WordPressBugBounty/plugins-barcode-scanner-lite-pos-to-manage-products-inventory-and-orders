<?php


namespace Stripe\Service;

class ConfirmationTokenService extends AbstractService
{
    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/confirmation_tokens/%s', $id), $params, $opts);
    }
}
