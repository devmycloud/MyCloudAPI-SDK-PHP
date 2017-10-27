<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use MyCloud\Api\Core\MCError;
use MyCloud\Api\Model\ProductCategory;

try {
	$category = ProductCategory::get( $argv[1] );

	if ( $category instanceof MCError ) {
		print "ERROR retrieving ProductCategory:" . PHP_EOL;
		print "      " . $category->getMessage() . PHP_EOL;
	} else {
		print_category( "ProductCategory", $category );
	}
} catch ( Exception $ex ) {
	print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
}
