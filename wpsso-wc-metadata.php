<?php
/**
 * Plugin Name: WPSSO Product Metadata for WooCommerce SEO
 * Plugin Slug: wpsso-wc-metadata
 * Text Domain: wpsso-wc-metadata
 * Domain Path: /languages
 * Plugin URI: https://wpsso.com/extend/plugins/wpsso-wc-metadata/
 * Assets URI: https://jsmoriss.github.io/wpsso-wc-metadata/assets/
 * Author: JS Morisset
 * Author URI: https://wpsso.com/
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Description: GTIN, GTIN-8, GTIN-12 (UPC), GTIN-13 (EAN), GTIN-14, ISBN, MPN, depth, and volume for WooCommerce products and variations.
 * Requires PHP: 7.2
 * Requires At Least: 5.2
 * Tested Up To: 5.8.3
 * WC Tested Up To: 6.1.0
 * Version: 1.13.0-dev.4
 *
 * Version Numbering: {major}.{minor}.{bugfix}[-{stage}.{level}]
 *
 *      {major}         Major structural code changes / re-writes or incompatible API changes.
 *      {minor}         New functionality was added or improved in a backwards-compatible manner.
 *      {bugfix}        Backwards-compatible bug fixes or small improvements.
 *      {stage}.{level} Pre-production release: dev < a (alpha) < b (beta) < rc (release candidate).
 *
 * Copyright 2020-2022 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoAbstractAddOn' ) ) {

	require_once dirname( __FILE__ ) . '/lib/abstract/add-on.php';
}

if ( ! class_exists( 'WpssoWcmd' ) ) {

	class WpssoWcmd extends WpssoAbstractAddOn {

		public $filters;	// WpssoWcmdFilters class object.
		public $search;		// WpssoWcmdSearch class object.
		public $wc;		// WpssoWcmdWooCommerce class object.

		protected $p;	// Wpsso class object.

		private static $instance = null;	// WpssoWcmd class object.

		public function __construct() {

			parent::__construct( __FILE__, __CLASS__ );
		}

		public static function &get_instance() {

			if ( null === self::$instance ) {

				self::$instance = new self;
			}

			return self::$instance;
		}

		public function init_textdomain() {

			load_plugin_textdomain( 'wpsso-wc-metadata', false, 'wpsso-wc-metadata/languages/' );
		}

		public function init_objects() {

			$this->p =& Wpsso::get_instance();

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			if ( $this->get_missing_requirements() ) {	// Returns false or an array of missing requirements.

				return;	// Stop here.
			}

			$this->filters = new WpssoWcmdFilters( $this->p, $this );
			$this->search  = new WpssoWcmdSearch( $this->p, $this );
			$this->wc      = new WpssoWcmdWooCommerce( $this->p, $this );
		}

		public function init_check_options() {

			if ( $this->get_missing_requirements() ) {	// Returns false or an array of missing requirements.

				return;	// Stop here.
			}

			$md_config = WpssoWcmdConfig::get_md_config();

			foreach ( $md_config as $md_suffix => $cfg ) {

				/**
				 * If the enable option is missing, then assume the other wcmd options are missing as well and
				 * reset the options to their default values.
				 */
				if ( ! isset( $this->p->options[ 'wcmd_enable_' . $md_suffix ] ) ) {	// Example: 'wcmd_enable_product_gtin'.

					/**
					 * Example defaults array:
					 *
					 * 	[defaults] => Array (
					 * 		[wcmd_enable]       => 1
					 * 		[wcmd_info_label]   => GTIN
					 * 		[wcmd_input_holder] => Bar code
					 * 		[wcmd_input_label]  => GTIN
					 * 		[plugin_cf]         => _wpsso_product_gtin
					 * 	)
					 */
					foreach ( $cfg[ 'defaults' ] as $opt_pre => $val ) {

						$opt_key = $opt_pre . '_' . $md_suffix;	// Example: 'wcmd_enable_product_gtin'.

						$this->p->options[ $opt_key ] = $val;
					}
				}

				/**
				 * Hard-code some option values, like 'plugin_attr_product_gtin' = '' for example.
				 *
				 * Example options array:
				 * 
				 * 	[options] => Array (
				 * 		[plugin_attr] =>
				 * 	)
				 */
				foreach ( $cfg[ 'options' ] as $opt_pre => $val ) {

					$opt_key = $opt_pre . '_' . $md_suffix;	// Example: 'plugin_attr_product_gtin'.

					$this->p->options[ $opt_key ]               = $val;
					$this->p->options[ $opt_key . ':disabled' ] = true;
				}

				/**
				 * The custom field name may be changed from the default value, but should not be empty.
				 *
				 * Just in case, the WpssoWcmdFilters->filter_option_type() filter also returns 'not_blank_quiet'
				 * for WooCommerce metadata custom fields.
				 */
				$opt_key = 'plugin_cf_' . $md_suffix;	// Example: 'plugin_cf_product_gtin'.

				if ( empty( $this->p->options[ $opt_key ] ) ) {

					$this->p->options[ $opt_key ] = $cfg[ 'defaults' ][ 'plugin_cf' ];
				}
			}
		}
	}

	WpssoWcmd::get_instance();	// Self-instantiate.
}
