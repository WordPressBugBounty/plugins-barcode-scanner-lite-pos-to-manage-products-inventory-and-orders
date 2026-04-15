<?php

namespace Stripe\V2;

class Collection extends \Stripe\StripeObject implements \Countable, \IteratorAggregate
{
    const OBJECT_NAME = 'list';

    use \Stripe\ApiOperations\Request;

    public static function baseUrl()
    {
        return \Stripe\Stripe::$apiBase;
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($k)
    {
        if (\is_string($k)) {
            return parent::offsetGet($k);
        }
        $msg = "You tried to access the {$k} index, but V2Collection "
            . 'types only support string keys. (HINT: List calls '
            . 'return an object with a `data` (which is the data '
            . "array). You likely want to call ->data[{$k}])";

        throw new \Stripe\Exception\InvalidArgumentException($msg);
    }

    #[\ReturnTypeWillChange]
    public function count()
    {
        return \count($this->data);
    }

    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    public function getReverseIterator()
    {
        return new \ArrayIterator(\array_reverse($this->data));
    }

    public function autoPagingIterator()
    {
        $page = $this->data;
        $next_page_url = $this->next_page_url;

        while (true) {
            foreach ($page as $item) {
                yield $item;
            }
            if (null === $next_page_url) {
                break;
            }

            list($response, $opts) = $this->_request(
                'get',
                $next_page_url,
                null,
                null,
                [],
                'v2'
            );
            $obj = \Stripe\Util\Util::convertToStripeObject($response, $opts, 'v2');
            $page = $obj->data;
            $next_page_url = $obj->next_page_url;
        }
    }
}
