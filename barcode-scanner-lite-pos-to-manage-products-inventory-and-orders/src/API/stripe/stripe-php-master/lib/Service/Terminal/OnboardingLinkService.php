<?php


namespace Stripe\Service\Terminal;

class OnboardingLinkService extends \Stripe\Service\AbstractService
{
    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/terminal/onboarding_links', $params, $opts);
    }
}
