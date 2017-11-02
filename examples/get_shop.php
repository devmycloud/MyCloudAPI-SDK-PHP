<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use MyCloud\Api\Core\MCError;
use MyCloud\Api\Model\Shop;

// ARGUMENTS:
//    None Currently only one shop per client

try {
	$shop = Shop::get(0);

	if ( $shop instanceof MCError ) {
		print "ERROR retrieving shop:" . PHP_EOL;
		print "      " . $shop->getMessage() . PHP_EOL;
	} else {
		print_shop( "Shop", $shop );
	}
} catch ( Exception $ex ) {
	print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
}
