<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use MyCloud\Api\Core\MCError;
use MyCloud\Api\Model\ProductCategory;

// ARGUMENTS:
//   [1] Category Name

if ( count($argv) != 2 ) {
	print "Incorrect arguments. Usage:" . PHP_EOL;
	print "    php " . $argv[0] . " categoryName" . PHP_EOL;
} else {
	try {
		$createCategory = new ProductCategory();

		$createCategory->setName($argv[1]);

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
}
