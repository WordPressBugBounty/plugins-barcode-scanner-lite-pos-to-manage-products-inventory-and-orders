<?php

namespace Stripe\Exception;

if (\interface_exists(\Throwable::class, false)) {
    interface ExceptionInterface extends \Throwable {}
} else {
    interface ExceptionInterface {}
}
