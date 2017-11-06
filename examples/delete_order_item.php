<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use MyCloud\Api\Core\MCError;
use MyCloud\Api\Model\OrderItem;

// ARGUMENTS:
//   [1] OrderItem ID

// NOTE If no error is returned, the original Order object is returned
//      but it is deleted from the database and can no longer be used.
//
if ( count($argv) != 2 ) {
	print "Incorrect arguments. Usage:" . PHP_EOL;
	print "    php " . $argv[0] . " orderItemId" . PHP_EOL;
} else {
	try {
		$deleteItem = new OrderItem();
		$deleteItem->setId( $argv[1] );

		$order_item = $deleteItem->delete();

		if ( $order_item instanceof MCError ) {
			print "ERROR deleting order item:" . PHP_EOL;
			print "      " . $order_item->getMessage() . PHP_EOL;
		} else {
			print "OrderItem with ID " . $order_item->getId() . " has been deleted.". PHP_EOL;
		}
	} catch ( Exception $ex ) {
		print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
	}
}
