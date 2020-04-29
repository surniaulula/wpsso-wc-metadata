<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2020 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoWcMdConfig' ) ) {

	class WpssoWcMdConfig {

		public static $cf = array(
			'plugin' => array(
				'wpssowcmd' => array(			// Plugin acronym.
					'version'     => '1.0.0-dev.4',	// Plugin version.
					'opt_version' => '6',		// Increment when changing default option values.
					'short'       => 'WPSSO WCMD',	// Short plugin name.
					'name'        => 'WPSSO Metadata for WooCommerce',
					'desc'        => 'GTIN, GTIN-8, GTIN-12 (UPC), GTIN-13 (EAN), GTIN-14, ISBN, MPN, Depth, and Volume for WooCommerce Products and Variations.',
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
							'class'       => 'Wpsso',
							'name'        => 'WPSSO Core',
							'min_version' => '7.3.0-dev.4',
						),
					),

					/**
					 * URLs or relative paths to plugin banners and icons.
					 */
					'assets' => array(
						'icons' => array(
							'low'  => 'images/icon-128x128.png',
							'high' => 'images/icon-256x256.png',
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

			static $locale_cache = null;

			if ( null !== $locale_cache ) {
				return $locale_cache;
			}

			$dim_unit       = WpssoSchema::get_data_unit_text( 'depth' );
			$dim_unit_wc    = get_option( 'woocommerce_dimension_unit' );
			$dim_unit_wc_1x = wc_get_dimension( 1, $dim_unit, $dim_unit_wc );

			$vol_unit = WpssoSchema::get_data_unit_text( 'volume' );

			/**
			 * Metadata options will be down in the order listed here.
			 */
			$local_cache = array(
				'product_mfr_part_no' => array(
					'label'   => _x( 'Product MPN', 'option label', 'wpsso-wc-metadata' ),
					'type'    => 'text',
					'actions' => array(
						'woocommerce_product_options_sku'       => true,
						'woocommerce_variation_options_pricing' => true,
					),
					'defaults' => array(
						'wcmd_enable' => 1,
						'wcmd_holder' => 'Part number',
						'wcmd_label'  => 'MPN',
						'plugin_cf'   => '_wpsso_product_mfr_part_no',
					),
					'options' => array(
						'plugin_attr' => '',
					),
				),
				'product_isbn' => array(
					'label'   => _x( 'Product ISBN', 'option label', 'wpsso-wc-metadata' ),
					'type'    => 'text',
					'actions' => array(
						'woocommerce_product_options_sku'       => true,
						'woocommerce_variation_options_pricing' => true,
					),
					'defaults' => array(
						'wcmd_enable' => 0,
						'wcmd_holder' => 'Book number',
						'wcmd_label'  => 'ISBN',
						'plugin_cf'   => '_wpsso_product_isbn',
					),
					'options' => array(
						'plugin_attr' => '',
					),
				),
				'product_gtin14' => array(
					'label'   => _x( 'Product GTIN-14', 'option label', 'wpsso-wc-metadata' ),
					'type'    => 'text',
					'actions' => array(
						'woocommerce_product_options_sku'       => true,
						'woocommerce_variation_options_pricing' => true,
					),
					'defaults' => array(
						'wcmd_enable' => 0,
						'wcmd_holder' => '14-digit bar code',
						'wcmd_label'  => 'GTIN-14',
						'plugin_cf'   => '_wpsso_product_gtin14',
					),
					'options' => array(
						'plugin_attr' => '',
					),
				),
				'product_gtin13' => array(
					'label'   => _x( 'Product GTIN-13 (EAN)', 'option label', 'wpsso-wc-metadata' ),
					'type'    => 'text',
					'actions' => array(
						'woocommerce_product_options_sku'       => true,
						'woocommerce_variation_options_pricing' => true,
					),
					'defaults' => array(
						'wcmd_enable' => 0,
						'wcmd_holder' => '13-digit bar code',
						'wcmd_label'  => 'EAN',
						'plugin_cf'   => '_wpsso_product_gtin13',
					),
					'options' => array(
						'plugin_attr' => '',
					),
				),
				'product_gtin12' => array(
					'label'   => _x( 'Product GTIN-12 (UPC)', 'option label', 'wpsso-wc-metadata' ),
					'type'    => 'text',
					'actions' => array(
						'woocommerce_product_options_sku'       => true,
						'woocommerce_variation_options_pricing' => true,
					),
					'defaults' => array(
						'wcmd_enable' => 0,
						'wcmd_holder' => '12-digit bar code',
						'wcmd_label'  => 'UPC',
						'plugin_cf'   => '_wpsso_product_gtin12',
					),
					'options' => array(
						'plugin_attr' => '',
					),
				),
				'product_gtin8' => array(
					'label'   => _x( 'Product GTIN-8', 'option label', 'wpsso-wc-metadata' ),
					'type'    => 'text',
					'actions' => array(
						'woocommerce_product_options_sku'       => true,
						'woocommerce_variation_options_pricing' => true,
					),
					'defaults' => array(
						'wcmd_enable' => 0,
						'wcmd_holder' => '8-digit bar code',
						'wcmd_label'  => 'GTIN-8',
						'plugin_cf'   => '_wpsso_product_gtin8',
					),
					'options' => array(
						'plugin_attr' => '',
					),
				),
				'product_gtin' => array(
					'label'   => _x( 'Product GTIN', 'option label', 'wpsso-wc-metadata' ),
					'type'    => 'text',
					'actions' => array(
						'woocommerce_product_options_sku'       => true,
						'woocommerce_variation_options_pricing' => true,
					),
					'defaults' => array(
						'wcmd_enable' => 1,
						'wcmd_holder' => 'Bar code',
						'wcmd_label'  => 'GTIN',
						'plugin_cf'   => '_wpsso_product_gtin',
					),
					'options' => array(
						'plugin_attr' => '',
					),
				),
				'product_depth_value' => array(
					'label'       => _x( 'Product Depth', 'option label', 'wpsso-wc-metadata' ),
					'type'        => 'text',
					'data_type'   => 'decimal',
					'unit_wc_1x'  => $dim_unit_wc_1x,
					'printf_args' => array( $dim_unit_wc ),
					'actions'     => array(
						'woocommerce_product_options_dimensions'   => true,
						'woocommerce_variation_options_dimensions' => true,
					),
					'defaults' => array(
						'wcmd_enable' => 0,
						'wcmd_holder' => 'Depth in %s',
						'wcmd_label'  => 'Depth (%s)',
						'plugin_cf'   => '_wpsso_product_depth_value',
					),
					'options' => array(
						'plugin_attr' => '',
					),
				),
				'product_volume_value' => array(
					'label'       => _x( 'Product Volume', 'option label', 'wpsso-wc-metadata' ),
					'type'        => 'text',
					'data_type'   => 'decimal',
					'printf_args' => array( $vol_unit ),
					'actions'     => array(
						'woocommerce_product_options_dimensions'   => true,
						'woocommerce_variation_options_dimensions' => true,
					),
					'defaults' => array(
						'wcmd_enable' => 0,
						'wcmd_holder' => 'Volume in %s',
						'wcmd_label'  => 'Volume (%s)',
						'plugin_cf'   => '_wpsso_product_volume_value',
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

		public static function set_constants( $plugin_file_path ) { 

			if ( defined( 'WPSSOWCMD_VERSION' ) ) {	// Define constants only once.
				return;
			}

			$info =& self::$cf[ 'plugin' ][ 'wpssowcmd' ];

			/**
			 * Define fixed constants.
			 */
			define( 'WPSSOWCMD_FILEPATH', $plugin_file_path );						
			define( 'WPSSOWCMD_PLUGINBASE', $info[ 'base' ] );	// Example: wpsso-wc-metadata/wpsso-wc-metadata.php.
			define( 'WPSSOWCMD_PLUGINDIR', trailingslashit( realpath( dirname( $plugin_file_path ) ) ) );
			define( 'WPSSOWCMD_PLUGINSLUG', $info[ 'slug' ] );	// Example: wpsso-wc-metadata.
			define( 'WPSSOWCMD_URLPATH', trailingslashit( plugins_url( '', $plugin_file_path ) ) );
			define( 'WPSSOWCMD_VERSION', $info[ 'version' ] );						
		}

		public static function require_libs( $plugin_file_path ) {

			require_once WPSSOWCMD_PLUGINDIR . 'lib/filters.php';
			require_once WPSSOWCMD_PLUGINDIR . 'lib/register.php';
			require_once WPSSOWCMD_PLUGINDIR . 'lib/woocommerce.php';

			add_filter( 'wpssowcmd_load_lib', array( 'WpssoWcMdConfig', 'load_lib' ), 10, 3 );
		}

		public static function load_lib( $ret = false, $filespec = '', $classname = '' ) {

			if ( false === $ret && ! empty( $filespec ) ) {

				$file_path = WPSSOWCMD_PLUGINDIR . 'lib/' . $filespec . '.php';

				if ( file_exists( $file_path ) ) {

					require_once $file_path;

					if ( empty( $classname ) ) {
						return SucomUtil::sanitize_classname( 'wpssowcmd' . $filespec, $allow_underscore = false );
					} else {
						return $classname;
					}
				}
			}

			return $ret;
		}
	}
}
