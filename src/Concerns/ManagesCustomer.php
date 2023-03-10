<?php
namespace Papertrail\Concerns;

use Papertrail\Exceptions\CustomerAlreadyCreated;
use Papertrail\Papertrail;
use Papertrail\Resources\Customer;

trait ManagesCustomer {

    public function papertrailFields()
    {
        return ['is_company', 'company_name', 'firstname', 'lastname', 'tax_id', 'street', 'number', 'city', 'country', 'zip_code'];
    }

    public function papertrailId()
    {
        return $this->papertrail_id;
    }

    public function hasPapertrailId()
    {
        return (bool) $this->papertrailId();
    }

    public function syncPapertrailCustomerDetails()
    {
        return $this->updatePapertrailCustomer([
            'name' => $this->name,
            'email' => $this->email,
        ]);
    }

    public function createOrGetStripeCustomer(array $options = [])
    {
        if ($this->hasPapertrailId()) {
            return $this->asPapertrailCustomer();
        }

        return $this->createAsPapertrailCustomer($options);
    }

    public function updatePapertrailCustomer(array $options = [])
    {
        return $this->papertrail()->customers->update(
            $this->papertrail_id, $options
        );
    }

    public static function papertrail(array $options = [])
    {
        //return Papertrail::client($options);
        return new Papertrail();
    }

    public function createAsCustomer(array $options = [])
    {
        if ($this->hasPapertrailId()) {
            throw CustomerAlreadyCreated::exists($this);
        }
        

        if (!array_key_exists('email', $options)) {
            $options['email'] = $this->email;
        }


        ;
        // Here we will create the customer instance on Stripe and store the ID of the
        // user from Stripe. This ID will correspond with the Stripe user instances
        // and allow us to retrieve users from Stripe later when we need to work.
        $customer = new Customer($this->papertrail()->customers()->create($options));

        $this->papertrail_id = $customer->id;

        $this->save();

        return $customer;
    }

    public function asCustomer()
    {
        if (!$this->hasPapertrailId()) {
            // change
            throw CustomerAlreadyCreated::exists($this);
        }

        return new Customer($this->papertrail()->customers()->retrieve($this->papertrailId(), []));
    }

    public function updateCustomer($data)
    {
        return new Customer($this->papertrail()->customers()->update($this->papertrailId(), ['form_params' => $data]));
    }




}
