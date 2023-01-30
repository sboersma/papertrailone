<?php

namespace Papertrail\Resources;

use Papertrail\Papertrail;

class Customer extends Resource
{
    public $id;

    public $isValid;

    public $isCompany;

    public $companyName;

    public $firstname;

    public $lastname;

    public $taxId;

    public $address;

    public function isValid()
    {
        return (bool) $this->isValid;
    }

    public function address()
    {
        return new Address($this->address);
    }
}
