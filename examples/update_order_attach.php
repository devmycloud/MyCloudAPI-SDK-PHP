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
//   [2] Attachment name
//   [3] Attachment filename
//   [4] Attachment file type (e.g., JPG, GIF, PNG)
//   [5] Full path to file to attach (including file name)

// NOTE Order image attachment updates check the name of existing attachments.
//      If the name of the update attachment matches an existing attachment, then
//      the existing attachment's image will be replaced by the update image. If
//      no existing attachment name matches the update name, then the update
//      will be attached as a new attachment.
//
if ( count($argv) < 6 ) {
	print "Not enough arguments. Usage:" . PHP_EOL;
	print "    php " . $argv[0] . " orderId name fileName fileType filePath" . PHP_EOL;
} else {
	try {
		$updateOrder = new Order();
		$updateOrder->setId( $argv[1] );
		$updateOrder->attachFile( $argv[2], $argv[3], $argv[4], $argv[5] );

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
