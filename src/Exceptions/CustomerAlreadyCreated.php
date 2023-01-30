<?php

namespace Papertrail\Exceptions;

use Exception;

class CustomerAlreadyCreated extends Exception
{
    public static function exists($owner)
    {
        return new static(class_basename($owner)." is already a Papertrail customer with ID {$owner->papertrail_id}.");
    }
}
