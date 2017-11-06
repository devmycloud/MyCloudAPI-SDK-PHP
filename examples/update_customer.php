<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use MyCloud\Api\Core\MCError;
use MyCloud\Api\Model\Customer;

// ARGUMENTS:
//   [1] Customer ID
//   [2...] Remaining arguments are of the format:
//
//             attribute_name=attribute_value
//
//          For example: 'name=Tom Cruise'

if ( count($argv) < 3 ) {
	print "Incorrect arguments. Usage:" . PHP_EOL;
	print "    php " . $argv[0] . " customerId updateSpec [updateSpec...]" . PHP_EOL;
	print "Where:" . PHP_EOL;
	print "    updateSpec is 'attributeName=value' (e.g., 'name=Tom Cruise')" . PHP_EOL;
} else {
	try {
		$argcnt = count($argv);
		$updateCustomer = new Customer();
		$updateCustomer->id = $argv[1];

		$argidx = 2;
		while ( $argidx < $argcnt ) {
			$exp = explode( "=", $argv[$argidx] );
			if ( count($exp) == 1 ) {
				$updateCustomer->$exp[0] = '';
			} elseif ( count($exp) == 2 ) {
				$updateCustomer->$exp[0] = $exp[1];
			} else {
				print "ARGUMENT '" . $argv[$argidx] . "' not valid.";
			}
			$argidx++;
		}

		$customer = $updateCustomer->update();

		if ( $customer instanceof MCError ) {
			print "ERROR updating customer:" . PHP_EOL;
			print "      " . $customer->getMessage() . PHP_EOL;
		} else {
			print_customer( "Updated Customer", $customer );
		}
	} catch ( Exception $ex ) {
		print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
	}
}
