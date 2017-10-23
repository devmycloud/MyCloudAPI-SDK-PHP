<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';

use MyCloud\Api\Core\MCError;
use MyCloud\Api\Model\Customer;

try {
	$customer = Customer::get( $argv[1] );

	if ( $customer instanceof MCError ) {
		print "ERROR retrieving customer:" . PHP_EOL;
		print "      " . $customer->getMessage() . PHP_EOL;
	} else {
		print "Customer[" . $customer->id . "]" . PHP_EOL;
		print "   shopId: " . $customer->shop_id . PHP_EOL;
		print "   Code: " . $customer->code . PHP_EOL;
		print "   Name: " . $customer->name . PHP_EOL;
		print "   Address: " . $customer->address . PHP_EOL;
		print "   Postcode: " . $customer->postcode . PHP_EOL;
		print "   SocialID: " . $customer->social_id . PHP_EOL;
		print "   Phone # " . $customer->phone_number . PHP_EOL;
		print "   E-mail: " . $customer->email . PHP_EOL;
		print "   Note: " . $customer->note . PHP_EOL;
	}
} catch ( Exception $ex ) {
	print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
}
