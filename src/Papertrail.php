<?php

namespace Papertrail;

use Stripe\StripeClient;

class Papertrail {

    public static function client(array $options = [])
    {
        return new StripeClient(array_merge([
            'api_key' => $options['api_key'] ?? config('cashier.secret'),
            'stripe_version' => static::STRIPE_VERSION,
            'api_base' => static::$apiBaseUrl,
        ], $options));
    }

}
