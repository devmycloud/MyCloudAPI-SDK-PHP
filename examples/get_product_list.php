<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';

use MyCloud\Api\Core\MCError;
use MyCloud\Api\Model\Product;

try {
	$products = Product::all();

	if ( $products instanceof MCError ) {
		print "ERROR retrieving product list:" . PHP_EOL;
		print "      " . $products->getMessage() . PHP_EOL;
	} else {
		print "Retrieved " . count($products) . " products." . PHP_EOL;
		foreach ( $products as $product ) {
			print "PRODUCT[" . $product->id . "]" . PHP_EOL;
			print "   shopId: " . $product->shop_id . PHP_EOL;
			print "   SKU: " . $product->sku . PHP_EOL;
			print "   Name: " . $product->name . PHP_EOL;
			print "   Description: " . $product->description . PHP_EOL;
			print "   PhotoUrl: " . $product->photo_url . PHP_EOL;
			print "   SupplierRef: " . $product->supplier_ref . PHP_EOL;
			print "   Reference[1]: " . $product->reference_1 . PHP_EOL;
			print "   Reference[2]: " . $product->reference_2 . PHP_EOL;
			print "   Reference[3]: " . $product->reference_3 . PHP_EOL;
			print "   Reference[4]: " . $product->reference_4 . PHP_EOL;
		}
	}
} catch ( Exception $ex ) {
	print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
}
