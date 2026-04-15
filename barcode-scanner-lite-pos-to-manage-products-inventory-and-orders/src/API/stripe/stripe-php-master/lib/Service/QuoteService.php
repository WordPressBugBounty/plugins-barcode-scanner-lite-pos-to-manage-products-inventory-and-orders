<?php


namespace Stripe\Service;

class QuoteService extends AbstractService
{
    public function accept($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/quotes/%s/accept', $id), $params, $opts);
    }

    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/quotes', $params, $opts);
    }

    public function allComputedUpfrontLineItems($id, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/v1/quotes/%s/computed_upfront_line_items', $id), $params, $opts);
    }

    public function allLineItems($id, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/v1/quotes/%s/line_items', $id), $params, $opts);
    }

    public function cancel($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/quotes/%s/cancel', $id), $params, $opts);
    }

    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/quotes', $params, $opts);
    }

    public function finalizeQuote($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/quotes/%s/finalize', $id), $params, $opts);
    }

    public function pdf($id, $readBodyChunkCallable, $params = null, $opts = null)
    {
        $opts = \Stripe\Util\RequestOptions::parse($opts);
        if (!isset($opts->apiBase)) {
            $opts->apiBase = $this->getClient()->getFilesBase();
        }

        return $this->requestStream('get', $this->buildPath('/v1/quotes/%s/pdf', $id), $readBodyChunkCallable, $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/quotes/%s', $id), $params, $opts);
    }

    public function update($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/quotes/%s', $id), $params, $opts);
    }
}
