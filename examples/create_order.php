<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';

use MyCloud\Api\Model\Customer;
use MyCloud\Api\Model\DeliveryMode;
use MyCloud\Api\Model\Order;
use MyCloud\Api\Model\Product;

try {
	$products = Product::all();
	$customers = Customer::all();
	$delivery_modes = DeliveryMode::all();

	$createOrder = new Order();

	$attachment = 'RECEIPT';
	$filename = 'TestReceipt.jpg';
	$filetype = 'image/jpeg';
	$filepath = '/Users/time/Downloads/TestReceipt.jpg';


	$createOrder->setName('Tim Endres')
		->setPhoneNumber('+66909168068')
		->setEmail('tim@bkbasic.com')
		->setAddress('#5 Sukhumvit Soi 45, Wattana, Bangkok')
		->setPostcode('10110')
		->setCustomer( $customers[0] )
		->setDeliveryMode( $delivery_modes[0] )
		->addProduct( $products[0], 1, 1200 )
		->addProduct( $products[1], 2, 900 )
		->attachFile( $attachment, $filename, $filetype, $filepath );

	$order = $createOrder->create();

	if ( $order instanceof MCError ) {
		print "ERROR creating Order:" . PHP_EOL;
		print "      " . $order->getMessage() . PHP_EOL;
	} else {
		print "Created Order:" . PHP_EOL;
		print "ORDER[" . $order->id . "]" . PHP_EOL;
		print "   status: " . $order->status . PHP_EOL;
		print "   shopId: " . $order->shop_id . PHP_EOL;
		print "   mcNumber: " . $order->mc_number . PHP_EOL;
		print "   Order # " . $order->order_number . PHP_EOL;
		print "   Weight: " . $order->weight . PHP_EOL;
		print "   Shipping Information:" . PHP_EOL;
		print "      Name: " . $order->name . PHP_EOL;
		print "      Address: " . $order->address . PHP_EOL;
		print "      PostCode: " . $order->postcode . PHP_EOL;
		print "      Phone # " . $order->phone_number . PHP_EOL;

		$customer = $order->getCustomer();
		if ( ! empty($customer) ) {
			print "   Customer[" . $customer->id . "]" . PHP_EOL;
			print "      Code: " . $customer->code . PHP_EOL;
			print "      Name: " . $customer->name . PHP_EOL;
			print "      Address: " . $customer->address . PHP_EOL;
			print "      Postcode: " . $customer->postcode . PHP_EOL;
			print "      SocialID: " . $customer->social_id . PHP_EOL;
			print "      Phone # " . $customer->phone_number . PHP_EOL;
			print "      E-mail: " . $customer->email . PHP_EOL;
			print "      Note: " . $customer->note . PHP_EOL;
		} else {
			print "   HAS NO Customer" . PHP_EOL;
		}

		$delivery_mode = $order->getDeliveryMode();
		if ( ! empty($delivery_mode) ) {
			print "   DeliveryMode[" . $delivery_mode->id . "]" . PHP_EOL;
			print "      Name: " . $delivery_mode->name . PHP_EOL;
			print "      Code: " . $delivery_mode->code . PHP_EOL;
			print "      Contact: " . $delivery_mode->contact . PHP_EOL;
		} else {
			print "   HAS NO deliveryMode" . PHP_EOL;
		}

		print "   --- Order Items ------------------------" . PHP_EOL;
		foreach ( $order->getOrderItems() as $order_item ) {
			print "   OrderItem[" . $order_item->id . "]" . PHP_EOL;
			print "      Price: " . $order_item->price . PHP_EOL;
			print "      Quantity: " . $order_item->quantity . PHP_EOL;
			$product = $order_item->getProduct();
			if ( ! empty($product) ) {
				print "      --- Product -------------------------" . PHP_EOL;
				print "          Product[" . $product->id . "]" . PHP_EOL;
				print "          SKU: " . $product->sku . PHP_EOL;
				print "          Name: " . $product->name . PHP_EOL;
				print "          Description: " . $product->description . PHP_EOL;
				print "          PhotoUrl: " . $product->photo_url . PHP_EOL;
				print "          SupplierRef: " . $product->supplier_ref . PHP_EOL;
			}
		}
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
