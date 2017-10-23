<?php

namespace MyCloud\Api\Core;

class MCUtil
{

	public static function root_dir( $dir, $levels ) {
		if ( $levels == 0 ) {
			return $dir;
		} else {
			return self::root_dir( dirname($dir), --$levels );
		}
	}
}
