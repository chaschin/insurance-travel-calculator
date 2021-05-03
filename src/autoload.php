<?php
/**
 * Anonymous function that registers a custom autoloader
 *
 * @package WordPress
 * @subpackage Calculator
 */

spl_autoload_register(
	function ( $class ) {
		$base_dir = PLUGIN_CALCULATOR_DIR . 'src/';
		$file = str_replace( '\\', '/', $class );
		$parts = explode( '/', $file );
		$prefix = 'class';
		if ( in_array( 'Traits', $parts ) ) {
			$prefix = 'trait';
		}
		$parts[ count( $parts ) - 1 ] = $prefix . '-' . $parts[ count( $parts ) - 1 ] . '.php';
		$file = implode( '/', $parts );
		$file = str_replace( '_', '-', strtolower( $file ) );
		if ( file_exists( $base_dir . $file ) ) {
			include_once $base_dir . $file;
		}
	}
);
