<?php
namespace Papertrail\Concerns;

use Papertrail\Papertrail;

trait ManagesCustomer {

    public function papertrailId()
    {
        return $this->papertrail_id;
    }

    public function updateStripeCustomer(array $options = [])
    {
        return $this->papertrail()->customers->update(
            $this->papertrail_id, $options
        );
    }

    public static function papertrail(array $options = [])
    {
        return Papertrail::client($options);
    }
}
