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
//   [2] Pickable Flag (1 or 0, 'TRUE' or 'FALSE')

if ( count($argv) != 3 ) {
	print "Incorrect arguments. Usage:" . PHP_EOL;
	print "    php " . $argv[0] . " orderId pickableFlag" . PHP_EOL;
} else {
	try {
		$updateOrder = new Order();
		var_export($updateOrder);
		$updateOrder->setId($argv[1]);
		$updateOrder->can_pick =
			($argv[2] == '1' || strtoupper($argv[2]) == 'TRUE') ? 1 : 0;

		$order = $updateOrder->update();

		if ( $order instanceof MCError ) {
			print "ERROR updating order:" . PHP_EOL;
			print "      " . $order->getMessage() . PHP_EOL;
		} else {
			print_order( "Updated Order", $order );
		}
	} catch ( Exception $ex ) {
		print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
	}
}
