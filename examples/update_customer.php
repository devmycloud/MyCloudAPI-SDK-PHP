<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use MyCloud\Api\Core\MCError;
use MyCloud\Api\Model\Customer;

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
