<?php

namespace Stripe\Util;

class DefaultLogger implements LoggerInterface
{
    public $messageType = 0;

    public $destination;

    public function error($message, array $context = [])
    {
        if (\count($context) > 0) {
            throw new \Stripe\Exception\BadMethodCallException('DefaultLogger does not currently implement context. Please implement if you need it.');
        }

        if (null === $this->destination) {
            \error_log($message, $this->messageType);
        } else {
            \error_log($message, $this->messageType, $this->destination);
        }
    }
}
