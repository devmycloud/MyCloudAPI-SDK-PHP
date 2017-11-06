<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use MyCloud\Api\Core\MCError;
use MyCloud\Api\Model\Customer;

// ARGUMENTS:
//   [1] Customer Code
//   [2] Customer Name
//   [3] Customer Address
//   [4] Customer Postcode
//   [5] Customer PhoneNumber
//   [6] Customer Email
//   [7] Customer SocialId
//   [8] Customer Note

if ( count($argv) != 9 ) {
	print "Incorrect arguments. Usage:" . PHP_EOL;
	print "    php " . $argv[0] . " code name address postcode phoneNumber email socialId note" . PHP_EOL;
} else {
	try {
		$createCustomer = new Customer();
		// NOTE That we do not set the ID.

		$createCustomer->setCode($argv[1])
			->setName($argv[2])
			->setAddress($argv[3])
			->setPostcode($argv[4])
			->setPhoneNumber($argv[5])
			->setEmail($argv[6])
			->setSocialId($argv[7])
			->setNote($argv[8]);

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
}
