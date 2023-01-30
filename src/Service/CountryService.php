<?php

namespace Papertrail\Service;

class CountryService extends \Papertrail\Service\AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->request('get', '/v1/countries', $params, $opts);
    }
}
