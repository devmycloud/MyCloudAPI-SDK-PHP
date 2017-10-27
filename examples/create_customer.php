<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

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
		print "      " . $customer->getMessage() . PHP_EOL;
	} else {
		print_customer( "Created Customer", $customer );
	}
} catch ( Exception $ex ) {
	print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
}
