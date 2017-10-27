<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use MyCloud\Api\Model\ProductCategory;

try {
	$createCategory = new ProductCategory();

	$createCategory->setName('My Category');

	$category = $createCategory->create();

	if ( $category instanceof MCError ) {
		print "ERROR creating ProductCategory:" . PHP_EOL;
		print "      " . $category->getMessage() . PHP_EOL;
	} else {
		print_category( "Created ProductCategory", $category );
	}
} catch ( Exception $ex ) {
	print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
}
