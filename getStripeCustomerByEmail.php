<?php

use Stripe\StripeClient;

Class getStripeCustomerByEmail {

	public $email;
	public $customer;
	private $stripe_secret = 'YOUR-SECRET-KEY'; // better to load this from config

    public function __construct($email)
    {
        $this->email = $email;
    }

    public function lookup()
    {
        $stripe_client = new StripeClient($this->stripe_secret);

		$previous_customer = null;
		$target_customer = null;

		while(true)
		{
			$customers = $previous_customer ? $stripe_client->customers->all(array("limit" => 100, "starting_after" => $previous_customer)) : $stripe_client->customers->all(array("limit" => 100));

		    foreach($customers->autoPagingIterator() as $customer)
		    {
		        if ($customer->email == $this->email) {
		            $target_customer = $customer;
		            break 2;
		        }
		    }
		    if (!$customers->has_more) {
		        break;
		    }
		    $previous_customer = end($customers->data);
		}

		$this->customer = $target_customer;

		return $this->customer
    }

}