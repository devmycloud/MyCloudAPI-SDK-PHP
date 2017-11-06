<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use MyCloud\Api\Core\MCError;
use MyCloud\Api\Model\OrderItem;

// ARGUMENTS:
//   [1] OrderItem ID

if ( count($argv) != 2 ) {
	print "Incorrect arguments. Usage:" . PHP_EOL;
	print "    php " . $argv[0] . " orderItemId" . PHP_EOL;
} else {
	try {
		$order_item = OrderItem::get( $argv[1] );

		if ( $order_item instanceof MCError ) {
			print "ERROR retrieving order item:" . PHP_EOL;
			print "      " . $order_item->getMessage() . PHP_EOL;
		} else {
			print_order_item( "OrderItem", $order_item );
		}
	} catch ( Exception $ex ) {
		print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
	}
}
