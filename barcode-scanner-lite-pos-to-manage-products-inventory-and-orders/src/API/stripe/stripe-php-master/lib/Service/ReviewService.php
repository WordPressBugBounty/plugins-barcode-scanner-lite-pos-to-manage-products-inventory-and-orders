<?php


namespace Stripe\Service;

class ReviewService extends AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/reviews', $params, $opts);
    }

    public function approve($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/reviews/%s/approve', $id), $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/reviews/%s', $id), $params, $opts);
    }
}
