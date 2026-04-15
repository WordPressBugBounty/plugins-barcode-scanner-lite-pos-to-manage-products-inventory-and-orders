<?php

namespace Stripe;

use Stripe\Util\Util;
use Stripe\V2\Core\EventNotification;

class BaseStripeClient implements StripeClientInterface, StripeStreamingClientInterface
{
    const DEFAULT_API_BASE = 'https://api.stripe.com';

    const DEFAULT_CONNECT_BASE = 'https://connect.stripe.com';

    const DEFAULT_FILES_BASE = 'https://files.stripe.com';

    const DEFAULT_METER_EVENTS_BASE = 'https://meter-events.stripe.com';

    const DEFAULT_CONFIG = [
        'api_key' => null,
        'app_info' => null,
        'client_id' => null,
        'stripe_account' => null,
        'stripe_context' => null,
        'stripe_version' => \Stripe\Util\ApiVersion::CURRENT,
        'api_base' => self::DEFAULT_API_BASE,
        'connect_base' => self::DEFAULT_CONNECT_BASE,
        'files_base' => self::DEFAULT_FILES_BASE,
        'meter_events_base' => self::DEFAULT_METER_EVENTS_BASE,
        'max_network_retries' => null,
    ];

    private $config;

    private $defaultOpts;

    public function __construct($config = [])
    {
        if (\is_string($config)) {
            $config = ['api_key' => $config];
        } elseif (!\is_array($config)) {
            throw new Exception\InvalidArgumentException('$config must be a string or an array');
        }

        if (!\array_key_exists('max_network_retries', $config)) {
            $config['max_network_retries'] = Stripe::getMaxNetworkRetries();
        }

        $config = \array_merge(self::DEFAULT_CONFIG, $config);
        $this->validateConfig($config);

        $this->config = $config;

        $this->defaultOpts = \Stripe\Util\RequestOptions::parse([
            'stripe_account' => $config['stripe_account'],
            'stripe_context' => $config['stripe_context'],
            'stripe_version' => $config['stripe_version'],
            'max_network_retries' => $config['max_network_retries'],
        ]);
    }

    public function getApiKey()
    {
        return $this->config['api_key'];
    }

    public function getClientId()
    {
        return $this->config['client_id'];
    }

    public function getStripeAccount()
    {
        return $this->config['stripe_account'];
    }

    public function getStripeContext()
    {
        return $this->config['stripe_context'];
    }

    public function getStripeVersion()
    {
        return $this->config['stripe_version'];
    }

    public function getApiBase()
    {
        return $this->config['api_base'];
    }

    public function getConnectBase()
    {
        return $this->config['connect_base'];
    }

    public function getFilesBase()
    {
        return $this->config['files_base'];
    }

    public function getMeterEventsBase()
    {
        return $this->config['meter_events_base'];
    }

    public function getMaxNetworkRetries()
    {
        return $this->config['max_network_retries'];
    }

    public function getAppInfo()
    {
        return $this->config['app_info'];
    }

    public function request($method, $path, $params, $opts)
    {
        $defaultRequestOpts = $this->defaultOpts;
        $apiMode = Util::getApiMode($path);

        $opts = $defaultRequestOpts->merge($opts, true);

        $baseUrl = $opts->apiBase ?: $this->getApiBase();
        $requestor = new ApiRequestor($this->apiKeyForRequest($opts), $baseUrl, $this->getAppInfo());
        list($response, $opts->apiKey) = $requestor->request($method, $path, $params, $opts->headers, $apiMode, ['stripe_client'], $opts->maxNetworkRetries);
        $opts->discardNonPersistentHeaders();
        $obj = Util::convertToStripeObject($response->json, $opts, $apiMode, Util::isV2DeleteRequest($method, $apiMode));
        if (\is_array($obj)) {
            $obj = new StripeObject();
        }
        $obj->setLastResponse($response);

        return $obj;
    }

    public function rawRequest($method, $path, $params = null, $opts = [], $maxNetworkRetries = null, $usage = null)
    {
        if ('post' !== $method && null !== $params) {
            throw new Exception\InvalidArgumentException('Error: rawRequest only supports $params on post requests. Please pass null and add your parameters to $path');
        }
        $apiMode = Util::getApiMode($path);
        $headers = [];
        if (\is_array($opts) && \array_key_exists('headers', $opts)) {
            $headers = $opts['headers'] ?: [];
            unset($opts['headers']);
        }

        $defaultRawRequestOpts = $this->defaultOpts;

        $opts = $defaultRawRequestOpts->merge($opts, true);

        $opts->headers = \array_merge($opts->headers, $headers);
        $baseUrl = $opts->apiBase ?: $this->getApiBase();
        $requestor = new ApiRequestor($this->apiKeyForRequest($opts), $baseUrl);

        if (null === $usage) {
            $usage = ['raw_request'];
        }

        list($response) = $requestor->request($method, $path, $params, $opts->headers, $apiMode, $usage, $maxNetworkRetries);

        return $response;
    }

    public function requestStream($method, $path, $readBodyChunkCallable, $params, $opts)
    {
        $opts = $this->defaultOpts->merge($opts, true);
        $baseUrl = $opts->apiBase ?: $this->getApiBase();
        $requestor = new ApiRequestor($this->apiKeyForRequest($opts), $baseUrl, $this->getAppInfo());
        $apiMode = Util::getApiMode($path);
        list($response, $opts->apiKey) = $requestor->requestStream($method, $path, $readBodyChunkCallable, $params, $opts->headers, $apiMode, ['stripe_client']);
    }

    public function requestCollection($method, $path, $params, $opts)
    {
        $obj = $this->request($method, $path, $params, $opts);
        $apiMode = Util::getApiMode($path);
        if ('v1' === $apiMode) {
            if (!$obj instanceof Collection) {
                $received_class = \get_class($obj);
                $msg = "Expected to receive `Stripe\\Collection` object from Stripe API. Instead received `{$received_class}`.";

                throw new Exception\UnexpectedValueException($msg);
            }
            $obj->setFilters($params);
        } else {
            if (!$obj instanceof V2\Collection) {
                $received_class = \get_class($obj);
                $msg = "Expected to receive `Stripe\\V2\\Collection` object from Stripe API. Instead received `{$received_class}`.";

                throw new Exception\UnexpectedValueException($msg);
            }
        }

        return $obj;
    }

    public function requestSearchResult($method, $path, $params, $opts)
    {
        $obj = $this->request($method, $path, $params, $opts);
        if (!$obj instanceof SearchResult) {
            $received_class = \get_class($obj);
            $msg = "Expected to receive `Stripe\\SearchResult` object from Stripe API. Instead received `{$received_class}`.";

            throw new Exception\UnexpectedValueException($msg);
        }
        $obj->setFilters($params);

        return $obj;
    }

    private function apiKeyForRequest($opts)
    {
        $apiKey = $opts->apiKey ?: $this->getApiKey();

        if (null === $apiKey) {
            $msg = 'No API key provided. Set your API key when constructing the '
                . 'StripeClient instance, or provide it on a per-request basis '
                . 'using the `api_key` key in the $opts argument.';

            throw new Exception\AuthenticationException($msg);
        }

        return $apiKey;
    }

    private function validateConfig($config)
    {
        if (null !== $config['api_key'] && !\is_string($config['api_key'])) {
            throw new Exception\InvalidArgumentException('api_key must be null or a string');
        }

        if (null !== $config['api_key'] && ('' === $config['api_key'])) {
            $msg = 'api_key cannot be the empty string';

            throw new Exception\InvalidArgumentException($msg);
        }

        if (null !== $config['api_key'] && \preg_match('/\s/', $config['api_key'])) {
            $msg = 'api_key cannot contain whitespace';

            throw new Exception\InvalidArgumentException($msg);
        }

        if (null !== $config['client_id'] && !\is_string($config['client_id'])) {
            throw new Exception\InvalidArgumentException('client_id must be null or a string');
        }

        if (null !== $config['stripe_account'] && !\is_string($config['stripe_account'])) {
            throw new Exception\InvalidArgumentException('stripe_account must be null or a string');
        }

        if (null !== $config['stripe_context'] && !\is_string($config['stripe_context']) && !($config['stripe_context'] instanceof StripeContext)) {
            throw new Exception\InvalidArgumentException('stripe_context must be null, a string, or a StripeContext instance');
        }

        if (null !== $config['stripe_version'] && !\is_string($config['stripe_version'])) {
            throw new Exception\InvalidArgumentException('stripe_version must be null or a string');
        }

        if (!\is_string($config['api_base'])) {
            throw new Exception\InvalidArgumentException('api_base must be a string');
        }

        if (!\is_string($config['connect_base'])) {
            throw new Exception\InvalidArgumentException('connect_base must be a string');
        }

        if (!\is_string($config['files_base'])) {
            throw new Exception\InvalidArgumentException('files_base must be a string');
        }

        if (null !== $config['app_info'] && !\is_array($config['app_info'])) {
            throw new Exception\InvalidArgumentException('app_info must be an array');
        }

        if (!\is_int($config['max_network_retries'])) {
            throw new Exception\InvalidArgumentException('max_network_retries must an int');
        }

        $appInfoKeys = ['name', 'version', 'url', 'partner_id'];
        if (null !== $config['app_info'] && array_diff_key($config['app_info'], array_flip($appInfoKeys))) {
            $msg = 'app_info must be of type array{name: string, version?: string, url?: string, partner_id?: string}';

            throw new Exception\InvalidArgumentException($msg);
        }

        $extraConfigKeys = \array_diff(\array_keys($config), \array_keys(self::DEFAULT_CONFIG));
        if (!empty($extraConfigKeys)) {
            $invalidKeys = "'" . \implode("', '", $extraConfigKeys) . "'";

            throw new Exception\InvalidArgumentException('Found unknown key(s) in configuration array: ' . $invalidKeys);
        }
    }

    public function deserialize($json, $apiMode = 'v1')
    {
        return Util::convertToStripeObject(\json_decode($json, true), [], $apiMode);
    }

    public function parseEventNotification($payload, $sigHeader, $secret, $tolerance = Webhook::DEFAULT_TOLERANCE)
    {
        $eventData = Util::utf8($payload);
        WebhookSignature::verifyHeader($payload, $sigHeader, $secret, $tolerance);

        return EventNotification::fromJson($eventData, $this);
    }
}
