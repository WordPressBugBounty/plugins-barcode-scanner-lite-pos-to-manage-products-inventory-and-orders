<?php

namespace Stripe\Service;

class OAuthService extends AbstractService
{
    protected function requestConnect($method, $path, $params, $opts)
    {
        $opts = $this->_parseOpts($opts);
        $opts->apiBase = $this->_getBase($opts);

        return $this->request($method, $path, $params, $opts);
    }

    public function authorizeUrl($params = null, $opts = null)
    {
        $params = $params ?: [];

        $opts = $this->_parseOpts($opts);
        $base = $this->_getBase($opts);

        $params['client_id'] = $this->_getClientId($params);
        if (!\array_key_exists('response_type', $params)) {
            $params['response_type'] = 'code';
        }
        $query = \Stripe\Util\Util::encodeParameters($params);

        return $base . '/oauth/authorize?' . $query;
    }

    public function token($params = null, $opts = null)
    {
        $params = $params ?: [];
        $params['client_secret'] = $this->_getClientSecret($params);

        return $this->requestConnect('post', '/oauth/token', $params, $opts);
    }

    public function deauthorize($params = null, $opts = null)
    {
        $params = $params ?: [];
        $params['client_id'] = $this->_getClientId($params);

        return $this->requestConnect('post', '/oauth/deauthorize', $params, $opts);
    }

    private function _getClientId($params = null)
    {
        $clientId = ($params && \array_key_exists('client_id', $params)) ? $params['client_id'] : null;

        if (null === $clientId) {
            $clientId = $this->client->getClientId();
        }
        if (null === $clientId) {
            $msg = 'No client_id provided. (HINT: set your client_id using '
              . '`new \Stripe\StripeClient([clientId => <CLIENT-ID>
                ])`)".  You can find your client_ids '
              . 'in your Stripe dashboard at '
              . 'https://dashboard.stripe.com/account/applications/settings, '
              . 'after registering your account as a platform. See '
              . 'https://stripe.com/docs/connect/standard-accounts for details, '
              . 'or email support@stripe.com if you have any questions.';

            throw new \Stripe\Exception\AuthenticationException($msg);
        }

        return $clientId;
    }

    private function _getClientSecret($params = null)
    {
        if (\array_key_exists('client_secret', $params)) {
            return $params['client_secret'];
        }

        return $this->client->getApiKey();
    }

    private function _parseOpts($opts)
    {
        if (\is_array($opts)) {
            if (\array_key_exists('connect_base', $opts)) {
                throw new \Stripe\Exception\InvalidArgumentException('Use `api_base`, not `connect_base`');
            }
        }

        return \Stripe\Util\RequestOptions::parse($opts);
    }

    private function _getBase($opts)
    {
        return isset($opts->apiBase)
          ? $opts->apiBase
          : $this->client->getConnectBase();
    }
}
