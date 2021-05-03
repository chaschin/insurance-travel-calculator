<?php
/**
 * Insurance Travel Calculator
 *
 * @package WordPress
 * @subpackage Calculator
 */

use Calculator\Template;
use Calculator\Shortcode;
use Calculator\Company;
use Calculator\Traits\Singleton;

/**
 * Calculator class
 */
class Calculator {

	use Singleton;

	/**
	 * Initialization
	 */
	private function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_and_scripts' ) );

		add_filter( 'locale', array( $this, 'get_locale' ) );

		$this->load_language( 'insurance-travel-calculator' );

		Shortcode::get_instance();
		Company::get_instance();

		add_action( 'wp_ajax_it_calculate', array( $this, 'it_calculate' ) );
		add_action( 'wp_ajax_nopriv_it_calculate', array( $this, 'it_calculate' ) );
	}

	public function it_calculate() {
		$post_data = $_POST;
		$data = array(
			'companies'    => Company::get_instance()->get_companies( $post_data ),
			'translations' => array(
				'For purchase online'                   => __( 'For purchase online', 'insurance-travel-calculator' ),
				'For purchase through a representative' => __( 'For purchase through a representative', 'insurance-travel-calculator' ),
			),
		);
		if ( isset( $post_data['option'] ) ) {
			$options = Company::get_instance()->get_options( $post_data['option'] );
			$post_data['option'] = $options;
		}
		if ( isset( $post_data['direction'] ) ) {
			$options = Company::get_instance()->get_destinations( $post_data['direction'] );
			$post_data['direction'] = $options;
		}
		$post_data['translations'] = array(
			'1 step result title' => __( '1 step result title', 'insurance-travel-calculator' ),
			'2 step result title' => __( '2 step result title', 'insurance-travel-calculator' ),
			'3 step result title' => __( '3 step result title', 'insurance-travel-calculator' ),
			'4 step result title' => __( '4 step result title', 'insurance-travel-calculator' ),
			'Passenger'           => __( 'Passenger', 'insurance-travel-calculator' ),
		);
		$result = array(
			'results'   => Template::get_instance()->render( 'info/results', $post_data ),
			'companies' => Template::get_instance()->render( 'info/companies', $data ),
			'direction' => $post_data['direction'],
		);
		echo json_encode( $result );
		wp_die();
	}

	/**
	 * Render template
	 *
	 * @param string $template_name Template name.
	 * @param array  $data All template params in array.
	 * @return void
	 */
	public static function render( string $template_name, array $data ) {
		$html = Template::get_instance()->render( $template_name, $data );
		echo do_shortcode( $html );
	}

	/**
	 * Load translation language
	 *
	 * @param string $domain Translation key.
	 * @param string $lang_dir Language dir.
	 * @return void
	 */
	public function load_language( string $domain, string $lang_dir = 'languages/' ) {
		$current_locale = get_locale();
		if ( ! empty( $current_locale ) ) {
			$mo_file = PLUGIN_CALCULATOR_DIR . $lang_dir . $current_locale . '.mo';
			load_textdomain( $domain, $mo_file );
		}
	}

	/**
	 * Get locale
	 *
	 * @param string $locale Locale name.
	 * @return string
	 */
	public function get_locale( string $locale ) {
		return $locale;
	}

	/**
	 * Enqueue plugin styles and scripts
	 *
	 * @return void
	 */
	public function enqueue_styles_and_scripts() {
		wp_enqueue_style(
			'client',
			PLUGIN_CALCULATOR_URL . 'css/client.min.css',
			array(),
			PLUGIN_CALCULATOR_VER
		);
		wp_enqueue_style(
			'jquery-ui',
			'https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css',
			array(),
			'1.12.1'
		);

		if ( ! is_admin() ) {
			wp_deregister_script( 'jquery' );
			wp_deregister_script( 'jquery-migrate' );
			wp_enqueue_script(
				'jquery',
				includes_url( 'js/jquery/jquery.js' ),
				array(),
				PLUGIN_CALCULATOR_VER,
				true
			);
			wp_enqueue_script(
				'jquery-migrate',
				includes_url( 'js/jquery/jquery-migrate.min.js' ),
				array(),
				PLUGIN_CALCULATOR_VER,
				true
			);
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'jquery-ui-tooltip' );
			wp_enqueue_script(
				'datepicker-he',
				PLUGIN_CALCULATOR_URL . 'js/datepicker-he.js',
				array( 'jquery', 'jquery-migrate', 'jquery-ui-datepicker' ),
				PLUGIN_CALCULATOR_VER,
				true
			);
			wp_enqueue_script(
				'client',
				PLUGIN_CALCULATOR_URL . 'js/client.min.js',
				array( 'jquery', 'jquery-migrate', 'jquery-ui-datepicker', 'datepicker-he' ),
				PLUGIN_CALCULATOR_VER,
				true
			);
			wp_localize_script(
				'client',
				'script_data',
				array(
					'admin_ajax_url' => admin_url( 'admin-ajax.php' ),
				)
			);
		}
	}
}
