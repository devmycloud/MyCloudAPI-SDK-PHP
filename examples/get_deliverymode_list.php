<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';

use \MyCloud\Api\Model\DeliveryMode;

try {
	$delivery_modes = DeliveryMode::all( array(), null );

	print "Retrieved " . count($delivery_modes) . " delivery modes." . PHP_EOL;
	foreach ( $delivery_modes as $delivery_mode ) {
		print "DELIVERY_MODE[" . $delivery_mode->id . "]" . PHP_EOL;
		print "   shopId " . $delivery_mode->shop_id . PHP_EOL;
		print "   Delivery Code " . $delivery_mode->delivery_code . PHP_EOL;
		print "   Name " . $delivery_mode->name . PHP_EOL;
		print "   Contact " . $delivery_mode->contact . PHP_EOL;
		print "   Is Available? " . ($delivery_mode->available ? 'YES' : 'NO') . PHP_EOL;
	}
} catch ( Exception $ex ) {
	print "EXCEPTION: " . $ex->getMessage() . PHP_EOL;
}
