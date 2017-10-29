<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use MyCloud\Api\Core\MCError;
use \MyCloud\Api\Model\DeliveryMode;

try {
	$delivery_mode = DeliveryMode::get( $argv[1] );

	if ( $delivery_mode instanceof MCError ) {
		print "ERROR retrieving delivery mode:" . PHP_EOL;
		print "      " . $delivery_mode->getMessage() . PHP_EOL;
	} else {
		print_delivery_mode( "DeliveryMode", $delivery_mode );
	}
} catch ( Exception $ex ) {
	print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
}
