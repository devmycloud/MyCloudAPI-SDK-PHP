<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use MyCloud\Api\Core\MCError;
use MyCloud\Api\Model\Customer;

try {
	$customers = Customer::all();

	if ( $customers instanceof MCError ) {
		print "ERROR retrieving customer list:" . PHP_EOL;
		print "      " . $customers->getMessage() . PHP_EOL;
	} else {
		print "Retrieved " . count($customers) . " customers." . PHP_EOL;
		foreach ( $customers as $customer ) {
			print_customer( "Customer", $customer );
		}
	}
} catch ( Exception $ex ) {
	print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
}
