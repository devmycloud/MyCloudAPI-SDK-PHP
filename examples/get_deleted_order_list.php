<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use MyCloud\Api\Core\MCError;
use \MyCloud\Api\Model\Order;

try {
	$orders = Order::all( array('deleted' => 'true'), null );

	if ( $orders instanceof MCError ) {
		print "ERROR retrieving order list:" . PHP_EOL;
		print "      " . $order->getMessage() . PHP_EOL;
	} else {
		foreach ( $orders as $order ) {
			print_order( "Order", $order );
		}
	}
} catch ( Exception $ex ) {
	print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
}
