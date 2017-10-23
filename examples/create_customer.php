<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';

use MyCloud\Api\Model\Customer;

try {
	$createCustomer = new Customer();

	$createCustomer->setCode('TESTCODE')
		->setName('Tim Endres')
		->setAddress('#5 Sukhumvit Soi 45, Wattana, Bangkok')
		->setPostcode('10110')
		->setPhoneNumber('+66909168068')
		->setEmail('tim@bkbasic.com')
		->setSocialId('myFaceBookName')
		->setNote('This is a customer note.');

	$customer = $createCustomer->create();

	if ( $customer instanceof MCError ) {
		print "ERROR creating Customer:" . PHP_EOL;
		print "      " . $order->getMessage() . PHP_EOL;
	} else {
		print "Created Customer:" . PHP_EOL;
		print "CUSTOMER[" . $customer->id . "]" . PHP_EOL;
		print "   shopId: " . $customer->shop_id . PHP_EOL;
		print "   Code: " . $customer->code . PHP_EOL;
		print "   Name: " . $customer->name . PHP_EOL;
		print "   Address: " . $customer->address . PHP_EOL;
		print "   Postcode: " . $customer->postcode . PHP_EOL;
		print "   Phone # : " . $customer->phone_number . PHP_EOL;
		print "   SocialID: " . $customer->social_id . PHP_EOL;
		print "   E-mail: " . $customer->email . PHP_EOL;
		print "   Note: " . $customer->note . PHP_EOL;
	}
} catch ( Exception $ex ) {
	print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
}
