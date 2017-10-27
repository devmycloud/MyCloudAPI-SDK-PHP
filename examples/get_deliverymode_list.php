<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';
require 'printers.php';

use \MyCloud\Api\Model\DeliveryMode;

try {
	$delivery_modes = DeliveryMode::all( array(), null );

	print "Retrieved " . count($delivery_modes) . " delivery modes." . PHP_EOL;
	foreach ( $delivery_modes as $delivery_mode ) {
		print_delivery_mode( "DeliveryMode", $delivery_mode );
	}
} catch ( Exception $ex ) {
	print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
}
