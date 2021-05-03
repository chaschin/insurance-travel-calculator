<?php
/**
 * Insurance Travel Calculator
 *
 * @package WordPress
 * @subpackage Calculator
 */

namespace Calculator\Traits;

/**
 * Trait Singleton
 */
trait Singleton {
	/**
	 * Class instance
	 *
	 * @var [type]
	 */
	public static $instance = null;

	/**
	 * Get Instance
	 *
	 * @return self
	 */
	public static function get_instance() {
		$class = __CLASS__;
		self::$instance = is_null( self::$instance ) ? new $class() : self::$instance;

		return self::$instance;
	}

	/**
	 * Construct
	 */
	private function __construct() {
	}

	/**
	 * Clone
	 *
	 * @return void
	 */
	public function __clone() {
	}

	/**
	 * Wake Up
	 */
	public function __wakeup() {
	}
}
