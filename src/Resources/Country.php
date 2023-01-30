<?php

namespace Papertrail\Resources;

use Papertrail\Papertrail;

class Country extends Resource
{
    public $name;

    public function isValid()
    {
        return (bool) $this->isValid;
    }
}
