<?php
/**
 * Plugin Name: WPSSO Metadata for WooCommerce
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
 * WC Tested Up To: 4.0.1
 * Version: 1.0.0-rc.1
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
		private $have_min_version = true;	// Have minimum wpsso version.

		private static $instance;

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

			if ( ! isset( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Check for required plugins and show notices.
		 */
		public static function show_required_notices() {

			$info = WpssoWcMdConfig::$cf[ 'plugin' ][ 'wpssowcmd' ];

			foreach ( $info[ 'req' ] as $ext => $req_info ) {

				if ( isset( $req_info[ 'class' ] ) ) {	// Just in case.

					if ( class_exists( $req_info[ 'class' ] ) ) {
						continue;	// Requirement satisfied.
					}

				} else continue;	// Nothing to check.

				$deactivate_url = html_entity_decode( wp_nonce_url( add_query_arg( array(
					'action'        => 'deactivate',
					'plugin'        => $info[ 'base' ],
					'plugin_status' => 'all',
					'paged'         => 1,
					's'             => '',
				), admin_url( 'plugins.php' ) ), 'deactivate-plugin_' . $info[ 'base' ] ) );

				self::wpsso_init_textdomain();	// If not already loaded, load the textdomain now.

				$notice_msg = __( 'The %1$s add-on requires the %2$s plugin &mdash; install and activate the plugin or <a href="%3$s">deactivate this add-on</a>.', 'wpsso-wc-metadata' );

				echo '<div class="notice notice-error error"><p>';
				echo sprintf( $notice_msg, $info[ 'name' ], $req_info[ 'name' ], $deactivate_url );
				echo '</p></div>';
			}
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

			$info = WpssoWcMdConfig::$cf[ 'plugin' ][ 'wpssowcmd' ];

			$req_info = $info[ 'req' ][ 'wpsso' ];

			if ( version_compare( $plugin_version, $req_info[ 'min_version' ], '<' ) ) {

				$this->have_min_version = false;

				return $cf;
			}

			return SucomUtil::array_merge_recursive_distinct( $cf, WpssoWcMdConfig::$cf );
		}

		/**
		 * The 'wpsso_get_avail' filter is run after the $check property is defined.
		 */
		public function wpsso_get_avail( $avail ) {

			if ( ! $this->have_min_version ) {

				$avail[ 'p_ext' ][ 'wcmd' ] = false;	// Signal that this extension / add-on is not available.

				return $avail;
			}

			$avail[ 'p_ext' ][ 'wcmd' ] = true;		// Signal that this extension / add-on is available.

			return $avail;
		}

		public function wpsso_init_objects() {

			$this->p =& Wpsso::get_instance();

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			if ( ! $this->have_min_version ) {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'exiting early: have_min_version is false' );
				}

				return;	// Stop here.
			}

			$this->filters = new WpssoWcMdFilters( $this->p );
			$this->wc      = new WpssoWcMdWooCommerce( $this->p );
		}

		public function wpsso_init_check_options() {

			if ( ! $this->have_min_version ) {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'exiting early: have_min_version is false' );
				}

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

			if ( ! $this->have_min_version ) {

				$this->min_version_notice();	// Show minimum version notice.

				return;	// Stop here.
			}
		}

		private function min_version_notice() {

			$info = WpssoWcMdConfig::$cf[ 'plugin' ][ 'wpssowcmd' ];

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
