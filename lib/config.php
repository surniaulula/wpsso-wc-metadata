<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2020-2022 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoWcmdConfig' ) ) {

	class WpssoWcmdConfig {

		public static $cf = array(
			'plugin' => array(
				'wpssowcmd' => array(			// Plugin acronym.
					'version'     => '2.0.0-dev.5',	// Plugin version.
					'opt_version' => '11',		// Increment when changing default option values.
					'short'       => 'WPSSO WCMD',	// Short plugin name.
					'name'        => 'WPSSO Product Metadata for WooCommerce SEO',
					'desc'        => 'GTIN, GTIN-8, GTIN-12 (UPC), GTIN-13 (EAN), GTIN-14, ISBN, MPN, depth, and volume for WooCommerce products and variations.',
					'slug'        => 'wpsso-wc-metadata',
					'base'        => 'wpsso-wc-metadata/wpsso-wc-metadata.php',
					'update_auth' => '',		// No premium version.
					'text_domain' => 'wpsso-wc-metadata',
					'domain_path' => '/languages',

					/**
					 * Required plugin and its version.
					 */
					'req' => array(
						'wpsso' => array(
							'name'          => 'WPSSO Core',
							'home'          => 'https://wordpress.org/plugins/wpsso/',
							'plugin_class'  => 'Wpsso',
							'version_const' => 'WPSSO_VERSION',
							'min_version'   => '13.14.0-dev.5',
						),
						'woocommerce' => array(
							'name'          => 'WooCommerce',
							'home'          => 'https://wordpress.org/plugins/woocommerce/',
							'plugin_class'  => 'WooCommerce',
							'version_const' => 'WC_VERSION',
							'min_version'   => '5.0.0',
						),
					),

					/**
					 * URLs or relative paths to plugin banners and icons.
					 */
					'assets' => array(

						/**
						 * Icon image array keys are '1x' and '2x'.
						 */
						'icons' => array(
							'1x' => 'images/icon-128x128.png',
							'2x' => 'images/icon-256x256.png',
						),
					),

					/**
					 * Library files loaded and instantiated by WPSSO.
					 */
					'lib' => array(
						'submenu' => array(
							'wcmd-general' => 'WC Metadata',
						),
					),
				),
			),
		);

		public static function get_md_config() {

			static $local_cache = null;

			if ( null !== $local_cache ) {

				return $local_cache;
			}

			if ( ! class_exists( 'WpssoUtilWoocommerce' ) ) {	// Just in case.

				return $local_cache = array();			// Must return an array.
			}

			/**
			 * WpssoSchema::get_data_unit_text() returns a https://schema.org/unitText value (for example, 'cm', 'ml',
			 * 'kg', etc.).
			 */
			$dimension_unit_text     = WpssoSchema::get_data_unit_text( 'depth' );
			$dimension_unit_wc_text  = get_option( 'woocommerce_dimension_unit', $dimension_unit_text );
			$dimension_unit_wc_label = WpssoUtilWoocommerce::get_dimension_label( $dimension_unit_wc_text );
			$dimension_unit_wc_1x    = WpssoUtilWoocommerce::get_dimension( 1, $dimension_unit_text, $dimension_unit_wc_text );

			$fl_vol_unit_text     = WpssoSchema::get_data_unit_text( 'fluid_volume' );
			$fl_vol_unit_wc_text  = get_option( 'woocommerce_fluid_volume_unit', $fl_vol_unit_text );
			$fl_vol_unit_wc_label = WpssoUtilWoocommerce::get_fluid_volume_label( $fl_vol_unit_wc_text );
			$fl_vol_unit_wc_1x    = WpssoUtilWoocommerce::get_fluid_volume( 1, $fl_vol_unit_text, $fl_vol_unit_wc_text );

			$weight_unit_text     = WpssoSchema::get_data_unit_text( 'weight' );
			$weight_unit_wc_text  = get_option( 'woocommerce_weight_unit', $weight_unit_text );
			$weight_unit_wc_label = WpssoUtilWoocommerce::get_weight_label( $weight_unit_wc_text );
			$weight_unit_wc_1x    = WpssoUtilWoocommerce::get_weight( 1, $weight_unit_text, $weight_unit_wc_text );

			/**
			 * Metadata options will be down in the order listed here.
			 */
			$local_cache = array(
				'product_mfr_part_no' => array(
					'label'      => _x( 'Product MPN', 'option label', 'wpsso-wc-metadata' ),
					'desc'       => __( '%1$s refers to a product Manufacturer Part Number (MPN).', 'wpsso-wc-metadata' ),
					'searchable' => true,
					'type'       => 'text',
					'actions'    => array(
						'woocommerce_product_options_sku'       => true,
						'woocommerce_variation_options_pricing' => true,
					),
					'filters' => array(
						'woocommerce_display_product_attributes' => true,
					),
					'defaults' => array(
						'wcmd_enable'       => 1,
						'wcmd_info_label'   => 'Manufacturer Part Number',
						'wcmd_input_holder' => 'Part number',	// Capitalize the first word.
						'wcmd_input_label'  => 'MPN',
						'plugin_cf'         => '_wpsso_product_mfr_part_no',
					),
					'options' => array(
						'plugin_attr' => '',
					),
				),
				'product_isbn' => array(
					'label'      => _x( 'Product ISBN', 'option label', 'wpsso-wc-metadata' ),
					'desc'       => __( '%1$s refers to an ISBN code (aka International Standard Book Number).', 'wpsso-wc-metadata' ),
					'searchable' => true,
					'type'       => 'text',
					'actions'    => array(
						'woocommerce_product_options_sku'       => true,
						'woocommerce_variation_options_pricing' => true,
					),
					'filters' => array(
						'woocommerce_display_product_attributes' => true,
					),
					'defaults' => array(
						'wcmd_enable'       => 0,
						'wcmd_info_label'   => 'ISBN',
						'wcmd_input_holder' => 'Book number',	// Capitalize the first word.
						'wcmd_input_label'  => 'ISBN',
						'plugin_cf'         => '_wpsso_product_isbn',
					),
					'options' => array(
						'plugin_attr' => '',
					),
				),
				'product_gtin14' => array(
					'label'      => _x( 'Product GTIN-14', 'option label', 'wpsso-wc-metadata' ),
					'desc'       => __( '%1$s refers to a product GTIN-14 code (aka ITF-14).', 'wpsso-wc-metadata' ),
					'type'       => 'text',
					'searchable' => true,
					'actions'    => array(
						'woocommerce_product_options_sku'       => true,
						'woocommerce_variation_options_pricing' => true,
					),
					'filters' => array(
						'woocommerce_display_product_attributes' => true,
					),
					'defaults' => array(
						'wcmd_enable'       => 0,
						'wcmd_info_label'   => 'GTIN-14',
						'wcmd_input_holder' => '14-digit bar code',	// Capitalize the first word.
						'wcmd_input_label'  => 'GTIN-14',
						'plugin_cf'         => '_wpsso_product_gtin14',
					),
					'options' => array(
						'plugin_attr' => '',
					),
				),
				'product_gtin13' => array(
					'label'      => _x( 'Product GTIN-13 (EAN)', 'option label', 'wpsso-wc-metadata' ),
					'desc'       => __( '%1$s refers to a product GTIN-13 code (aka 13-digit ISBN codes or EAN/UCC-13).', 'wpsso-wc-metadata' ),
					'type'       => 'text',
					'searchable' => true,
					'actions'    => array(
						'woocommerce_product_options_sku'       => true,
						'woocommerce_variation_options_pricing' => true,
					),
					'filters' => array(
						'woocommerce_display_product_attributes' => true,
					),
					'defaults' => array(
						'wcmd_enable'       => 1,
						'wcmd_info_label'   => 'EAN',
						'wcmd_input_holder' => '13-digit bar code',	// Capitalize the first word.
						'wcmd_input_label'  => 'EAN',
						'plugin_cf'         => '_wpsso_product_gtin13',
					),
					'options' => array(
						'plugin_attr' => '',
					),
				),
				'product_gtin12' => array(
					'label'      => _x( 'Product GTIN-12 (UPC)', 'option label', 'wpsso-wc-metadata' ),
					'desc'       => __( '%1$s refers to a product GTIN-12 code (12-digit GS1 identification key composed of a UPC company prefix, item reference, and check digit).', 'wpsso-wc-metadata' ),
					'type'       => 'text',
					'searchable' => true,
					'actions'    => array(
						'woocommerce_product_options_sku'       => true,
						'woocommerce_variation_options_pricing' => true,
					),
					'filters' => array(
						'woocommerce_display_product_attributes' => true,
					),
					'defaults' => array(
						'wcmd_enable'       => 1,
						'wcmd_info_label'   => 'UPC',
						'wcmd_input_holder' => '12-digit bar code',	// Capitalize the first word.
						'wcmd_input_label'  => 'UPC',
						'plugin_cf'         => '_wpsso_product_gtin12',
					),
					'options' => array(
						'plugin_attr' => '',
					),
				),
				'product_gtin8' => array(
					'label'      => _x( 'Product GTIN-8', 'option label', 'wpsso-wc-metadata' ),
					'desc'       => __( '%1$s refers to a product GTIN-8 code (aka EAN/UCC-8 or 8-digit EAN).', 'wpsso-wc-metadata' ),
					'searchable' => true,
					'type'       => 'text',
					'actions'    => array(
						'woocommerce_product_options_sku'       => true,
						'woocommerce_variation_options_pricing' => true,
					),
					'filters' => array(
						'woocommerce_display_product_attributes' => true,
					),
					'defaults' => array(
						'wcmd_enable'       => 0,
						'wcmd_info_label'   => 'GTIN-8',
						'wcmd_input_holder' => '8-digit bar code',	// Capitalize the first word.
						'wcmd_input_label'  => 'GTIN-8',
						'plugin_cf'         => '_wpsso_product_gtin8',
					),
					'options' => array(
						'plugin_attr' => '',
					),
				),
				'product_gtin' => array(
					'label'      => _x( 'Product GTIN', 'option label', 'wpsso-wc-metadata' ),
					'desc'       => __( '%1$s refers to a product GTIN code (GTIN-8, GTIN-12/UPC, GTIN-13/EAN, or GTIN-14).', 'wpsso-wc-metadata' ),
					'searchable' => true,
					'type'       => 'text',
					'actions'    => array(
						'woocommerce_product_options_sku'       => true,
						'woocommerce_variation_options_pricing' => true,
					),
					'filters' => array(
						'woocommerce_display_product_attributes' => true,
					),
					'defaults' => array(
						'wcmd_enable'       => 0,
						'wcmd_info_label'   => 'GTIN',
						'wcmd_input_holder' => 'Bar code',	// Capitalize the first word.
						'wcmd_input_label'  => 'GTIN',
						'plugin_cf'         => '_wpsso_product_gtin',
					),
					'options' => array(
						'plugin_attr' => '',
					),
				),
				'product_length_value' => array(
					'label'        => _x( 'Product Net Length', 'option label', 'wpsso-wc-metadata' ),
					'desc'         => __( '%1$s refers to a product net length (in %2$s).', 'wpsso-wc-metadata' ),
					'type'         => 'text',
					'data_type'    => 'decimal',
					'unit_text'    => $dimension_unit_wc_text,
					'unit_label'   => $dimension_unit_wc_label,
					'unit_wc_1x'   => $dimension_unit_wc_1x === 1 ? null : $dimension_unit_wc_1x,
					'actions'      => array(
						'woocommerce_product_options_sku'       => true,
						'woocommerce_variation_options_pricing' => true,
					),
					'filters' => array(
						'woocommerce_display_product_attributes' => true,
					),
					'defaults' => array(
						'wcmd_enable'       => 1,
						'wcmd_info_label'   => 'Net Length',
						'wcmd_input_holder' => 'Net length in %s',	// Capitalize the first word.
						'wcmd_input_label'  => 'Net Length (%s)',
						'plugin_cf'         => '_wpsso_product_length_value',
					),
					'options' => array(
						'plugin_attr' => '',
					),
				),
				'product_width_value' => array(
					'label'        => _x( 'Product Net Width', 'option label', 'wpsso-wc-metadata' ),
					'desc'         => __( '%1$s refers to a product net width (in %2$s).', 'wpsso-wc-metadata' ),
					'type'         => 'text',
					'data_type'    => 'decimal',
					'unit_text'    => $dimension_unit_wc_text,
					'unit_label'   => $dimension_unit_wc_label,
					'unit_wc_1x'   => $dimension_unit_wc_1x === 1 ? null : $dimension_unit_wc_1x,
					'actions'      => array(
						'woocommerce_product_options_sku'       => true,
						'woocommerce_variation_options_pricing' => true,
					),
					'filters' => array(
						'woocommerce_display_product_attributes' => true,
					),
					'defaults' => array(
						'wcmd_enable'       => 1,
						'wcmd_info_label'   => 'Net Width',
						'wcmd_input_holder' => 'Net width in %s',	// Capitalize the first word.
						'wcmd_input_label'  => 'Net Width (%s)',
						'plugin_cf'         => '_wpsso_product_width_value',
					),
					'options' => array(
						'plugin_attr' => '',
					),
				),
				'product_height_value' => array(
					'label'        => _x( 'Product Net Height', 'option label', 'wpsso-wc-metadata' ),
					'desc'         => __( '%1$s refers to a product net height (in %2$s).', 'wpsso-wc-metadata' ),
					'type'         => 'text',
					'data_type'    => 'decimal',
					'unit_text'    => $dimension_unit_wc_text,
					'unit_label'   => $dimension_unit_wc_label,
					'unit_wc_1x'   => $dimension_unit_wc_1x === 1 ? null : $dimension_unit_wc_1x,
					'actions'      => array(
						'woocommerce_product_options_sku'       => true,
						'woocommerce_variation_options_pricing' => true,
					),
					'filters' => array(
						'woocommerce_display_product_attributes' => true,
					),
					'defaults' => array(
						'wcmd_enable'       => 1,
						'wcmd_info_label'   => 'Net Height',
						'wcmd_input_holder' => 'Net height in %s',	// Capitalize the first word.
						'wcmd_input_label'  => 'Net Height (%s)',
						'plugin_cf'         => '_wpsso_product_height_value',
					),
					'options' => array(
						'plugin_attr' => '',
					),
				),
				'product_depth_value' => array(
					'label'        => _x( 'Product Net Depth', 'option label', 'wpsso-wc-metadata' ),
					'desc'         => __( '%1$s refers to a product net depth (in %2$s).', 'wpsso-wc-metadata' ),
					'type'         => 'text',
					'data_type'    => 'decimal',
					'unit_text'    => $dimension_unit_wc_text,
					'unit_label'   => $dimension_unit_wc_label,
					'unit_wc_1x'   => $dimension_unit_wc_1x === 1 ? null : $dimension_unit_wc_1x,
					'actions'      => array(
						'woocommerce_product_options_sku'       => true,
						'woocommerce_variation_options_pricing' => true,
					),
					'filters' => array(
						'woocommerce_display_product_attributes' => true,
					),
					'defaults' => array(
						'wcmd_enable'       => 0,
						'wcmd_info_label'   => 'Net Depth',
						'wcmd_input_holder' => 'Net depth in %s',	// Capitalize the first word.
						'wcmd_input_label'  => 'Net Depth (%s)',
						'plugin_cf'         => '_wpsso_product_depth_value',
					),
					'options' => array(
						'plugin_attr' => '',
					),
				),
				'product_fluid_volume_value' => array(
					'label'        => _x( 'Product Net Fluid Volume', 'option label', 'wpsso-wc-metadata' ),
					'desc'         => __( '%1$s refers to a product net fluid volume (in %2$s).', 'wpsso-wc-metadata' ),
					'type'         => 'text',
					'data_type'    => 'decimal',
					'unit_text'    => $fl_vol_unit_wc_text,
					'unit_label'   => $fl_vol_unit_wc_label,
					'unit_wc_1x'   => $fl_vol_unit_wc_1x === 1 ? null : $fl_vol_unit_wc_1x,
					'actions'      => array(
						'woocommerce_product_options_sku'       => true,
						'woocommerce_variation_options_pricing' => true,
					),
					'filters' => array(
						'woocommerce_display_product_attributes' => true,
					),
					'defaults' => array(
						'wcmd_enable'       => 0,
						'wcmd_info_label'   => 'Net Fluid Volume',
						'wcmd_input_holder' => 'Net fluid volume in %s',	// Capitalize the first word.
						'wcmd_input_label'  => 'Net Fl. Volume (%s)',
						'plugin_cf'         => '_wpsso_product_fluid_volume_value',
					),
					'options' => array(
						'plugin_attr' => '',
					),
				),
				'product_weight_value' => array(
					'label'        => _x( 'Product Net Weight', 'option label', 'wpsso-wc-metadata' ),
					'desc'         => __( '%1$s refers to a product net weight (in %2$s).', 'wpsso-wc-metadata' ),
					'type'         => 'text',
					'data_type'    => 'decimal',
					'unit_text'    => $weight_unit_wc_text,
					'unit_label'   => $weight_unit_wc_label,
					'unit_wc_1x'   => $weight_unit_wc_1x === 1 ? null : $weight_unit_wc_1x,
					'actions'      => array(
						'woocommerce_product_options_sku'       => true,
						'woocommerce_variation_options_pricing' => true,
					),
					'filters' => array(
						'woocommerce_display_product_attributes' => true,
					),
					'defaults' => array(
						'wcmd_enable'       => 1,
						'wcmd_info_label'   => 'Net Weight',
						'wcmd_input_holder' => 'Net weight in %s',	// Capitalize the first word.
						'wcmd_input_label'  => 'Net Weight (%s)',
						'plugin_cf'         => '_wpsso_product_weight_value',
					),
					'options' => array(
						'plugin_attr' => '',
					),
				),
			);

			$local_cache = apply_filters( 'wpsso_wc_metadata_config', $local_cache );

			return $local_cache;
		}

		public static function get_version( $add_slug = false ) {

			$info =& self::$cf[ 'plugin' ][ 'wpssowcmd' ];

			return $add_slug ? $info[ 'slug' ] . '-' . $info[ 'version' ] : $info[ 'version' ];
		}

		public static function set_constants( $plugin_file ) {

			if ( defined( 'WPSSOWCMD_VERSION' ) ) {	// Define constants only once.

				return;
			}

			$info =& self::$cf[ 'plugin' ][ 'wpssowcmd' ];

			/**
			 * Define fixed constants.
			 */
			define( 'WPSSOWCMD_FILEPATH', $plugin_file );
			define( 'WPSSOWCMD_PLUGINBASE', $info[ 'base' ] );	// Example: wpsso-wc-metadata/wpsso-wc-metadata.php.
			define( 'WPSSOWCMD_PLUGINDIR', trailingslashit( realpath( dirname( $plugin_file ) ) ) );
			define( 'WPSSOWCMD_PLUGINSLUG', $info[ 'slug' ] );	// Example: wpsso-wc-metadata.
			define( 'WPSSOWCMD_URLPATH', trailingslashit( plugins_url( '', $plugin_file ) ) );
			define( 'WPSSOWCMD_VERSION', $info[ 'version' ] );
		}

		public static function require_libs( $plugin_file ) {

			require_once WPSSOWCMD_PLUGINDIR . 'lib/filters.php';
			require_once WPSSOWCMD_PLUGINDIR . 'lib/register.php';
			require_once WPSSOWCMD_PLUGINDIR . 'lib/search.php';
			require_once WPSSOWCMD_PLUGINDIR . 'lib/woocommerce.php';

			add_filter( 'wpssowcmd_load_lib', array( __CLASS__, 'load_lib' ), 10, 3 );
		}

		public static function load_lib( $success = false, $filespec = '', $classname = '' ) {

			if ( false !== $success ) {

				return $success;
			}

			if ( ! empty( $classname ) ) {

				if ( class_exists( $classname ) ) {

					return $classname;
				}
			}

			if ( ! empty( $filespec ) ) {

				$file_path = WPSSOWCMD_PLUGINDIR . 'lib/' . $filespec . '.php';

				if ( file_exists( $file_path ) ) {

					require_once $file_path;

					if ( empty( $classname ) ) {

						return SucomUtil::sanitize_classname( 'wpssowcmd' . $filespec, $allow_underscore = false );
					}

					return $classname;
				}
			}

			return $success;
		}
	}
}
