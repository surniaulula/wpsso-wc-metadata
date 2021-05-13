<?php
/**
 * Plugin Name: WPSSO Product Metadata for WooCommerce
 * Plugin Slug: wpsso-wc-metadata
 * Text Domain: wpsso-wc-metadata
 * Domain Path: /languages
 * Plugin URI: https://wpsso.com/extend/plugins/wpsso-wc-metadata/
 * Assets URI: https://jsmoriss.github.io/wpsso-wc-metadata/assets/
 * Author: JS Morisset
 * Author URI: https://wpsso.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl.txt
 * Description: GTIN, GTIN-8, GTIN-12 (UPC), GTIN-13 (EAN), GTIN-14, ISBN, MPN, depth, and volume for WooCommerce products and variations.
 * Requires PHP: 7.0
 * Requires At Least: 4.5
 * Tested Up To: 5.7.2
 * WC Tested Up To: 5.3.0
 * Version: 1.8.1
 *
 * Version Numbering: {major}.{minor}.{bugfix}[-{stage}.{level}]
 *
 *      {major}         Major structural code changes / re-writes or incompatible API changes.
 *      {minor}         New functionality was added or improved in a backwards-compatible manner.
 *      {bugfix}        Backwards-compatible bug fixes or small improvements.
 *      {stage}.{level} Pre-production release: dev < a (alpha) < b (beta) < rc (release candidate).
 *
 * Copyright 2020-2021 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoAddOn' ) ) {

	require_once dirname( __FILE__ ) . '/lib/abstracts/add-on.php';	// WpssoAddOn class.
}

if ( ! class_exists( 'WpssoWcmd' ) ) {

	class WpssoWcmd extends WpssoAddOn {

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

		/**
		 * $is_admin, $doing_ajax, and $doing_cron available since WPSSO Core v8.8.0.
		 */
		public function init_objects( $is_admin = false, $doing_ajax = false, $doing_cron = false ) {

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
				 * If the enable option is missing, assume the other wcmd options are missing as well.
				 */
				if ( ! isset( $this->p->options[ 'wcmd_enable_' . $md_suffix ] ) ) {

					foreach ( $cfg[ 'defaults' ] as $opt_prefix => $val ) {

						$opt_key = $opt_prefix . '_' . $md_suffix;

						$this->p->options[ $opt_key ] = $val;
					}

				/**
				 * For enabled options, hard-code some option values (example: 'plugin_attr_product_gtin' = '') and
				 * make sure the custom field name is not empty.
				 */
				} elseif ( ! empty( $this->p->options[ 'wcmd_enable_' . $md_suffix ] ) ) {

					foreach ( $cfg[ 'options' ] as $opt_prefix => $val ) {

						$opt_key = $opt_prefix . '_' . $md_suffix;

						$this->p->options[ $opt_key ]         = $val;
						$this->p->options[ $opt_key . ':is' ] = 'disabled';
					}

					/**
					 * The custom field name may be changed from the default, but should not be empty for
					 * enabled WooCommerce metadata.
					 *
					 * The WpssoWcmdFilters->filter_option_type() filter also returns 'not_blank' for enabled
					 * WooCommerce metadata custom fields in order to show an error notice.
					 */
					$opt_key = 'plugin_cf_' . $md_suffix;

					if ( empty( $this->p->options[ $opt_key ] ) ) {

						$this->p->options[ $opt_key ] = $cfg[ 'defaults' ][ 'plugin_cf' ];
					}
				}
			}
		}
	}

	WpssoWcmd::get_instance();	// Self-instantiate.
}
