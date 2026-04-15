<?php

namespace Stripe;

class StripeContext
{
    private $segments;

    public function __construct($segments = [])
    {
        $this->segments = $segments ?: [];
    }

    public function push($segment)
    {
        if (null === $segment) {
            throw new \InvalidArgumentException('segment cannot be null');
        }

        $newSegments = \array_merge($this->segments, [$segment]);

        return new StripeContext($newSegments);
    }

    public function pop()
    {
        if (empty($this->segments)) {
            throw new Exception\BadMethodCallException('Cannot pop from an empty context');
        }

        $newSegments = \array_slice($this->segments, 0, -1);

        return new StripeContext($newSegments);
    }

    public function __toString()
    {
        return \implode('/', $this->segments);
    }

    public static function parse($contextStr)
    {
        if (null === $contextStr || '' === $contextStr) {
            return new StripeContext([]);
        }

        $segments = \explode('/', $contextStr);

        return new StripeContext($segments);
    }

    public function getSegments()
    {
        return $this->segments;
    }
}
