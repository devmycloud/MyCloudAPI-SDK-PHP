<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use MyCloud\Api\Core\MCError;
use MyCloud\Api\Model\Order;
use MyCloud\Api\Model\OrderItem;
use MyCloud\Api\Model\Product;

try {
	$updateOrder = new Order();
	$updateOrder->setId($argv[1]);
	$updateOrder->setStatus($argv[2]);

	$updateItem = new OrderItem($updateOrder, NULL, $argv[4], $argv[5] );
	$updateItem->setId($argv[3]);

	$updateOrder->addOrderItem( $updateItem );

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
