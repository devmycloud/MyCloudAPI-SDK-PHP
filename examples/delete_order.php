<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use MyCloud\Api\Core\MCError;
use MyCloud\Api\Model\Order;

// ARGUMENTS:
//   [1] Order ID

// NOTE If no error is returned, the original Order object is returned
//      but it is deleted from the database and can no longer be used.
//
if ( count($argv) != 2 ) {
	print "Incorrect arguments. Usage:" . PHP_EOL;
	print "    php " . $argv[0] . " orderId" . PHP_EOL;
} else {
	try {
		$deleteOrder = new Order();
		$deleteOrder->setId( $argv[1] );

		$order = $deleteOrder->delete();

		if ( $order instanceof MCError ) {
			print "ERROR deleting order:" . PHP_EOL;
			print "      " . $order->getMessage() . PHP_EOL;
		} else {
			print "Order with ID " . $order->getId() . " has been deleted.". PHP_EOL;
		}
	} catch ( Exception $ex ) {
		print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
	}
}
