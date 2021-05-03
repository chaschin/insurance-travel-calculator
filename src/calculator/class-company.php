<?php
/**
 * Insurance Travel Calculator
 *
 * @package WordPress
 * @subpackage use Calculator;
 */

namespace Calculator;

use Calculator\Traits\Singleton;
use Calculator\Template;

/**
 * Company class
 */
class Company {

	use Singleton;

	/**
	 * Initialization
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'custom_post_type_init' ) );
		add_action( 'plugins_loaded', array( $this, 'init_acf_settings' ) );
	}

	/**
	 * Custom post type INIT
	 *
	 * @return void
	 */
	public function custom_post_type_init() {
		$labels = array(
			'name'               => _x( 'Company', 'post type general name', 'insurance-travel-calculator' ),
			'singular_name'      => _x( 'Company', 'post type singular name', 'insurance-travel-calculator' ),
			'menu_name'          => _x( 'Company', 'admin menu', 'insurance-travel-calculator' ),
			'name_admin_bar'     => _x( 'Company', 'add new on admin bar', 'insurance-travel-calculator' ),
			'add_new'            => _x( 'Add New', 'book', 'insurance-travel-calculator' ),
			'add_new_item'       => __( 'Add New Company', 'insurance-travel-calculator' ),
			'new_item'           => __( 'New Company', 'insurance-travel-calculator' ),
			'edit_item'          => __( 'Edit Company', 'insurance-travel-calculator' ),
			'view_item'          => __( 'View Company', 'insurance-travel-calculator' ),
			'all_items'          => __( 'All Company', 'insurance-travel-calculator' ),
			'search_items'       => __( 'Search Company', 'insurance-travel-calculator' ),
			'parent_item_colon'  => __( 'Parent Company:', 'insurance-travel-calculator' ),
			'not_found'          => __( 'No companies found.', 'insurance-travel-calculator' ),
			'not_found_in_trash' => __( 'No companies found in Trash.', 'insurance-travel-calculator' ),
		);
		$args = array(
			'public'             => false,
			'labels'             => $labels,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => false,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array(
				'title',
				'editor',
			),
		);
		register_post_type( 'company', $args );

		$labels = array(
			'name'                       => _x( 'Destinations', 'taxonomy general name' ),
			'singular_name'              => _x( 'Destination', 'taxonomy singular name' ),
			'search_items'               => __( 'Search Destinations' ),
			'popular_items'              => __( 'Popular Destinations' ),
			'all_items'                  => __( 'All Destinations' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Destination' ),
			'update_item'                => __( 'Update Destination' ),
			'add_new_item'               => __( 'Add New Destination' ),
			'new_item_name'              => __( 'New Destination Name' ),
			'separate_items_with_commas' => __( 'Separate destinations with commas' ),
			'add_or_remove_items'        => __( 'Add or remove destinations' ),
			'choose_from_most_used'      => __( 'Choose from the most used destinations' ),
			'menu_name'                  => __( 'Destinations' ),
		);

		register_taxonomy(
			'destination',
			'company',
			array(
				'hierarchical' => false,
				'labels'       => $labels,
				'query_var'    => true,
			)
		);

		$labels = array(
			'name'                       => _x( 'Options', 'taxonomy general name' ),
			'singular_name'              => _x( 'Option', 'taxonomy singular name' ),
			'search_items'               => __( 'Search Options' ),
			'popular_items'              => __( 'Popular Options' ),
			'all_items'                  => __( 'All Options' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Option' ),
			'update_item'                => __( 'Update Option' ),
			'add_new_item'               => __( 'Add New Option' ),
			'new_item_name'              => __( 'New Option Name' ),
			'separate_items_with_commas' => __( 'Separate options with commas' ),
			'add_or_remove_items'        => __( 'Add or remove options' ),
			'choose_from_most_used'      => __( 'Choose from the most used options' ),
			'menu_name'                  => __( 'Options' ),
		);

		register_taxonomy(
			'option',
			'company',
			array(
				'hierarchical' => false,
				'labels'       => $labels,
				'query_var'    => true,
			)
		);
	}

	/**
	 * Get Companies
	 *
	 * @param array $post_data $_POST data.
	 * @return array
	 */
	public function get_companies( $post_data = array() ): array {
		$result = [];
		$args = array(
			'post_type' => 'company',
		);
		$companies = get_posts( $args );

		foreach ( $companies as $company ) {
			$company_logo = get_field( 'company_logo', $company->ID );
			$logo = '';
			if ( $company_logo ) {
				$logo = $company_logo['sizes']['thumbnail'];
			}

			$destinations = get_field( 'destinations', $company->ID );
			$destinations = is_array( $destinations ) ? $destinations : array();
			foreach ( $destinations as &$destination ) {
				$destination['fee'] = floatval( $destination['fee'] );
			}
			$options      = get_field( 'options', $company->ID );
			$options     = is_array( $options ) ? $options : array();
			foreach ( $options as &$option ) {
				$option['fee'] = floatval( $option['fee'] );
			}

			$c = array(
				'id'           => $company->ID,
				'name'         => $company->post_title,
				'logo'         => $logo,
				'adult_fee'    => floatval( get_field( 'adult_fee', $company->ID ) ),
				'infant_fee'   => floatval( get_field( 'infant_fee', $company->ID ) ),
				'child_fee'    => floatval( get_field( 'child_fee', $company->ID ) ),
				'child_fee_2'  => floatval( get_field( 'child_fee_2', $company->ID ) ),
				'destinations' => $destinations,
				'options'      => $options,
				'link_1'       => get_field( 'link_for_purchase_online', $company->ID ),
				'link_2'       => get_field( 'link_for_purchase_through', $company->ID ),
			);

			if ( $post_data ) {
				$c['total_fee'] = $this->calculate_total_fee( $c, $post_data );
			} else {
				$c['total_fee'] = $c['adult_fee'];
			}

			$result[] = $c;
		}
		return $result;
	}

	/**
	 * Calculate total fee
	 *
	 * @param array $company_data Company data.
	 * @param array $post_data $_POST data.
	 * @return float
	 */
	public function calculate_total_fee( array $company_data, array $post_data ): float {
		$total_fee = 0;
		foreach ( $post_data['passenger'] as $passenger_date ) {
			if ( ! empty( $passenger_date ) ) {
				$years = $this->get_full_years( $passenger_date );
				if ( 3 >= $years && $company_data['infant_fee'] ) {
					$total_fee += $company_data['infant_fee'];
				} elseif ( 3 > $years && $years <= 7 && $company_data['child_fee'] ) {
					$total_fee += $company_data['child_fee'];
				} elseif ( 7 > $years && $years <= 14 && $company_data['child_fee_2'] ) {
					$total_fee += $company_data['child_fee_2'];
				} else {
					$total_fee += $company_data['adult_fee'];
				}
			} else {
				$total_fee += $company_data['adult_fee'];
			}
		}
		$post_data['direction'] = array_map(
			function( $a ) {
				return intval( $a );
			},
			$post_data['direction']
		);
		foreach ( $company_data['destinations'] as $option ) {
			foreach ( $post_data['direction'] as $dir ) {
				if ( in_array( $dir, $option['destination'] ) ) {
					$total_fee += $option['fee'];
				}
			}
		}
		$post_data['option'] = array_map(
			function( $a ) {
				return intval( $a );
			},
			$post_data['option']
		);
		foreach ( $company_data['options'] as $option ) {
			foreach ( $post_data['option'] as $opt ) {
				if ( in_array( $opt, $option['option'] ) ) {
					$total_fee += $option['fee'];
				}
			}
		}
		return $total_fee;
	}

	/**
	 * Get full years
	 *
	 * @param  string $d Date.
	 * @return int
	 */
	private function get_full_years( string $d ): int {
		$diff = abs( time() - strtotime( $d ) );
		$years = floor( $diff / ( 365 * 60 * 60 * 24 ) );
		return $years;
	}

	/**
	 * Get company options
	 *
	 * @param array $ids Ids of terms.
	 * @return array
	 */
	public function get_options( array $ids = array() ): array {
		return $this->get_terms( 'option', $ids );
	}

	/**
	 * Get company destinations
	 *
	 * @param array $ids Ids of terms.
	 * @return array
	 */
	public function get_destinations( array $ids = array() ): array {
		return $this->get_terms( 'destination', $ids );
	}

	/**
	 * Get custom terms
	 *
	 * @param string $taxonomy Taxonomy name.
	 * @param array  $ids Ids of terms.
	 * @return array
	 */
	private function get_terms( string $taxonomy, array $ids = array() ): array {
		$args = array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		);
		if ( $ids ) {
			$args['include'] = $ids;
		}
		$terms = get_terms( $args );
		$terms = is_array( $terms ) ? $terms : array();
		return $terms;
	}

	/**
	 * Init ACF Settings
	 *
	 * @return void
	 */
	public function init_acf_settings() {
		if ( function_exists( 'acf_add_local_field_group' ) ) {
			acf_add_local_field_group(
				array(
					'key' => 'group_608f2cfef40d5',
					'title' => __( 'Company Settings', 'insurance-travel-calculator' ),
					'fields' => array(
						array(
							'key' => 'field_608f30c353954',
							'label' => __( 'Company Logo', 'insurance-travel-calculator' ),
							'name' => 'company_logo',
							'type' => 'image',
							'instructions' => '',
							'required' => 1,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'return_format' => 'array',
							'preview_size' => 'thumbnail',
							'library' => 'all',
							'min_width' => '',
							'min_height' => '',
							'min_size' => '',
							'max_width' => '',
							'max_height' => '',
							'max_size' => '',
							'mime_types' => '',
						),
						array(
							'key' => 'field_608f2d16ac9fd',
							'label' => __( 'Adult Fee', 'insurance-travel-calculator' ),
							'name' => 'adult_fee',
							'type' => 'text',
							'instructions' => '',
							'required' => 1,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
						),
						array(
							'key' => 'field_608f2e631432b',
							'label' => __( 'Infant Fee', 'insurance-travel-calculator' ),
							'name' => 'infant_fee',
							'type' => 'text',
							'instructions' => __( 'Up to three years old', 'insurance-travel-calculator' ),
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
						),
						array(
							'key' => 'field_608f2e9a1432d',
							'label' => __( 'Child Fee', 'insurance-travel-calculator' ),
							'name' => 'child_fee',
							'type' => 'text',
							'instructions' => __( 'Three to seven years old', 'insurance-travel-calculator' ),
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
						),
						array(
							'key' => 'field_608f2ed21432e',
							'label' => __( 'Child Fee', 'insurance-travel-calculator' ),
							'name' => 'child_fee_2',
							'type' => 'text',
							'instructions' => __( 'Seven to fourteen years old', 'insurance-travel-calculator' ),
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
						),
						array(
							'key' => 'field_608f2f022b865',
							'label' => __( 'Destinations', 'insurance-travel-calculator' ),
							'name' => 'destinations',
							'type' => 'repeater',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'collapsed' => '',
							'min' => 0,
							'max' => 0,
							'layout' => 'table',
							'button_label' => __( 'Add Destination Fee', 'insurance-travel-calculator' ),
							'sub_fields' => array(
								array(
									'key' => 'field_608f2f852b866',
									'label' => __( 'Destination', 'insurance-travel-calculator' ),
									'name' => 'destination',
									'type' => 'taxonomy',
									'instructions' => '',
									'required' => 1,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'taxonomy' => 'destination',
									'field_type' => 'multi_select',
									'allow_null' => 0,
									'add_term' => 1,
									'save_terms' => 1,
									'load_terms' => 0,
									'return_format' => 'id',
									'multiple' => 0,
								),
								array(
									'key' => 'field_608f30282b867',
									'label' => __( 'Fee', 'insurance-travel-calculator' ),
									'name' => 'fee',
									'type' => 'text',
									'instructions' => '',
									'required' => 1,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'default_value' => '',
									'placeholder' => '',
									'prepend' => '',
									'append' => '',
									'maxlength' => '',
								),
							),
						),
						array(
							'key' => 'field_608f306de5dc7',
							'label' => __( 'Options', 'insurance-travel-calculator' ),
							'name' => 'options',
							'type' => 'repeater',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'collapsed' => '',
							'min' => 0,
							'max' => 0,
							'layout' => 'table',
							'button_label' => __( 'Add Option Fee', 'insurance-travel-calculator' ),
							'sub_fields' => array(
								array(
									'key' => 'field_608f306de5dc8',
									'label' => __( 'Option', 'insurance-travel-calculator' ),
									'name' => 'option',
									'type' => 'taxonomy',
									'instructions' => '',
									'required' => 1,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'taxonomy' => 'option',
									'field_type' => 'multi_select',
									'allow_null' => 0,
									'add_term' => 1,
									'save_terms' => 1,
									'load_terms' => 0,
									'return_format' => 'id',
									'multiple' => 0,
								),
								array(
									'key' => 'field_608f306de5dc9',
									'label' => __( 'Fee', 'insurance-travel-calculator' ),
									'name' => 'fee',
									'type' => 'text',
									'instructions' => '',
									'required' => 1,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'default_value' => '',
									'placeholder' => '',
									'prepend' => '',
									'append' => '',
									'maxlength' => '',
								),
							),
						),
						array(
							'key' => 'field_608f442fe6f55',
							'label' => __( 'Link for purchase online', 'insurance-travel-calculator' ),
							'name' => 'link_for_purchase_online',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
						),
						array(
							'key' => 'field_608f4474e6f56',
							'label' => __( 'Link for purchase through a representative', 'insurance-travel-calculator' ),
							'name' => 'link_for_purchase_through',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
						),
					),
					'location' => array(
						array(
							array(
								'param' => 'post_type',
								'operator' => '==',
								'value' => 'company',
							),
						),
					),
					'menu_order' => 0,
					'position' => 'normal',
					'style' => 'default',
					'label_placement' => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen' => '',
					'active' => true,
					'description' => '',
				)
			);
		}
	}
}
