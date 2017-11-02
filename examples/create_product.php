<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use MyCloud\Api\Core\MCError;
use MyCloud\Api\Model\Product;

$filename = 'ProductImage.jpg';
$filetype = 'image/jpeg';
$filepath = '/Users/time/Downloads/ProductImage.jpg';

// ARGUMENTS:
//   [1] Order Name
//   [2] Order Description
//   [3] Order SupplierReference
//   [4] Photo Filename  (e.g., 'ProductPhoto.jpg')
//   [5] Order Filetype  (e.g., 'image/jpeg')
//   [6] Order Filepath  (path to actual file to upload)
//   [7-10] ClientReference 1 thru 4
//          If argument not provided, reference is not set.

try {
	$createProduct = new Product();

	// NOTE You can set up to 4 client references
	$argcnt = count($argv);
	$client_references = array();
	if ( $argcnt > 7 ) {
		$client_references[] = $argv[7];
	}
	if ( $argcnt > 8 ) {
		$client_references[] = $argv[8];
	}
	if ( $argcnt > 9 ) {
		$client_references[] = $argv[9];
	}
	if ( $argcnt > 10 ) {
		$client_references[] = $argv[10];
	}

	$createProduct
		->setName($argv[1])
		->setDescription($argv[2])
		->setSupplierReference($argv[3])
		->setClientReferences( $client_references )
		->setPhoto( $argv[4], $argv[5], $argv[6] );

	$product = $createProduct->create();

	if ( $product instanceof MCError ) {
		print "ERROR creating Product:" . PHP_EOL;
		print "      " . $product->getMessage() . PHP_EOL;
	} else {
		print_product( "Created Product", $product );
	}
} catch ( Exception $ex ) {
	print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
}
