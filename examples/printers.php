<?php
//
// Provide common printing functions for all models
//
function print_category( $title, $category ) {
	print $title . "[" . $category->id . "]" . PHP_EOL;
	print "   Code: " . $category->code . PHP_EOL;
	print "   Name: " . $category->name . PHP_EOL;
}

function print_customer( $title, $customer ) {
	print $title . "[" . $customer->id . "]" . PHP_EOL;
	print "   Code: " . $customer->code . PHP_EOL;
	print "   Name: " . $customer->name . PHP_EOL;
	print "   Address: " . $customer->address . PHP_EOL;
	print "   Postcode: " . $customer->postcode . PHP_EOL;
	print "   SocialID: " . $customer->social_id . PHP_EOL;
	print "   Phone # " . $customer->phone_number . PHP_EOL;
	print "   E-mail: " . $customer->email . PHP_EOL;
	print "   Note: " . $customer->note . PHP_EOL;
}

function print_delivery_mode( $title, $delivery_mode ) {
	print $title . "[" . $delivery_mode->id . "]" . PHP_EOL;
	print "   Delivery Code " . $delivery_mode->delivery_code . PHP_EOL;
	print "   Name " . $delivery_mode->name . PHP_EOL;
	print "   Contact " . $delivery_mode->contact . PHP_EOL;
	print "   Is Available? " . ($delivery_mode->available ? 'YES' : 'NO') . PHP_EOL;
}

function print_order( $title, $order ) {
	print $title . "[" . $order->id . "]" . PHP_EOL;
	print "   status: " . $order->status . PHP_EOL;
	print "   mcNumber: " . $order->mc_number . PHP_EOL;
	print "   Order # " . $order->order_number . PHP_EOL;
	print "   Billing Title: " . $order->billing_title . PHP_EOL;
	print "   Customer Reference: " . $order->customer_reference . PHP_EOL;
	print "   CreateDate: " . $order->create_date . PHP_EOL;
	print "   DeliveryDate: " . $order->delivery_date . PHP_EOL;
	print "   Total Price: " . $order->total_price . PHP_EOL;
	print "   Weight: " . $order->weight . PHP_EOL;
	print "   Urgent: " . ($order->urgent ? 'Yes' : 'No') . PHP_EOL;
	print "   BitlyCode: " . $order->bitly_code . PHP_EOL;
	print "   BitlyUrl: " . $order->bitly_url . PHP_EOL;
	print "   Shipping Information:" . PHP_EOL;
	print "      Name: " . $order->name . PHP_EOL;
	print "      Address: " . $order->address . PHP_EOL;
	print "      PostCode: " . $order->postcode . PHP_EOL;
	print "      Phone # " . $order->phone_number . PHP_EOL;
	print "   Payment Information:" . PHP_EOL;
	print "      Amount: " . $order->payment_amount . PHP_EOL;
	print "      Date: " . $order->payment_date . PHP_EOL;
	print "      Time: " . $order->payment_time . PHP_EOL;

	if ( empty($order->attachments) ) {
		print "   HAS NO Attachments" . PHP_EOL;
	} else {
		print "   Attachments:" . PHP_EOL;
		foreach ( $order->attachments as $idx => $attachment ) {
			print "      [" . ($idx + 1) . "] Name: " . $attachment['name'] . PHP_EOL;
			print "      [" . ($idx + 1) . "] Url: " . $attachment['url'] . PHP_EOL;
		}
	}

	$customer = $order->getCustomer();
	if ( empty($customer) ) {
		print "   HAS NO Customer" . PHP_EOL;
	} else {
		print "   Customer[" . $customer->id . "]" . PHP_EOL;
		print "      Code: " . $customer->code . PHP_EOL;
		print "      Name: " . $customer->name . PHP_EOL;
		print "      Address: " . $customer->address . PHP_EOL;
		print "      Postcode: " . $customer->postcode . PHP_EOL;
		print "      SocialID: " . $customer->social_id . PHP_EOL;
		print "      Phone # " . $customer->phone_number . PHP_EOL;
		print "      E-mail: " . $customer->email . PHP_EOL;
		print "      Note: " . $customer->note . PHP_EOL;
	}

	$delivery_mode = $order->getDeliveryMode();
	if ( empty($delivery_mode) ) {
		print "   HAS NO DeliveryMode" . PHP_EOL;
	} else {
		print "   DeliveryMode[" . $delivery_mode->id . "]" . PHP_EOL;
		print "      Name: " . $delivery_mode->name . PHP_EOL;
		print "      Code: " . $delivery_mode->code . PHP_EOL;
		print "      Contact: " . $delivery_mode->contact . PHP_EOL;
	}

	print "   --- Order Items ------------------------" . PHP_EOL;
	foreach ( $order->getOrderItems() as $order_item ) {
		print "   OrderItem[" . $order_item->id . "]" . PHP_EOL;
		print "      Quantity: " . $order_item->quantity . PHP_EOL;
		print "      Price: " . $order_item->price . PHP_EOL;
		$product = $order_item->getProduct();
		if ( ! empty($product) ) {
			print "      --- Product -------------------------" . PHP_EOL;
			print "          Product[" . $product->id . "]" . PHP_EOL;
			print "          SKU: " . $product->sku . PHP_EOL;
			print "          ShopSKU: " . $product->shop_sku . PHP_EOL;
			print "          Name: " . $product->name . PHP_EOL;
			print "          Description: " . $product->description . PHP_EOL;
			print "          PhotoUrl: " . $product->photo_url . PHP_EOL;
			print "          SupplierRef: " . $product->supplier_reference . PHP_EOL;
			print "          ClientReference[1]: " . $product->getClientReference(0) . PHP_EOL;
			print "          ClientReference[2]: " . $product->getClientReference(1) . PHP_EOL;
			print "          ClientReference[3]: " . $product->getClientReference(2) . PHP_EOL;
			print "          ClientReference[4]: " . $product->getClientReference(3) . PHP_EOL;
		}
	}
}

function print_order_item( $title, $order_item ) {
	print $title . "[" . $order_item->id . "]" . PHP_EOL;
	print "   Quantity: " . $order_item->quantity . PHP_EOL;
	print "   Price: " . $order_item->price . PHP_EOL;
	if ( ! empty($order_item->product) ) {
		print_product( "   ------------ Product", $order_item->product );
	}
}

function print_product( $title, $product ) {
	print $title . "[" . $product->id . "]" . PHP_EOL;
	print "   SKU: " . $product->sku . PHP_EOL;
	print "   ShopSKU: " . $product->shop_sku . PHP_EOL;
	print "   Name: " . $product->name . PHP_EOL;
	print "   Description: " . $product->description . PHP_EOL;
	print "   PhotoUrl: " . $product->photo_url . PHP_EOL;
	print "   SupplierRef: " . $product->supplier_reference . PHP_EOL;
	print "   Physical Inventory: " . $product->physical_inventory . PHP_EOL;
	print "   Reserved Inventory: " . $product->reserved_inventory . PHP_EOL;
	print "   Available Inventory: " . $product->available_inventory . PHP_EOL;
	print "   ClientReference[1]: " . $product->getClientReference(0) . PHP_EOL;
	print "   ClientReference[2]: " . $product->getClientReference(1) . PHP_EOL;
	print "   ClientReference[3]: " . $product->getClientReference(2) . PHP_EOL;
	print "   ClientReference[4]: " . $product->getClientReference(3) . PHP_EOL;
}

function print_shop( $title, $shop ) {
	print $title . PHP_EOL;
	print "   Available: " . $shop->available . PHP_EOL;
	print "   Code: " . $shop->code . PHP_EOL;
	print "   Name: " . $shop->name . PHP_EOL;
	print "   Logo: " . $shop->logo_url . PHP_EOL;
	print "   Address: " . $shop->address . PHP_EOL;
	print "   Postcode: " . $shop->postcode . PHP_EOL;
	print "   Contact: " . $shop->contact_name . PHP_EOL;
	print "   Phone # " . $shop->phone_number . PHP_EOL;
	print "   E-mail: " . $shop->email . PHP_EOL;
}
