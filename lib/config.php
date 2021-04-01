<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2020-2021 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoWcmdConfig' ) ) {

	class WpssoWcmdConfig {

		public static $cf = array(
			'plugin' => array(
				'wpssowcmd' => array(			// Plugin acronym.
					'version'     => '1.8.1',	// Plugin version.
					'opt_version' => '10',		// Increment when changing default option values.
					'short'       => 'WPSSO WCMD',	// Short plugin name.
					'name'        => 'WPSSO Product Metadata for WooCommerce',
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
						'woocommerce' => array(
							'name'          => 'WooCommerce',
							'home'          => 'https://wordpress.org/plugins/woocommerce/',
							'plugin_class'  => 'WooCommerce',
							'version_const' => 'WC_VERSION',
							'min_version'   => '3.8.0',	// WooCommerce v3.8.0.
						),
						'wpsso' => array(
							'name'          => 'WPSSO Core',
							'home'          => 'https://wordpress.org/plugins/wpsso/',
							'plugin_class'  => 'Wpsso',
							'version_const' => 'WPSSO_VERSION',
							'min_version'   => '8.25.2',
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
							'wcmd-general' => 'WooCommerce Metadata',
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

			if ( ! class_exists( 'WpssoUtilWooCommerce' ) ) {	// Just in case.

				return $local_cache = array();			// Must return an array.
			}

			/**
			 * WpssoSchema::get_data_unit_text() returns a https://schema.org/unitText value (for example, 'cm', 'ml',
			 * 'kg', etc.).
			 */
			$dim_unit_text     = WpssoSchema::get_data_unit_text( 'depth' );
			$dim_unit_wc_text  = get_option( 'woocommerce_dimension_unit', $dim_unit_text );
			$dim_unit_wc_label = WpssoUtilWooCommerce::get_dimension_label( $dim_unit_wc_text );
			$dim_unit_wc_1x    = WpssoUtilWooCommerce::get_dimension( 1, $dim_unit_text, $dim_unit_wc_text );

			$fl_vol_unit_text     = WpssoSchema::get_data_unit_text( 'fluid_volume' );
			$fl_vol_unit_wc_text  = get_option( 'woocommerce_fluid_volume_unit', $fl_vol_unit_text );
			$fl_vol_unit_wc_label = WpssoUtilWooCommerce::get_fluid_volume_label( $fl_vol_unit_wc_text );
			$fl_vol_unit_wc_1x    = WpssoUtilWooCommerce::get_fluid_volume( 1, $fl_vol_unit_text, $fl_vol_unit_wc_text );

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
						'wcmd_input_holder' => 'Part number',
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
						'wcmd_input_holder' => 'Book number',
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
						'wcmd_input_holder' => '14-digit bar code',
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
						'wcmd_enable'       => 0,
						'wcmd_info_label'   => 'EAN',
						'wcmd_input_holder' => '13-digit bar code',
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
						'wcmd_enable'       => 0,
						'wcmd_info_label'   => 'UPC',
						'wcmd_input_holder' => '12-digit bar code',
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
						'wcmd_input_holder' => '8-digit bar code',
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
						'wcmd_enable'       => 1,
						'wcmd_info_label'   => 'GTIN',
						'wcmd_input_holder' => 'Bar code',
						'wcmd_input_label'  => 'GTIN',
						'plugin_cf'         => '_wpsso_product_gtin',
					),
					'options' => array(
						'plugin_attr' => '',
					),
				),
				'product_depth_value' => array(
					'label'        => _x( 'Product Depth', 'option label', 'wpsso-wc-metadata' ),
					'desc'         => __( '%1$s refers to a product depth (in %2$s).', 'wpsso-wc-metadata' ),
					'type'         => 'text',
					'data_type'    => 'decimal',
					'unit_text'    => $dim_unit_wc_text,
					'unit_label'   => $dim_unit_wc_label,
					'unit_wc_1x'   => $dim_unit_wc_1x === 1 ? null : $dim_unit_wc_1x,
					'insert_after' => 'dimensions',	// Used by 'woocommerce_display_product_attributes' filter.
					'actions'      => array(
						'woocommerce_product_options_dimensions'   => true,
						'woocommerce_variation_options_dimensions' => true,
					),
					'filters' => array(
						'woocommerce_display_product_attributes' => true,
					),
					'defaults' => array(
						'wcmd_enable'       => 0,
						'wcmd_info_label'   => 'Depth',
						'wcmd_input_holder' => 'Depth in %s',
						'wcmd_input_label'  => 'Depth (%s)',
						'plugin_cf'         => '_wpsso_product_depth_value',
					),
					'options' => array(
						'plugin_attr' => '',
					),
				),
				'product_fluid_volume_value' => array(
					'label'        => _x( 'Product Fluid Volume', 'option label', 'wpsso-wc-metadata' ),
					'desc'         => __( '%1$s refers to a product fluid volume (in %2$s).', 'wpsso-wc-metadata' ),
					'type'         => 'text',
					'data_type'    => 'decimal',
					'unit_text'    => $fl_vol_unit_wc_text,
					'unit_label'   => $fl_vol_unit_wc_label,
					'unit_wc_1x'   => $fl_vol_unit_wc_1x === 1 ? null : $fl_vol_unit_wc_1x,
					'insert_after' => 'weight',	// Used by 'woocommerce_display_product_attributes' filter.
					'actions'      => array(
						'woocommerce_product_options_dimensions'   => true,
						'woocommerce_variation_options_dimensions' => true,
					),
					'filters' => array(
						'woocommerce_display_product_attributes' => true,
					),
					'defaults' => array(
						'wcmd_enable'       => 0,
						'wcmd_info_label'   => 'Volume',
						'wcmd_input_holder' => 'Volume in %s',
						'wcmd_input_label'  => 'Volume (%s)',
						'plugin_cf'         => '_wpsso_product_fluid_volume_value',
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

			add_filter( 'wpssowcmd_load_lib', array( 'WpssoWcmdConfig', 'load_lib' ), 10, 3 );
		}

		public static function load_lib( $success = false, $filespec = '', $classname = '' ) {

			if ( false === $success && ! empty( $filespec ) ) {

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
