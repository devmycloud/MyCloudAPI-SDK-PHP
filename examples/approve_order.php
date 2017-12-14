<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use MyCloud\Api\Core\MCError;
use MyCloud\Api\Model\Order;
use MyCloud\Api\Model\OrderItem;
use MyCloud\Api\Model\Product;

// ARGUMENTS:
//   [1] Order ID

if ( count($argv) != 2 ) {
	print "Incorrect arguments. Usage:" . PHP_EOL;
	print "    php " . $argv[0] . " orderId" . PHP_EOL;
} else {
	try {
		$updateOrder = new Order();
		$updateOrder->setId( $argv[1] );
		$updateOrder->setStatus( Order::API_STATUS_APPROVED );

		$order = $updateOrder->update();

		if ( $order instanceof MCError ) {
			print "ERROR approving order:" . PHP_EOL;
			print "      " . $order->getMessage() . PHP_EOL;
		} else {
			print_order( "Updated Order status to 'Approved'", $order );
		}
	} catch ( Exception $ex ) {
		print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
	}
}
