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
 * Description: GTIN, GTIN-8, GTIN-12 (UPC), GTIN-13 (EAN), GTIN-14, ISBN, MPN, Depth, and Volume for WooCommerce Products and Variations.
 * Requires PHP: 5.6
 * Requires At Least: 4.2
 * Tested Up To: 5.4.1
 * WC Tested Up To: 4.1.0
 * Version: 1.1.0
 *
 * Version Numbering: {major}.{minor}.{bugfix}[-{stage}.{level}]
 *
 *      {major}         Major structural code changes / re-writes or incompatible API changes.
 *      {minor}         New functionality was added or improved in a backwards-compatible manner.
 *      {bugfix}        Backwards-compatible bug fixes or small improvements.
 *      {stage}.{level} Pre-production release: dev < a (alpha) < b (beta) < rc (release candidate).
 *
 * Copyright 2017-2020 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoWcMd' ) ) {

	class WpssoWcMd {

		/**
		 * Wpsso plugin class object variable.
		 */
		public $p;		// Wpsso

		/**
		 * Library class object variables.
		 */
		public $filters;	// WpssoWcMdFilters
		public $reg;		// WpssoWcMdRegister
		public $wc;		// WpssoWcMdWooCommerce

		/**
		 * Reference Variables (config, options, modules, etc.).
		 */
		private $have_wpsso_min_version = true;	// Have WPSSO Core minimum version.

		private static $ext      = 'wpssowcmd';
		private static $p_ext    = 'wcmd';
		private static $info     = array();
		private static $instance = null;

		public function __construct() {

			require_once dirname( __FILE__ ) . '/lib/config.php';

			WpssoWcMdConfig::set_constants( __FILE__ );

			WpssoWcMdConfig::require_libs( __FILE__ );	// Includes the register.php class library.

			$this->reg = new WpssoWcMdRegister();		// Activate, deactivate, uninstall hooks.

			/**
			 * Check for required plugins and show notices.
			 */
			add_action( 'all_admin_notices', array( __CLASS__, 'show_required_notices' ) );

			/**
			 * Add WPSSO filter hooks.
			 */
			add_filter( 'wpsso_get_config', array( $this, 'wpsso_get_config' ), 10, 2 );	// Checks core version and merges config array.
			add_filter( 'wpsso_get_avail', array( $this, 'wpsso_get_avail' ), 10, 1 );

			/**
			 * Add WPSSO action hooks.
			 */
			add_action( 'wpsso_init_textdomain', array( __CLASS__, 'wpsso_init_textdomain' ) );
			add_action( 'wpsso_init_objects', array( $this, 'wpsso_init_objects' ), 10 );
			add_action( 'wpsso_init_check_options', array( $this, 'wpsso_init_check_options' ), 10 );
			add_action( 'wpsso_init_plugin', array( $this, 'wpsso_init_plugin' ), 10 );
		}

		public static function &get_instance() {

			if ( null === self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Check for required plugins and show notices.
		 */
		public static function show_required_notices() {

			$missing_requirements = self::get_missing_requirements();	// Returns false or an array of missing requirements.

			if ( ! $missing_requirements ) {
				return;	// Stop here.
			}

			self::wpsso_init_textdomain();	// If not already loaded, load the textdomain now.

			$info = WpssoWcMdConfig::$cf[ 'plugin' ][ self::$ext ];

			$notice_msg = __( 'The %1$s add-on requires the %2$s plugin &mdash; please install and activate the missing plugin.',
				'wpsso-wc-metadata' );

			foreach ( $missing_requirements as $key => $req_info ) {

				echo '<div class="notice notice-error error"><p>';

				echo sprintf( $notice_msg, $info[ 'name' ], $req_info[ 'name' ] );

				echo '</p></div>';
			}
		}

		/**
		 * Returns false or an array of the missing requirements (ie. 'wpsso', 'woocommerce', etc.).
		 */
		public static function get_missing_requirements() {

			static $local_cache = null;

			if ( null !== $local_cache ) {
				return $local_cache;
			}

			$local_cache = array();

			$info = WpssoWcMdConfig::$cf[ 'plugin' ][ self::$ext ];

			foreach ( $info[ 'req' ] as $key => $req_info ) {

				if ( isset( $req_info[ 'class' ] ) ) {

					if ( class_exists( $req_info[ 'class' ] ) ) {
						continue;	// Requirement satisfied.
					}

				} else {
					continue;	// Nothing to check.
				}

				$local_cache[ $key ] = $req_info;
			}

			if ( empty( $local_cache ) ) {
				$local_cache = false;
			}

			return $local_cache;
		}

		/**
		 * The 'wpsso_init_textdomain' action is run after the $check, $avail, and $debug properties are defined.
		 */
		public static function wpsso_init_textdomain( $debug_enabled = false ) {

			static $loaded = null;

			if ( null !== $loaded ) {
				return;
			}

			$loaded = true;

			load_plugin_textdomain( 'wpsso-wc-metadata', false, 'wpsso-wc-metadata/languages/' );
		}

		/**
		 * Checks the core plugin version and merges the extension / add-on config array.
		 */
		public function wpsso_get_config( $cf, $plugin_version = 0 ) {

			$info = WpssoWcMdConfig::$cf[ 'plugin' ][ self::$ext ];

			$req_info = $info[ 'req' ][ 'wpsso' ];

			if ( version_compare( $plugin_version, $req_info[ 'min_version' ], '<' ) ) {

				$this->have_wpsso_min_version = false;

				return $cf;
			}

			return SucomUtil::array_merge_recursive_distinct( $cf, WpssoWcMdConfig::$cf );
		}

		/**
		 * The 'wpsso_get_avail' filter is run after the $check property is defined.
		 */
		public function wpsso_get_avail( $avail ) {

			if ( ! $this->have_wpsso_min_version ) {

				$avail[ 'p_ext' ][ self::$p_ext ] = false;	// Signal that this extension / add-on is not available.

				return $avail;
			}

			$avail[ 'p_ext' ][ self::$p_ext ] = true;		// Signal that this extension / add-on is available.

			return $avail;
		}

		public function wpsso_init_objects() {

			$this->p =& Wpsso::get_instance();

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			if ( ! $this->have_wpsso_min_version ) {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'exiting early: have_wpsso_min_version is false' );
				}

				return;	// Stop here.
			}

			if ( self::get_missing_requirements() ) {	// Returns false or an array of missing requirements.
				return;	// Stop here.
			}

			$this->filters = new WpssoWcMdFilters( $this->p );
			$this->wc      = new WpssoWcMdWooCommerce( $this->p );
		}

		public function wpsso_init_check_options() {

			if ( ! $this->have_wpsso_min_version ) {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'exiting early: have_wpsso_min_version is false' );
				}

				return;	// Stop here.
			}

			if ( self::get_missing_requirements() ) {	// Returns false or an array of missing requirements.
				return;	// Stop here.
			}

			$md_config = WpssoWcMdConfig::get_md_config();

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
					 * The WpssoWcMdFilters->filter_option_type() filter also returns 'not_blank' for enabled
					 * WooCommerce metadata custom fields in order to show an error notice.
					 */
					$opt_key = 'plugin_cf_' . $md_suffix;

					if ( empty( $this->p->options[ $opt_key ] ) ) {

						$this->p->options[ $opt_key ] = $cfg[ 'defaults' ][ 'plugin_cf' ];
					}
				}
			}
		}

		/**
		 * All WPSSO objects are instantiated and configured.
		 */
		public function wpsso_init_plugin() {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			if ( ! $this->have_wpsso_min_version ) {

				$this->min_version_notice();	// Show minimum version notice.

				return;	// Stop here.
			}
		}

		private function min_version_notice() {

			$info = WpssoWcMdConfig::$cf[ 'plugin' ][ self::$ext ];

			$req_info = $info[ 'req' ][ 'wpsso' ];

			if ( is_admin() ) {

				$notice_msg = sprintf( __( 'The %1$s version %2$s add-on requires %3$s version %4$s or newer (version %5$s is currently installed).',
					'wpsso-wc-metadata' ), $info[ 'name' ], $info[ 'version' ], $req_info[ 'name' ], $req_info[ 'min_version' ],
						$this->p->cf[ 'plugin' ][ 'wpsso' ][ 'version' ] );

				$this->p->notice->err( $notice_msg );

				if ( method_exists( $this->p->admin, 'get_check_for_updates_link' ) ) {
	
					$update_msg = $this->p->admin->get_check_for_updates_link();

					if ( ! empty( $update_msg ) ) {
						$this->p->notice->inf( $update_msg );
					}
				}

			} else {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( sprintf( '%1$s version %2$s requires %3$s version %4$s or newer',
						$info[ 'name' ], $info[ 'version' ], $req_info[ 'name' ], $req_info[ 'min_version' ] ) );
				}
			}
		}
	}

	WpssoWcMd::get_instance();	// Self-instantiate.
}
