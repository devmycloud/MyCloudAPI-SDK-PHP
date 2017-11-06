<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use MyCloud\Api\Core\MCError;
use MyCloud\Api\Model\Product;

// ARGUMENTS:
//   [1] Product ID
//   [1] Product Name

if ( count($argv) != 2 ) {
	print "Incorrect arguments. Usage:" . PHP_EOL;
	print "    php " . $argv[0] . " productId newName" . PHP_EOL;
} else {
	try {
		$updateProduct = new Product();
		$updateProduct->id = $argv[1];
		$updateProduct->name = $argv[2];

		$product = $updateProduct->update();

		if ( $product instanceof MCError ) {
			print "ERROR updating product:" . PHP_EOL;
			print "      " . $product->getMessage() . PHP_EOL;
		} else {
			print_product( "Updated Product", $product );
		}
	} catch ( Exception $ex ) {
		print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
	}
}
