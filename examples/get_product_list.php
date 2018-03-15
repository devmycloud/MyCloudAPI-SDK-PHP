<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use MyCloud\Api\Core\MCError;
use MyCloud\Api\Model\Product;

// ARGUMENTS:
//   [1] List offset
//   [2] Product count

$offset = 0;
$count = 10;

if ( $argc > 1 ) { $offset = $argv[1]; }
if ( $argc > 2 ) { $count = $argv[2]; }

try {
	$products = Product::all( array( 'offset' => $offset, 'count' => $count ) );

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
