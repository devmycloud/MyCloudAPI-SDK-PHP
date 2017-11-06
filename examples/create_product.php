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
//   [1] Product Name
//   [2] Product Description
//   [3] Product SupplierReference
//   [4] Product Photo Filename  (e.g., 'ProductPhoto.jpg')
//   [5] Product Photo Filetype  (e.g., 'image/jpeg')
//   [6] Product Photo Filepath  (path to actual file to upload)
//   [7-10] ClientReference 1 thru 4
//          If argument not provided, reference is not set.

if ( count($argv) < 7 || count($argv) > 11 ) {
	print "Incorrect arguments. Usage:" . PHP_EOL;
	print "    php " . $argv[0] . " name description supplierReference " .
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
}
