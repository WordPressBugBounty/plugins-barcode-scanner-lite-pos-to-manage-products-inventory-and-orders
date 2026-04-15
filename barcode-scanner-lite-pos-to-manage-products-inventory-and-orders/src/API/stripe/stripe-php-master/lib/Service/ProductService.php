<?php


namespace Stripe\Service;

class ProductService extends AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/products', $params, $opts);
    }

    public function allFeatures($parentId, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/v1/products/%s/features', $parentId), $params, $opts);
    }

    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/products', $params, $opts);
    }

    public function createFeature($parentId, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/products/%s/features', $parentId), $params, $opts);
    }

    public function delete($id, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/v1/products/%s', $id), $params, $opts);
    }

    public function deleteFeature($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/v1/products/%s/features/%s', $parentId, $id), $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/products/%s', $id), $params, $opts);
    }

    public function retrieveFeature($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/products/%s/features/%s', $parentId, $id), $params, $opts);
    }

    public function search($params = null, $opts = null)
    {
        return $this->requestSearchResult('get', '/v1/products/search', $params, $opts);
    }

    public function update($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/products/%s', $id), $params, $opts);
    }
}
