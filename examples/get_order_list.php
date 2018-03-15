<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use MyCloud\Api\Core\MCError;
use \MyCloud\Api\Model\Order;

// ARGUMENTS:
//   [1] List offset
//   [2] Product count

$offset = 0;
$count = 10;

if ( $argc > 1 ) { $offset = $argv[1]; }
if ( $argc > 2 ) { $count = $argv[2]; }

try {
	$orders = Order::all( array( 'offset' => $offset, 'count' => $count ) );

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
