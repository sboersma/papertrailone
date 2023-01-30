<?php

namespace Papertrail\Service;

class CustomerService extends \Papertrail\Service\AbstractService
{
    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', '/v1/customers/' . $id, $params, $opts);
    }

    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/customers', $params, $opts);
    }

    public function update($id, $params = null, $opts = null)
    {
        return $this->request('put', '/v1/customers/' . $id, $params, $opts);
    }
}
