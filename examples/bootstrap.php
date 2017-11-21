<?php

function root_dir( $dir, $levels ) {
	if ( $levels == 0 ) {
		return $dir;
	} else {
		return root_dir( dirname($dir), --$levels );
	}
}

$debug = FALSE;
$loadable = TRUE;

date_default_timezone_set('Asia/Bangkok');

// First, we will assume that this is the development project pulled from
// git with a top level vendor directory created by 'composer install'
$topDir = root_dir( __FILE__, 2 );
if ( $debug ) {
	print "1) Path[top] '" . $topDir . "'" . PHP_EOL;
}

$composerAutoload = implode( DIRECTORY_SEPARATOR, array( $topDir, "vendor", "autoload.php" ) );
	
if ( ! file_exists($composerAutoload) ) {
	// Okay, so this is a standard composer installation, with the full
	// 'vendor/mycloudth/rest-api-php-sdk' hierarchy
	if ( $debug ) {
		print "   '" . $composerAutoload . "' NOT FOUND". PHP_EOL;
	}
	$topDir = root_dir( __FILE__, 4 );
	if ( $debug ) {
		print "2) Path[top] '" . $topDir . "'" . PHP_EOL;
	}

	$composerAutoload = $topDir . DIRECTORY_SEPARATOR . 'autoload.php';
	if ( ! file_exists($composerAutoload) ) {
		$loadable = FALSE;
		if ( $debug ) {
			print "   '" . $composerAutoload . "' NOT FOUND". PHP_EOL;
		}
	}
}

if ( $loadable ) {
	if ( $debug ) {
		print "Loading: '" . $composerAutoload . "'" . PHP_EOL;
	}
	require $composerAutoload;
} else {
	print "Could not bootstrap the example program: (examples/boostrap.php)" . PHP_EOL;
	print "If this is a project built with composer:" . PHP_EOL;
	print "   Bootstrap could not locate the vendor folder." . PHP_EOL;
	print "   You must run 'composer update' to resolve dependencies." . PHP_EOL;
	print "If this is a binary distribution of the API:" . PHP_EOL;
	print "   Bootstrap could not locate the autoload.php file." . PHP_EOL;
	print "   Please contact support for a proper binary distribution" . PHP_EOL;
	exit(1);
}
