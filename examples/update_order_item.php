<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use MyCloud\Api\Core\MCError;
use MyCloud\Api\Model\OrderItem;

// ARGUMENTS:
//   [1] OrderItem ID
//   [2] new quantity
//   [3] new price

if ( count($argv) < 4 ) {
	print "Not enough arguments. Usage:" . PHP_EOL;
	print "    php " . $argv[0] . " orderItemId newQuantity newPrice" . PHP_EOL;
} else {
	try {
		$updateItem = new OrderItem();
		$updateItem->id = $argv[1];
		$updateItem->quantity = $argv[2];
		$updateItem->price = $argv[3];

		$order_item = $updateItem->update();

		if ( $order_item instanceof MCError ) {
			print "ERROR updating customer:" . PHP_EOL;
			print "      " . $order_item->getMessage() . PHP_EOL;
		} else {
			print_order_item( "Updated OrderItem", $order_item );
		}
	} catch ( Exception $ex ) {
		print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
	}
}