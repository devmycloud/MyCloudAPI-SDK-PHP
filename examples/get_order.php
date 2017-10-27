<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use MyCloud\Api\Core\MCError;
use MyCloud\Api\Model\Order;

try {
	$order = Order::get( $argv[1] );

	if ( $order instanceof MCError ) {
		print "ERROR retrieving order:" . PHP_EOL;
		print "      " . $order->getMessage() . PHP_EOL;
	} else {
		print_order( "Order", $order );
	}
} catch ( Exception $ex ) {
	print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
}
