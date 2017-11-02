<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use MyCloud\Api\Core\MCError;
use MyCloud\Api\Model\Customer;

// ARGUMENTS:
//   [1] Customer ID

try {
	$customer = Customer::get( $argv[1] );

	if ( $customer instanceof MCError ) {
		print "ERROR retrieving customer:" . PHP_EOL;
		print "      " . $customer->getMessage() . PHP_EOL;
	} else {
		print_customer( "Customer", $customer );
	}
} catch ( Exception $ex ) {
	print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
}
