<?php


namespace Stripe\Service;

class CustomerService extends AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/customers', $params, $opts);
    }

    public function allBalanceTransactions($parentId, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/v1/customers/%s/balance_transactions', $parentId), $params, $opts);
    }

    public function allCashBalanceTransactions($parentId, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/v1/customers/%s/cash_balance_transactions', $parentId), $params, $opts);
    }

    public function allPaymentMethods($id, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/v1/customers/%s/payment_methods', $id), $params, $opts);
    }

    public function allSources($parentId, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/v1/customers/%s/sources', $parentId), $params, $opts);
    }

    public function allTaxIds($parentId, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/v1/customers/%s/tax_ids', $parentId), $params, $opts);
    }

    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/customers', $params, $opts);
    }

    public function createBalanceTransaction($parentId, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/customers/%s/balance_transactions', $parentId), $params, $opts);
    }

    public function createFundingInstructions($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/customers/%s/funding_instructions', $id), $params, $opts);
    }

    public function createSource($parentId, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/customers/%s/sources', $parentId), $params, $opts);
    }

    public function createTaxId($parentId, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/customers/%s/tax_ids', $parentId), $params, $opts);
    }

    public function delete($id, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/v1/customers/%s', $id), $params, $opts);
    }

    public function deleteDiscount($id, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/v1/customers/%s/discount', $id), $params, $opts);
    }

    public function deleteSource($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/v1/customers/%s/sources/%s', $parentId, $id), $params, $opts);
    }

    public function deleteTaxId($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/v1/customers/%s/tax_ids/%s', $parentId, $id), $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/customers/%s', $id), $params, $opts);
    }

    public function retrieveBalanceTransaction($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/customers/%s/balance_transactions/%s', $parentId, $id), $params, $opts);
    }

    public function retrieveCashBalance($parentId, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/customers/%s/cash_balance', $parentId), $params, $opts);
    }

    public function retrieveCashBalanceTransaction($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/customers/%s/cash_balance_transactions/%s', $parentId, $id), $params, $opts);
    }

    public function retrievePaymentMethod($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/customers/%s/payment_methods/%s', $parentId, $id), $params, $opts);
    }

    public function retrieveSource($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/customers/%s/sources/%s', $parentId, $id), $params, $opts);
    }

    public function retrieveTaxId($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/customers/%s/tax_ids/%s', $parentId, $id), $params, $opts);
    }

    public function search($params = null, $opts = null)
    {
        return $this->requestSearchResult('get', '/v1/customers/search', $params, $opts);
    }

    public function update($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/customers/%s', $id), $params, $opts);
    }

    public function updateBalanceTransaction($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/customers/%s/balance_transactions/%s', $parentId, $id), $params, $opts);
    }

    public function updateCashBalance($parentId, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/customers/%s/cash_balance', $parentId), $params, $opts);
    }

    public function updateSource($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/customers/%s/sources/%s', $parentId, $id), $params, $opts);
    }

    public function verifySource($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/customers/%s/sources/%s/verify', $parentId, $id), $params, $opts);
    }
}
