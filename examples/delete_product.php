<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use MyCloud\Api\Core\MCError;
use MyCloud\Api\Model\Product;

// ARGUMENTS:
//   [1] Product ID

// NOTE If no error is returned, the original Product object is returned
//      but it is deleted from the database and can no longer be used.
//
if ( count($argv) != 2 ) {
	print "Incorrect arguments. Usage:" . PHP_EOL;
	print "    php " . $argv[0] . " productId" . PHP_EOL;
} else {
	try {
		$deleteProduct = new Product();
		$deleteProduct->setId( $argv[1] );

		$product = $deleteProduct->delete();

		if ( $product instanceof MCError ) {
			print "ERROR deleting product:" . PHP_EOL;
			print "      " . $product->getMessage() . PHP_EOL;
		} else {
			print "Product with ID " . $product->getId() . " has been deleted.". PHP_EOL;
		}
	} catch ( Exception $ex ) {
		print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
	}
}
