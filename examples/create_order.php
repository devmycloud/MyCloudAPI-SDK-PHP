<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use MyCloud\Api\Core\MCError;
use MyCloud\Api\Model\Customer;
use MyCloud\Api\Model\DeliveryMode;
use MyCloud\Api\Model\Order;
use MyCloud\Api\Model\Product;

// ARGUMENTS:
//   [1] Order Name
//   [2] Order Address
//   [3] Order Postcode
//   [4] Order PhoneNumber
//   [5] Order Email
//   [6] Order Attachment Name     (e.g., 'RECEIPT')
//   [7] Order Attachment Filename (e.g., 'ProductImage.jpg')
//   [8] Order Attachment Filetype (e.g., 'image/jpeg')
//   [9] Order Attachment Filepath (path to actual local file to upload)

if ( count($argv) != 10 ) {
	print "Incorrect arguments. Usage:" . PHP_EOL;
	print "    php " . $argv[0] . " name address postcode phoneNumber email " .
		"attachmentName attachmentFileName attachmentFileType attachmentFilePath" . PHP_EOL;
	print "Where:" . PHP_EOL;
	print "   attachmentName is the 'tag' assigned to the attachment (e.g., RECEIPT)" . PHP_EOL;
	print "   attachmentFileName is the name of the attachment file (reference only)" . PHP_EOL;
	print "   attachmentFileType is the MIME type of the attachment file (e.g., image/jpg)" . PHP_EOL;
	print "   attachmentFilePath is the path to the file on the local computer" . PHP_EOL;
} else {
	try {
		$products = Product::all();
		$customers = Customer::all();
		$delivery_modes = DeliveryMode::all();

		$createOrder = new Order();

		$attachment = $argv[6];
		$filename = $argv[7];
		$filetype = $argv[8];
		$filepath = $argv[9];

		$createOrder->setName($argv[1])
			->setAddress($argv[2])
			->setPostcode($argv[3])
			->setPhoneNumber($argv[4])
			->setEmail($argv[5])
			->attachFile( $attachment, $filename, $filetype, $filepath );

		// Only set the customer if we got a model from the API
		if ( count($customers) > 0 ) {
			$createOrder->setCustomer( $customers[0] );
		}

		// Only set the delivery mode if we got a model from the API
		if ( count($delivery_modes) > 0 ) {
			$createOrder->setDeliveryMode( $delivery_modes[0] );
		}

		// Only add products if we got models from the API
		// NOTE addProduct() is a shortcut for adding an OrderItem.
		//      Adding a product will create the OrderItem that ties
		//      that product to this order.
		if ( count($products) > 0 ) {
			$createOrder->addProduct( $products[0], 1, 1200 );
		}
		if ( count($products) > 1 ) {
			$createOrder->addProduct( $products[1], 1, 1200 );
		}

		$order = $createOrder->create();

		if ( $order instanceof MCError ) {
			print "ERROR creating Order:" . PHP_EOL;
			print "      " . $order->getMessage() . PHP_EOL;
		} else {
			print_order( "Created Order", $order );
		}
	} catch ( Exception $ex ) {
		print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
	}
}
