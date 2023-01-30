<?php

namespace Papertrail\Resources;

use Papertrail\Papertrail;

class Address extends Resource
{
    public $id;

    public $isValid;

    public $street;

    public $number;

    public $zipCode;

    public $city;

    public $country;

    public function isValid()
    {
        return (bool) $this->isValid;
    }
}
