<?php

namespace Papertrail\Service;

class QuotationService extends \Papertrail\Service\AbstractService
{
    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/quotations', $params, $opts);
    }

}
