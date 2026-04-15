<?php


namespace Stripe\Sigma;

class ScheduledQueryRun extends \Stripe\ApiResource
{
    const OBJECT_NAME = 'scheduled_query_run';

    public static function all($params = null, $opts = null)
    {
        $url = static::classUrl();

        return static::_requestPage($url, \Stripe\Collection::class, $params, $opts);
    }

    public static function retrieve($id, $opts = null)
    {
        $opts = \Stripe\Util\RequestOptions::parse($opts);
        $instance = new static($id, $opts);
        $instance->refresh();

        return $instance;
    }

    public static function classUrl()
    {
        return '/v1/sigma/scheduled_query_runs';
    }
}
