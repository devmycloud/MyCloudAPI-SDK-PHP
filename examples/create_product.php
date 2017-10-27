<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use MyCloud\Api\Model\Product;

$filename = 'ProductImage.jpg';
$filetype = 'image/jpeg';
$filepath = '/Users/time/Downloads/ProductImage.jpg';


try {
	$createProduct = new Product();

	$createProduct
		->setName('Cool Product #1')
		->setDescription('This product is so cool, you really want to but it!')
		->setSupplierReference('SR-001-0023123')
		->setClientReferences( array( 'ClientRef1', 'ClientRef2' ) )
		->setPhoto( $filename, $filetype, $filepath );

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
