<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use MyCloud\Api\Core\MCError;
use MyCloud\Api\Model\ProductCategory;

try {
	$categories = ProductCategory::all();

	if ( $categories instanceof MCError ) {
		print "ERROR retrieving ProductCategory list:" . PHP_EOL;
		print "      " . $categories->getMessage() . PHP_EOL;
	} else {
		print "Retrieved " . count($categories) . " product categories." . PHP_EOL;
		foreach ( $categories as $category ) {
			print_category( "ProductCategory", $category );
		}
	}
} catch ( Exception $ex ) {
	print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
}
