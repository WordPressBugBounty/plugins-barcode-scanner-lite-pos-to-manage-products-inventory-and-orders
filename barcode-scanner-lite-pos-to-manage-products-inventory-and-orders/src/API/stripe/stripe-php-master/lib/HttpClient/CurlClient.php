<?php

namespace Stripe\HttpClient;

use Stripe\Exception;
use Stripe\Stripe;
use Stripe\Util;

// @codingStandardsIgnoreStart


if (!\defined('CURL_SSLVERSION_TLSv1_2')) {
    \define('CURL_SSLVERSION_TLSv1_2', 6);
}
// @codingStandardsIgnoreEnd

if (!\defined('CURL_HTTP_VERSION_2TLS')) {
    \define('CURL_HTTP_VERSION_2TLS', 4);
}

class CurlClient implements ClientInterface, StreamingClientInterface
{
    protected static $instance;

    public static function instance()
    {
        if (!static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    protected $defaultOptions;

    protected $randomGenerator;

    protected $userAgentInfo;

    protected $enablePersistentConnections = true;

    protected $enableHttp2;

    protected $curlHandle;

    protected $requestStatusCallback;

    public function __construct($defaultOptions = null, $randomGenerator = null)
    {
        $this->defaultOptions = $defaultOptions;
        $this->randomGenerator = $randomGenerator ?: new Util\RandomGenerator();
        $this->initUserAgentInfo();

        $this->enableHttp2 = $this->canSafelyUseHttp2();
    }

    public function __destruct()
    {
        $this->closeCurlHandle();
    }

    public function initUserAgentInfo()
    {
        $curlVersion = \curl_version();
        $this->userAgentInfo = [
            'httplib' => 'curl ' . $curlVersion['version'],
            'ssllib' => $curlVersion['ssl_version'],
        ];
    }

    public function getDefaultOptions()
    {
        return $this->defaultOptions;
    }

    public function getUserAgentInfo()
    {
        return $this->userAgentInfo;
    }

    public function getEnablePersistentConnections()
    {
        return $this->enablePersistentConnections;
    }

    public function setEnablePersistentConnections($enable)
    {
        $this->enablePersistentConnections = $enable;
    }

    public function getEnableHttp2()
    {
        return $this->enableHttp2;
    }

    public function setEnableHttp2($enable)
    {
        $this->enableHttp2 = $enable;
    }

    public function getRequestStatusCallback()
    {
        return $this->requestStatusCallback;
    }

    public function setRequestStatusCallback($requestStatusCallback)
    {
        $this->requestStatusCallback = $requestStatusCallback;
    }


    const DEFAULT_TIMEOUT = 80;
    const DEFAULT_CONNECT_TIMEOUT = 30;

    private $timeout = self::DEFAULT_TIMEOUT;
    private $connectTimeout = self::DEFAULT_CONNECT_TIMEOUT;

    public function setTimeout($seconds)
    {
        $this->timeout = (int) \max($seconds, 0);

        return $this;
    }

    public function setConnectTimeout($seconds)
    {
        $this->connectTimeout = (int) \max($seconds, 0);

        return $this;
    }

    public function getTimeout()
    {
        return $this->timeout;
    }

    public function getConnectTimeout()
    {
        return $this->connectTimeout;
    }


    private function constructUrlAndBody($method, $absUrl, $params, $hasFile, $apiMode)
    {
        $params = Util\Util::objectsToIds($params);
        if ('post' === $method) {
            $absUrl = Util\Util::utf8($absUrl);
            if ($hasFile) {
                return [$absUrl, $params];
            }
            if ('v2' === $apiMode) {
                if (\is_array($params) && 0 === \count($params)) {
                    return [$absUrl, null];
                }

                return [$absUrl, \json_encode($params)];
            }

            return [$absUrl, Util\Util::encodeParameters($params)];
        }
        if ($hasFile) {
            throw new Exception\UnexpectedValueException("Unexpected. {$method} methods don't support file attachments");
        }
        if (0 === \count($params)) {
            return [Util\Util::utf8($absUrl), null];
        }
        $encoded = Util\Util::encodeParameters($params, $apiMode);

        $absUrl = "{$absUrl}?{$encoded}";
        $absUrl = Util\Util::utf8($absUrl);

        return [$absUrl, null];
    }

    private function calculateDefaultOptions($method, $absUrl, $headers, $params, $hasFile)
    {
        if (\is_callable($this->defaultOptions)) { 
            $ret = \call_user_func_array($this->defaultOptions, [$method, $absUrl, $headers, $params, $hasFile]);
            if (!\is_array($ret)) {
                throw new Exception\UnexpectedValueException('Non-array value returned by defaultOptions CurlClient callback');
            }

            return $ret;
        }
        if (\is_array($this->defaultOptions)) { 
            return $this->defaultOptions;
        }

        return [];
    }

    private function constructCurlOptions($method, $absUrl, $headers, $body, $opts, $apiMode)
    {
        if ('get' === $method) {
            $opts[\CURLOPT_HTTPGET] = 1;
        } elseif ('post' === $method) {
            $opts[\CURLOPT_POST] = 1;
        } elseif ('delete' === $method) {
            $opts[\CURLOPT_CUSTOMREQUEST] = 'DELETE';
        } else {
            throw new Exception\UnexpectedValueException("Unrecognized method {$method}");
        }

        if ($body) {
            $opts[\CURLOPT_POSTFIELDS] = $body;
        }
        elseif (isset($opts[\CURLOPT_POST]) && 1 === $opts[\CURLOPT_POST]) {
            $opts[\CURLOPT_POSTFIELDS] = '';
        }

        if (!$this->hasHeader($headers, 'Idempotency-Key')) {
            if ('v2' === $apiMode) {
                if ('post' === $method || 'delete' === $method) {
                    $headers[] = 'Idempotency-Key: ' . $this->randomGenerator->uuid();
                }
            } else {
                if ('post' === $method && Stripe::$maxNetworkRetries > 0) {
                    $headers[] = 'Idempotency-Key: ' . $this->randomGenerator->uuid();
                }
            }
        }

        $headers[] = 'Expect: ';

        $opts[\CURLOPT_URL] = $absUrl;
        $opts[\CURLOPT_RETURNTRANSFER] = true;
        $opts[\CURLOPT_CONNECTTIMEOUT] = $this->connectTimeout;
        $opts[\CURLOPT_TIMEOUT] = $this->timeout;
        $opts[\CURLOPT_HTTPHEADER] = $headers;
        $opts[\CURLOPT_CAINFO] = Stripe::getCABundlePath();
        if (!Stripe::getVerifySslCerts()) {
            $opts[\CURLOPT_SSL_VERIFYPEER] = false;
        }

        if (!isset($opts[\CURLOPT_HTTP_VERSION]) && $this->getEnableHttp2()) {
            $opts[\CURLOPT_HTTP_VERSION] = \CURL_HTTP_VERSION_2TLS;
        }

        return $opts;
    }

    private function constructRequest($method, $absUrl, $headers, $params, $hasFile, $apiMode)
    {
        $method = \strtolower($method);

        $opts = $this->calculateDefaultOptions($method, $absUrl, $headers, $params, $hasFile);
        list($absUrl, $body) = $this->constructUrlAndBody($method, $absUrl, $params, $hasFile, $apiMode);
        $opts = $this->constructCurlOptions($method, $absUrl, $headers, $body, $opts, $apiMode);

        return [$opts, $absUrl];
    }

    public function request($method, $absUrl, $headers, $params, $hasFile, $apiMode = 'v1', $maxNetworkRetries = null)
    {
        list($opts, $absUrl) = $this->constructRequest($method, $absUrl, $headers, $params, $hasFile, $apiMode);
        list($rbody, $rcode, $rheaders) = $this->executeRequestWithRetries($opts, $absUrl, $maxNetworkRetries);

        return [$rbody, $rcode, $rheaders];
    }

    public function requestStream($method, $absUrl, $headers, $params, $hasFile, $readBodyChunk, $apiMode = 'v1', $maxNetworkRetries = null)
    {
        list($opts, $absUrl) = $this->constructRequest($method, $absUrl, $headers, $params, $hasFile, $apiMode);
        $opts[\CURLOPT_RETURNTRANSFER] = false;
        list($rbody, $rcode, $rheaders) = $this->executeStreamingRequestWithRetries($opts, $absUrl, $readBodyChunk, $maxNetworkRetries);

        return [$rbody, $rcode, $rheaders];
    }

    private function useHeadersToDetermineWriteCallback($opts, $determineWriteCallback)
    {
        $rheaders = new Util\CaseInsensitiveArray();
        $headerCallback = static function ($curl, $header_line) use (&$rheaders) {
            return self::parseLineIntoHeaderArray($header_line, $rheaders);
        };

        $writeCallback = null;
        $writeCallbackWrapper = static function ($curl, $data) use (&$writeCallback, &$rheaders, &$determineWriteCallback) {
            if (null === $writeCallback) {
                $writeCallback = \call_user_func_array($determineWriteCallback, [$rheaders]);
            }

            return \call_user_func_array($writeCallback, [$curl, $data]);
        };

        return [$headerCallback, $writeCallbackWrapper];
    }

    private static function parseLineIntoHeaderArray($line, &$headers)
    {
        if (false === \strpos($line, ':')) {
            return \strlen($line);
        }
        list($key, $value) = \explode(':', \trim($line), 2);
        $headers[\trim($key)] = \trim($value);

        return \strlen($line);
    }

    public function executeStreamingRequestWithRetries($opts, $absUrl, $readBodyChunk, $maxNetworkRetries = null)
    {
        $shouldRetry = false;
        $numRetries = 0;

        $rbody = null;

        $rcode = null;

        $lastRHeaders = null;

        $errno = null;
        $message = null;

        $determineWriteCallback = function ($rheaders) use (&$readBodyChunk, &$shouldRetry, &$rbody, &$numRetries, &$rcode, &$lastRHeaders, &$errno, &$maxNetworkRetries) {
            $lastRHeaders = $rheaders;
            $errno = \curl_errno($this->curlHandle);

            $rcode = \curl_getinfo($this->curlHandle, \CURLINFO_HTTP_CODE);

            if ($rcode < 300) {
                $rbody = null;

                return static function ($curl, $data) use (&$readBodyChunk) {
                    \call_user_func_array($readBodyChunk, [$data]);

                    return \strlen($data);
                };
            }

            $shouldRetry = $this->shouldRetry($errno, $rcode, $rheaders, $numRetries, $maxNetworkRetries);

            if ($shouldRetry) {
                return static function ($curl, $data) {
                    return \strlen($data);
                };
            } else {
                $rbody = '';

                return static function ($curl, $data) use (&$rbody) {
                    $rbody .= $data;

                    return \strlen($data);
                };
            }
        };

        while (true) {
            list($headerCallback, $writeCallback) = $this->useHeadersToDetermineWriteCallback($opts, $determineWriteCallback);
            $opts[\CURLOPT_HEADERFUNCTION] = $headerCallback;
            $opts[\CURLOPT_WRITEFUNCTION] = $writeCallback;

            $shouldRetry = false;
            $rbody = null;
            $this->resetCurlHandle();
            \curl_setopt_array($this->curlHandle, $opts);
            $result = \curl_exec($this->curlHandle);
            $errno = \curl_errno($this->curlHandle);
            if (0 !== $errno) {
                $message = \curl_error($this->curlHandle);
            }
            if (!$this->getEnablePersistentConnections()) {
                $this->closeCurlHandle();
            }

            if (\is_callable($this->getRequestStatusCallback())) {
                \call_user_func_array(
                    $this->getRequestStatusCallback(),
                    [$rbody, $rcode, $lastRHeaders, $errno, $message, $shouldRetry, $numRetries]
                );
            }

            if ($shouldRetry) {
                ++$numRetries;
                $sleepSeconds = $this->sleepTime($numRetries, $lastRHeaders);
                \usleep((int) ($sleepSeconds * 1000000));
            } else {
                break;
            }
        }

        if (0 !== $errno) {
            $this->handleCurlError($absUrl, $errno, $message, $numRetries);
        }

        return [$rbody, $rcode, $lastRHeaders];
    }

    public function executeRequestWithRetries($opts, $absUrl, $maxNetworkRetries = null)
    {
        $numRetries = 0;

        while (true) {
            $rcode = 0;
            $errno = 0;
            $message = null;

            $rheaders = new Util\CaseInsensitiveArray();
            $headerCallback = static function ($curl, $header_line) use (&$rheaders) {
                return CurlClient::parseLineIntoHeaderArray($header_line, $rheaders);
            };
            $opts[\CURLOPT_HEADERFUNCTION] = $headerCallback;

            $this->resetCurlHandle();
            \curl_setopt_array($this->curlHandle, $opts);
            $rbody = \curl_exec($this->curlHandle);

            if (false === $rbody) {
                $errno = \curl_errno($this->curlHandle);
                $message = \curl_error($this->curlHandle);
            } else {
                $rcode = \curl_getinfo($this->curlHandle, \CURLINFO_HTTP_CODE);
            }
            if (!$this->getEnablePersistentConnections()) {
                $this->closeCurlHandle();
            }

            $shouldRetry = $this->shouldRetry($errno, $rcode, $rheaders, $numRetries, $maxNetworkRetries);

            if (\is_callable($this->getRequestStatusCallback())) {
                \call_user_func_array(
                    $this->getRequestStatusCallback(),
                    [$rbody, $rcode, $rheaders, $errno, $message, $shouldRetry, $numRetries]
                );
            }

            if ($shouldRetry) {
                ++$numRetries;
                $sleepSeconds = $this->sleepTime($numRetries, $rheaders);
                \usleep((int) ($sleepSeconds * 1000000));
            } else {
                break;
            }
        }

        if (false === $rbody) {
            $this->handleCurlError($absUrl, $errno, $message, $numRetries);
        }

        return [$rbody, $rcode, $rheaders];
    }

    private function handleCurlError($url, $errno, $message, $numRetries)
    {
        switch ($errno) {
            case \CURLE_COULDNT_CONNECT:
            case \CURLE_COULDNT_RESOLVE_HOST:
            case \CURLE_OPERATION_TIMEOUTED:
                $msg = "Could not connect to Stripe ({$url}).  Please check your "
                    . 'internet connection and try again.  If this problem persists, '
                    . "you should check Stripe's service status at "
                    . 'https://twitter.com/stripestatus, or';

                break;

            case \CURLE_SSL_CACERT:
            case \CURLE_SSL_PEER_CERTIFICATE:
                $msg = "Could not verify Stripe's SSL certificate.  Please make sure "
                    . 'that your network is not intercepting certificates.  '
                    . "(Try going to {$url} in your browser.)  "
                    . 'If this problem persists,';

                break;

            default:
                $msg = 'Unexpected error communicating with Stripe.  '
                    . 'If this problem persists,';
        }
        $msg .= ' let us know at support@stripe.com.';

        $msg .= "\n\n(Network error [errno {$errno}]: {$message})";

        if ($numRetries > 0) {
            $msg .= "\n\nRequest was retried {$numRetries} times.";
        }

        throw new Exception\ApiConnectionException($msg);
    }

    private function shouldRetry($errno, $rcode, $rheaders, $numRetries, $maxNetworkRetries)
    {
        if (null === $maxNetworkRetries) {
            $maxNetworkRetries = Stripe::getMaxNetworkRetries();
        }

        if ($numRetries >= $maxNetworkRetries) {
            return false;
        }

        if (\CURLE_OPERATION_TIMEOUTED === $errno) {
            return true;
        }

        if (\CURLE_COULDNT_CONNECT === $errno) {
            return true;
        }

        if (isset($rheaders['stripe-should-retry'])) {
            if ('false' === $rheaders['stripe-should-retry']) {
                return false;
            }
            if ('true' === $rheaders['stripe-should-retry']) {
                return true;
            }
        }

        if (409 === $rcode) {
            return true;
        }

        if ($rcode >= 500) {
            return true;
        }

        return false;
    }

    private function sleepTime($numRetries, $rheaders)
    {
        $sleepSeconds = \min(
            Stripe::getInitialNetworkRetryDelay() * 1.0 * 2 ** ($numRetries - 1),
            Stripe::getMaxNetworkRetryDelay()
        );

        $sleepSeconds *= 0.5 * (1 + $this->randomGenerator->randFloat());

        $sleepSeconds = \max(Stripe::getInitialNetworkRetryDelay(), $sleepSeconds);

        $retryAfter = isset($rheaders['retry-after']) ? (float) ($rheaders['retry-after']) : 0.0;
        if (\floor($retryAfter) === $retryAfter && $retryAfter <= Stripe::getMaxRetryAfter()) {
            $sleepSeconds = \max($sleepSeconds, $retryAfter);
        }

        return $sleepSeconds;
    }

    private function initCurlHandle()
    {
        $this->closeCurlHandle();
        $this->curlHandle = \curl_init();
    }

    private function closeCurlHandle()
    {
        if (null !== $this->curlHandle) {
            if (PHP_VERSION_ID < 80000) {
                \curl_close($this->curlHandle);
            }
            $this->curlHandle = null;
        }
    }

    private function resetCurlHandle()
    {
        if (null !== $this->curlHandle && $this->getEnablePersistentConnections()) {
            \curl_reset($this->curlHandle);
        } else {
            $this->initCurlHandle();
        }
    }

    private function canSafelyUseHttp2()
    {
        $curlVersion = \curl_version()['version'];

        return \version_compare($curlVersion, '7.60.0') >= 0;
    }

    private function hasHeader($headers, $name)
    {
        foreach ($headers as $header) {
            if (0 === \strncasecmp($header, "{$name}: ", \strlen($name) + 2)) {
                return true;
            }
        }

        return false;
    }
}
