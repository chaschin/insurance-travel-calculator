<?php
/**
 * Insurance Travel Calculator
 *
 * @package WordPress
 * @subpackage Calculator;
 */

namespace Calculator;

use Twig;
use Calculator\Traits\Singleton;

/**
 * Template class
 */
class Template {

	use Singleton;

	/**
	 * Twig instance
	 *
	 * @var Twig\Environment
	 */
	private $twig = null;

	/**
	 * Initialization
	 */
	private function __construct() {
		$loader     = new Twig\Loader\FilesystemLoader( PLUGIN_CALCULATOR_DIR . 'templates' );
		$this->twig = new Twig\Environment(
			$loader,
			array(
				'cache'       => PLUGIN_CALCULATOR_DIR . 'cache',
				'auto_reload' => true,
			)
		);
	}

	/**
	 * Render template
	 *
	 * @param string $template_name Template name.
	 * @param array  $data All template params in array.
	 * @return string
	 */
	public function render( string $template_name, array $data ): string {

		$template = $this->twig->load( $template_name . '.html' );
		$content = $template->render( $data );

		return $content;
	}

}
