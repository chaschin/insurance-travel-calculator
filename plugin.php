<?php
/**
 * Insurance Travel Calculator
 *
 * @package Calculator
 *
 * Plugin Name: Insurance Travel Calculator
 * Plugin URI: https://github.com/chaschin/calculator
 * Description: Insurance Travel Calculator
 * Version: 1.0.0.alpha.01
 * Author: Alexey Chaschin
 * Author URI: https://github.com/chaschin
 * Text Domain: insurance-travel-calculator
 */

if ( ! function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a theme, not much I can do when called directly.';
	exit;
}

define( 'PLUGIN_CALCULATOR_DIR', plugin_dir_path( __FILE__ ) );
define( 'PLUGIN_CALCULATOR_URL', plugin_dir_url( __FILE__ ) );

define( 'PLUGIN_CALCULATOR_VER', '1.0.0' );

require_once( PLUGIN_CALCULATOR_DIR . 'vendor/autoload.php' );
require_once( PLUGIN_CALCULATOR_DIR . 'src/autoload.php' );

Calculator::get_instance();
