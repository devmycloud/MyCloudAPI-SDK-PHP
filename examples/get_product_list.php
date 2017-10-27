<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

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
			print_product( "Product", $product );
		}
	}
} catch ( Exception $ex ) {
	print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
}
