<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use MyCloud\Api\Core\MCError;
use \MyCloud\Api\Model\OrderItem;

// ARGUMENTS:
//   [1] Order ID

if ( count($argv) != 2 ) {
	print "Incorrect arguments. Usage:" . PHP_EOL;
	print "    php " . $argv[0] . " orderId" . PHP_EOL;
} else {
	try {
		$order_items = OrderItem::forOrderId( $argv[1], null );

		if ( $order_items instanceof MCError ) {
			print "ERROR retrieving orderItem list:" . PHP_EOL;
			print "      " . $order_items->getMessage() . PHP_EOL;
		} else {
			foreach ( $order_items as $order_item ) {
				print_order_item( "OrderItem", $order_item );
			}
		}
	} catch ( Exception $ex ) {
		print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
	}
}
