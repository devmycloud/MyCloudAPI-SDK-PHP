<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';

use MyCloud\Api\Core\MCError;
use MyCloud\Api\Model\Order;

try {
	$order = Order::get( $argv[1] );

	if ( $order instanceof MCError ) {
		print "ERROR retrieving order:" . PHP_EOL;
		print "      " . $order->getMessage() . PHP_EOL;
	} else {
		print "ORDER[" . $order->id . "]" . PHP_EOL;
		print "   status " . $order->status . PHP_EOL;
		print "   shopId " . $order->shop_id . PHP_EOL;
		print "   mcNumber " . $order->mc_number . PHP_EOL;
		print "   Order # " . $order->order_number . PHP_EOL;
		print "   Weight " . $order->weight . PHP_EOL;
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
			print "   HAS NO DeliveryMode" . PHP_EOL;
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
