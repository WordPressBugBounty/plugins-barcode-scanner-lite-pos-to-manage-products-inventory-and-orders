<?php


namespace Stripe\Service\TestHelpers\Issuing;

class PersonalizationDesignService extends \Stripe\Service\AbstractService
{
    public function activate($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/test_helpers/issuing/personalization_designs/%s/activate', $id), $params, $opts);
    }

    public function deactivate($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/test_helpers/issuing/personalization_designs/%s/deactivate', $id), $params, $opts);
    }

    public function reject($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/test_helpers/issuing/personalization_designs/%s/reject', $id), $params, $opts);
    }
}
