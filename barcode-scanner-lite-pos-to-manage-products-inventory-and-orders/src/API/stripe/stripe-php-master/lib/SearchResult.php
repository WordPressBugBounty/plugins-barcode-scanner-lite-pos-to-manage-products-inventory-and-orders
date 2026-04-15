<?php

namespace Stripe;

class SearchResult extends StripeObject implements \Countable, \IteratorAggregate
{
    const OBJECT_NAME = 'search_result';

    use ApiOperations\Request;

    protected $filters = [];

    public static function baseUrl()
    {
        return Stripe::$apiBase;
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function setFilters($filters)
    {
        $this->filters = $filters;
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($k)
    {
        if (\is_string($k)) {
            return parent::offsetGet($k);
        }
        $msg = "You tried to access the {$k} index, but SearchResult "
                   . 'types only support string keys. (HINT: Search calls '
                   . 'return an object with a `data` (which is the data '
                   . "array). You likely want to call ->data[{$k}])";

        throw new Exception\InvalidArgumentException($msg);
    }

    public function all($params = null, $opts = null)
    {
        self::_validateParams($params);
        list($url, $params) = $this->extractPathAndUpdateParams($params);

        list($response, $opts) = $this->_request('get', $url, $params, $opts);
        $obj = Util\Util::convertToStripeObject($response, $opts);
        if (!$obj instanceof SearchResult) {
            throw new Exception\UnexpectedValueException(
                'Expected type ' . SearchResult::class . ', got "' . \get_class($obj) . '" instead.'
            );
        }
        $obj->setFilters($params);

        return $obj;
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

    public function autoPagingIterator()
    {
        $page = $this;

        while (true) {
            foreach ($page as $item) {
                yield $item;
            }
            $page = $page->nextPage();

            if ($page->isEmpty()) {
                break;
            }
        }
    }

    public static function emptySearchResult($opts = null)
    {
        return SearchResult::constructFrom(['data' => []], $opts);
    }

    public function isEmpty()
    {
        return empty($this->data);
    }

    public function nextPage($params = null, $opts = null)
    {
        if (!$this->has_more) {
            return static::emptySearchResult($opts);
        }

        $params = \array_merge(
            $this->filters ?: [],
            ['page' => $this->next_page],
            $params ?: []
        );

        return $this->all($params, $opts);
    }

    public function first()
    {
        return \count($this->data) > 0 ? $this->data[0] : null;
    }

    public function last()
    {
        return \count($this->data) > 0 ? $this->data[\count($this->data) - 1] : null;
    }

    private function extractPathAndUpdateParams($params)
    {
        $url = \parse_url($this->url);

        if (!isset($url['path'])) {
            throw new Exception\UnexpectedValueException("Could not parse list url into parts: {$url}");
        }

        if (isset($url['query'])) {
            $query = [];
            \parse_str($url['query'], $query);
            $params = \array_merge($params ?: [], $query);
        }

        return [$url['path'], $params];
    }
}
