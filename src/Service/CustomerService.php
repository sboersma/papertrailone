<?php

namespace Papertrail\Service;

class CustomerService extends \Papertrail\Service\AbstractService
{
    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/customers', $params, $opts);
    }
}
