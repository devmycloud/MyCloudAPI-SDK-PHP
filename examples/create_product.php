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
//   [1] Product Shop SKU
//   [2] Product Name
//   [3] Product Description
//   [4] Product SupplierReference
//   [5] Product Photo Filename  (e.g., 'ProductPhoto.jpg')
//   [6] Product Photo Filetype  (e.g., 'image/jpeg')
//   [7] Product Photo Filepath  (path to actual file to upload)
//   [8-11] ClientReference 1 thru 4
//          If argument not provided, reference is not set.

if ( count($argv) < 8 || count($argv) > 12 ) {
	print "Incorrect arguments. Usage:" . PHP_EOL;
	print "    php " . $argv[0] . " shopsku name description supplierReference " .
		"photoFilename photoFiletype photoFilepath [clientReference...]" . PHP_EOL;
	print "Where:" . PHP_EOL;
	print "   photoFilename is the name of the attachment file (reference only)" . PHP_EOL;
	print "   photoFiletype is the MIME type of the attachment file (e.g., image/jpg)" . PHP_EOL;
	print "   photoFilepath is the path to the file on the local computer" . PHP_EOL;
	print "   clientReference (optional) you may specify up to four separate references" . PHP_EOL;
} else {
	try {
		$createProduct = new Product();

		// NOTE You can set up to 4 client references
		$argcnt = count($argv);
		$client_references = array();
		if ( $argcnt > 8 ) {
			$client_references[] = $argv[8];
		}
		if ( $argcnt > 9 ) {
			$client_references[] = $argv[9];
		}
		if ( $argcnt > 10 ) {
			$client_references[] = $argv[10];
		}
		if ( $argcnt > 11 ) {
			$client_references[] = $argv[11];
		}

		$createProduct
			->setShopSKU($argv[1])
			->setName($argv[2])
			->setDescription($argv[3])
			->setSupplierReference($argv[4])
			->setClientReferences( $client_references )
			->setPhoto( $argv[5], $argv[6], $argv[7] );

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
}
