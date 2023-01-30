<?php

namespace Papertrail;

use GuzzleHttp\Client;
use Papertrail\Service\CustomerService;

class Papertrail {

    protected $client;

    public function __construct() {
        $this->client = $this->client();
    }

    public static function client(array $options = [])
    {
        return new Client(array_merge([
            'base_uri' => 'https://api.papertrail.one/v1',
            'timeout'  => 2.0,
        ], $options));
    }


    public function getClient()
    {
        return $this->client;
    }

    protected function request($method, $path, $params, $opts)
    {
        return $this->getClient()->request($method, $path, static::formatParams($params), $opts);
    }

    public function customers()
    {
        return new CustomerService($this->getClient());
    }

    public function quotations()
    {
        return new CustomerService($this->getClient());
    }


}
