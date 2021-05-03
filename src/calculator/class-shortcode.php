<?php
/**
 * Insurance Travel Calculator
 *
 * @package WordPress
 * @subpackage use Calculator;
 */

namespace Calculator;

use Calculator\Traits\Singleton;
use Calculator\Company;
use Calculator\Template;

/**
 * Shortcode class
 */
class Shortcode {

	use Singleton;

	/**
	 * Initialization
	 */
	private function __construct() {
		add_shortcode( 'it-calculator', array( $this, 'get_shortcode_html' ) );
	}

	/**
	 * Get Shortcode HTML
	 *
	 * @param mixed $atts Shortcode attributes.
	 * @return string HTML.
	 */
	public function get_shortcode_html( $atts ): string {
		$html = '';
		$data = array();
		$atts = $atts ?? array();
		$data['atts'] = shortcode_atts(
			array(
				'title' => '',
			),
			$atts
		);

		$data['image_url']    = PLUGIN_CALCULATOR_URL . 'img/';
		$data['destinations'] = Company::get_instance()->get_destinations();
		$data['options']      = Company::get_instance()->get_options();
		$data['companies']    = Company::get_instance()->get_companies();
		$data['passenger']    = array( '', '' );
		$data['direction']    = array();
		$data['travel_date']  = array(
			'arrival' => '',
			'return'  => '',
		);
		$data['option']       = array();

		$data['translations'] = array(
			'Shortcode Title'                       => __( 'Shortcode Title', 'insurance-travel-calculator' ),
			'Passenger'                             => __( 'Passenger', 'insurance-travel-calculator' ),
			'Title for a column with form'          => __( 'Title for a column with form', 'insurance-travel-calculator' ),
			'Companies column title'                => __( 'Companies column title', 'insurance-travel-calculator' ),
			'Results column title'                  => __( 'Results column title', 'insurance-travel-calculator' ),
			'Date of birth'                         => __( 'Date of birth', 'insurance-travel-calculator' ),
			'Bottom legal note'                     => __( 'Bottom legal note', 'insurance-travel-calculator' ),
			'Arrival date'                          => __( 'Arrival date', 'insurance-travel-calculator' ),
			'Return date'                           => __( 'Return date', 'insurance-travel-calculator' ),
			'For purchase online'                   => __( 'For purchase online', 'insurance-travel-calculator' ),
			'For purchase through a representative' => __( 'For purchase through a representative', 'insurance-travel-calculator' ),
			'1 step title'                          => __( '1 step title', 'insurance-travel-calculator' ),
			'2 step title'                          => __( '2 step title', 'insurance-travel-calculator' ),
			'3 step title'                          => __( '3 step title', 'insurance-travel-calculator' ),
			'4 step title'                          => __( '4 step title', 'insurance-travel-calculator' ),
			'1 step notice'                         => __( '1 step notice', 'insurance-travel-calculator' ),
			'2 step notice'                         => __( '2 step notice', 'insurance-travel-calculator' ),
			'3 step notice'                         => __( '3 step notice', 'insurance-travel-calculator' ),
			'4 step notice'                         => __( '4 step notice', 'insurance-travel-calculator' ),
			'1 step result title'                   => __( '1 step result title', 'insurance-travel-calculator' ),
			'2 step result title'                   => __( '2 step result title', 'insurance-travel-calculator' ),
			'3 step result title'                   => __( '3 step result title', 'insurance-travel-calculator' ),
			'4 step result title'                   => __( '4 step result title', 'insurance-travel-calculator' ),
		);
		$html = Template::get_instance()->render( 'shortcode', $data );
		return $html;
	}

}
