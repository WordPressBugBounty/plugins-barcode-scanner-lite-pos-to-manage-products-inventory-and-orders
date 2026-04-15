<?php

namespace Stripe\Util;

use Stripe\StripeObject;
use Stripe\V2\DeletedObject;

abstract class Util
{
    private static $isMbstringAvailable = null;
    private static $isHashEqualsAvailable = null;

    public static function isList($array)
    {
        if (!\is_array($array)) {
            return false;
        }
        if ([] === $array) {
            return true;
        }
        if (\array_keys($array) !== \range(0, \count($array) - 1)) {
            return false;
        }

        return true;
    }

    public static function convertToStripeObject($resp, $opts, $apiMode = 'v1', $isV2DeletedObject = false)
    {
        $types = 'v1' === $apiMode ? ObjectTypes::mapping
            : ObjectTypes::v2Mapping;
        if (self::isList($resp)) {
            $mapped = [];
            foreach ($resp as $i) {
                $mapped[] = self::convertToStripeObject($i, $opts, $apiMode);
            }

            return $mapped;
        }
        if (\is_array($resp)) {
            if ($isV2DeletedObject) {
                $class = DeletedObject::class;
            } elseif (
                isset($resp['object']) && \is_string($resp['object'])
                && isset($types[$resp['object']])
            ) {
                $class = $types[$resp['object']];
                if ('v2' === $apiMode && ('v2.core.event' === $resp['object'])) {
                    $eventTypes = EventTypes::v2EventMapping;
                    if (\array_key_exists('type', $resp) && \array_key_exists($resp['type'], $eventTypes)) {
                        $class = $eventTypes[$resp['type']];
                    } else {
                        $class = \Stripe\V2\Core\Event::class;
                    }
                }
            } elseif (\array_key_exists('data', $resp) && \array_key_exists('next_page_url', $resp)) {
                $class = \Stripe\V2\Collection::class;
            } else {
                $class = StripeObject::class;
            }

            return $class::constructFrom($resp, $opts, $apiMode);
        }

        return $resp;
    }

    public static function utf8($value)
    {
        if (null === self::$isMbstringAvailable) {
            self::$isMbstringAvailable = \function_exists('mb_detect_encoding')
                && \function_exists('mb_convert_encoding');

            if (!self::$isMbstringAvailable) {
                \trigger_error(
                    'It looks like the mbstring extension is not enabled. '
                        . 'UTF-8 strings will not properly be encoded. Ask your system '
                        . 'administrator to enable the mbstring extension, or write to '
                        . 'support@stripe.com if you have any questions.',
                    \E_USER_WARNING
                );
            }
        }

        if (
            \is_string($value) && self::$isMbstringAvailable
            && 'UTF-8' !== \mb_detect_encoding($value, 'UTF-8', true)
        ) {
            return mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
        }

        return $value;
    }

    public static function secureCompare($a, $b)
    {
        if (null === self::$isHashEqualsAvailable) {
            self::$isHashEqualsAvailable = \function_exists('hash_equals');
        }

        if (self::$isHashEqualsAvailable) {
            return \hash_equals($a, $b);
        }
        if (\strlen($a) !== \strlen($b)) {
            return false;
        }

        $result = 0;
        for ($i = 0; $i < \strlen($a); ++$i) {
            $result |= \ord($a[$i]) ^ \ord($b[$i]);
        }

        return 0 === $result;
    }

    public static function objectsToIds($h)
    {
        if ($h instanceof \Stripe\ApiResource) {
            return $h->id;
        }
        if (static::isList($h)) {
            $results = [];
            foreach ($h as $v) {
                $results[] = static::objectsToIds($v);
            }

            return $results;
        }
        if (\is_array($h)) {
            $results = [];
            foreach ($h as $k => $v) {
                if (null === $v) {
                    continue;
                }
                $results[$k] = static::objectsToIds($v);
            }

            return $results;
        }

        return $h;
    }

    public static function encodeParameters($params, $apiMode = 'v1')
    {
        $flattenedParams = self::flattenParams($params, null, $apiMode);
        $pieces = [];
        foreach ($flattenedParams as $param) {
            list($k, $v) = $param;
            $pieces[] = self::urlEncode($k) . '=' . self::urlEncode($v);
        }

        return \implode('&', $pieces);
    }

    public static function flattenParams(
        $params,
        $parentKey = null,
        $apiMode = 'v1'
    ) {
        $result = [];

        foreach ($params as $key => $value) {
            $calculatedKey = $parentKey ? "{$parentKey}[{$key}]" : $key;
            if (self::isList($value)) {
                $result = \array_merge(
                    $result,
                    self::flattenParamsList($value, $calculatedKey, $apiMode)
                );
            } elseif (\is_array($value)) {
                $result = \array_merge(
                    $result,
                    self::flattenParams($value, $calculatedKey, $apiMode)
                );
            } else {
                $result[] = [$calculatedKey, $value];
            }
        }

        return $result;
    }

    public static function flattenParamsList(
        $value,
        $calculatedKey,
        $apiMode = 'v1'
    ) {
        $result = [];

        foreach ($value as $i => $elem) {
            if (self::isList($elem)) {
                $result = \array_merge(
                    $result,
                    self::flattenParamsList($elem, $calculatedKey)
                );
            } elseif (\is_array($elem)) {
                $result = \array_merge(
                    $result,
                    self::flattenParams($elem, "{$calculatedKey}[{$i}]")
                );
            } else {
                $result[] = ["{$calculatedKey}[{$i}]", $elem];
            }
        }

        return $result;
    }

    public static function urlEncode($key)
    {
        $s = \urlencode((string) $key);

        $s = \str_replace('%5B', '[', $s);

        return \str_replace('%5D', ']', $s);
    }

    public static function normalizeId($id)
    {
        if (\is_array($id)) {
            if (!isset($id['id'])) {
                return [null, $id];
            }
            $params = $id;
            $id = $params['id'];
            unset($params['id']);
        } else {
            $params = [];
        }

        return [$id, $params];
    }

    public static function currentTimeMillis()
    {
        return (int) \round(\microtime(true) * 1000);
    }

    public static function getApiMode($path)
    {
        $apiMode = 'v1';
        if ('/v2' === substr($path, 0, 3)) {
            $apiMode = 'v2';
        }

        return $apiMode;
    }

    public static function isV2DeleteRequest($method, $apiMode)
    {
        return 'delete' === $method && 'v2' === $apiMode;
    }
}
