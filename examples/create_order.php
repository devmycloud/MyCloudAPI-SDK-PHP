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
		$createOrder->addProduct( $products[0], 1, 1200 )
	}
	if ( count($products) > 1 ) {
		$createOrder->addProduct( $products[1], 1, 1200 )
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

/*
 * Common stack trace when API KEY is not set properly, almost always caused
 * by not setting MCAPI_CONFIG_PATH or a bad config file.
 *
PHP Fatal error:  Uncaught exception 'MyCloud\Api\Exception\MCConnectionException' with message 'Got Http response code 404 when accessing http://api.mycloudfulfillment.com/v1/gettoken.' in /Users/time/Work/Back2Basics/MyCloud/API/MyCloudAPI-SDK-PHP/lib/MyCloud/Api/Core/MCHttpConnection.php:204
Stack trace:
#0 /Users/time/Work/Back2Basics/MyCloud/API/MyCloudAPI-SDK-PHP/lib/MyCloud/Api/Core/MCAuthenticator.php(109): MyCloud\Api\Core\MCHttpConnection->execute('/v1/gettoken', 'POST', Array, NULL)
#1 /Users/time/Work/Back2Basics/MyCloud/API/MyCloudAPI-SDK-PHP/lib/MyCloud/Api/Core/ApiContext.php(54): MyCloud\Api\Core\MCAuthenticator->getToken()
#2 /Users/time/Work/Back2Basics/MyCloud/API/MyCloudAPI-SDK-PHP/lib/MyCloud/Api/Core/MyCloudModel.php(303): MyCloud\Api\Core\ApiContext->getToken()
#3 /Users/time/Work/Back2Basics/MyCloud/API/MyCloudAPI-SDK-PHP/lib/MyCloud/Api/Model/Order.php(181): MyCloud\Api\Core\MyCloudModel::executeCall('/v1/orders', 'POST', '{"name":"Tim En...', Array, NULL)
#4 /Users/time/Work/Back2Basics/MyCloud/API/My in /Users/time/Work/Back2Basics/MyCloud/API/MyCloudAPI-SDK-PHP/lib/MyCloud/Api/Core/MCHttpConnection.php on line 204

*/

/*
 * Stack trace from a 500 error
 *
MacProTim-2:examples time$ php create_order.php
PHP Fatal error:  Uncaught exception 'MyCloud\Api\Exception\MCConnectionException' with message 'Got Http response code 500 when accessing http://api.mycloudfulfillment.com:4848/api/v1/orders.' in /Users/time/Work/Back2Basics/MyCloud/API/MyCloudAPI-SDK-PHP/lib/MyCloud/Api/Core/MCHttpConnection.php:204
Stack trace:
#0 /Users/time/Work/Back2Basics/MyCloud/API/MyCloudAPI-SDK-PHP/lib/MyCloud/Api/Core/MyCloudModel.php(308): MyCloud\Api\Core\MCHttpConnection->execute('/v1/orders', 'POST', Array, Array)
#1 /Users/time/Work/Back2Basics/MyCloud/API/MyCloudAPI-SDK-PHP/lib/MyCloud/Api/Model/Order.php(236): MyCloud\Api\Core\MyCloudModel::executeCall('/v1/orders', 'POST', Array, Array, NULL)
#2 /Users/time/Work/Back2Basics/MyCloud/API/MyCloudAPI-SDK-PHP/examples/create_order.php(28): MyCloud\Api\Model\Order->create()
#3 {main}
  thrown in /Users/time/Work/Back2Basics/MyCloud/API/MyCloudAPI-SDK-PHP/lib/MyCloud/Api/Core/MCHttpConnection.php on line 204

Fatal error: Uncaught exception 'MyCloud\Api\Exception\MCConnectionException' with message 'Got Http response code 500 when accessing http://api.mycloudfulfillment.com:4848/api/v1/orders.' in /Users/time/Work/Back2Basics/MyCloud/API/MyCloudAPI-SDK-PHP/lib/MyCloud/Api/Core/MCHttpConnection.php on line 204

MyCloud\Api\Exception\MCConnectionException: Got Http response code 500 when accessing http://api.mycloudfulfillment.com:4848/api/v1/orders. in /Users/time/Work/Back2Basics/MyCloud/API/MyCloudAPI-SDK-PHP/lib/MyCloud/Api/Core/MCHttpConnection.php on line 204

Call Stack:
    0.0003     232552   1. {main}() /Users/time/Work/Back2Basics/MyCloud/API/MyCloudAPI-SDK-PHP/examples/create_order.php:0
    0.2718    1118624   2. MyCloud\Api\Model\Order->create() /Users/time/Work/Back2Basics/MyCloud/API/MyCloudAPI-SDK-PHP/examples/create_order.php:28
    0.2718    1121400   3. MyCloud\Api\Core\MyCloudModel::executeCall() /Users/time/Work/Back2Basics/MyCloud/API/MyCloudAPI-SDK-PHP/lib/MyCloud/Api/Model/Order.php:236
    0.2726    1123320   4. MyCloud\Api\Core\MCHttpConnection->execute() /Users/time/Work/Back2Basics/MyCloud/API/MyCloudAPI-SDK-PHP/lib/MyCloud/Api/Core/MyCloudModel.php:308
 *
 */
