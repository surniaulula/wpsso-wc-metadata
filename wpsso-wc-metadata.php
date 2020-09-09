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
 * Tested Up To: 5.5
 * WC Tested Up To: 4.5.0
 * Version: 1.5.0
 *
 * Version Numbering: {major}.{minor}.{bugfix}[-{stage}.{level}]
 *
 *      {major}         Major structural code changes / re-writes or incompatible API changes.
 *      {minor}         New functionality was added or improved in a backwards-compatible manner.
 *      {bugfix}        Backwards-compatible bug fixes or small improvements.
 *      {stage}.{level} Pre-production release: dev < a (alpha) < b (beta) < rc (release candidate).
 *
 * Copyright 2020 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoWcmd' ) ) {

	class WpssoWcmd {

		/**
		 * Wpsso plugin class object variable.
		 */
		public $p;		// Wpsso

		/**
		 * Library class object variables.
		 */
		public $filters;	// WpssoWcmdFilters
		public $reg;		// WpssoWcmdRegister
		public $search;		// WpssoWcmdSearch
		public $wc;		// WpssoWcmdWooCommerce

		/**
		 * Reference Variables (config, options, modules, etc.).
		 */
		private static $ext           = 'wpssowcmd';
		private static $p_ext         = 'wcmd';
		private static $missing_shown = false;
		private static $instance      = null;

		public function __construct() {

			require_once dirname( __FILE__ ) . '/lib/config.php';

			WpssoWcmdConfig::set_constants( __FILE__ );

			WpssoWcmdConfig::require_libs( __FILE__ );	// Includes the register.php class library.

			$this->reg = new WpssoWcmdRegister();		// Activate, deactivate, uninstall hooks.

			/**
			 * WPSSO filter hooks.
			 */
			add_filter( 'wpsso_get_config', array( __CLASS__, 'wpsso_get_config' ), 10, 2 );
			add_filter( 'wpsso_get_avail', array( __CLASS__, 'wpsso_get_avail' ), 10, 1 );

			/**
			 * WPSSO action hooks.
			 */
			add_action( 'wpsso_init_textdomain', array( __CLASS__, 'wpsso_init_textdomain' ) );
			add_action( 'wpsso_init_objects', array( $this, 'wpsso_init_objects' ), 10 );
			add_action( 'wpsso_init_check_options', array( $this, 'wpsso_init_check_options' ), 10 );
			add_action( 'wpsso_init_plugin', array( $this, 'wpsso_init_plugin' ), 10 );

			/**
			 * WordPress action hooks.
			 */
			add_action( 'all_admin_notices', array( __CLASS__, 'maybe_show_notices' ) );
		}

		public static function &get_instance() {

			if ( null === self::$instance ) {

				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Checks the core plugin version and merges the extension / add-on config array.
		 */
		public static function wpsso_get_config( $cf, $plugin_version = 0 ) {

			if ( self::get_missing_requirements() ) {	// Returns false or an array of missing requirements.

				return $cf;	// Stop here.
			}

			return SucomUtil::array_merge_recursive_distinct( $cf, WpssoWcmdConfig::$cf );
		}

		/**
		 * The 'wpsso_get_avail' filter is run after the $check property is defined.
		 */
		public static function wpsso_get_avail( $avail ) {

			if ( self::get_missing_requirements() ) {		// Returns false or an array of missing requirements.

				$avail[ 'p_ext' ][ self::$p_ext ] = false;	// Signal that this extension / add-on is not available.

				return $avail;
			}

			$avail[ 'p_ext' ][ self::$p_ext ] = true;		// Signal that this extension / add-on is available.

			return $avail;
		}

		/**
		 * The 'wpsso_init_textdomain' action is run after the $check, $avail, and $debug properties are defined.
		 */
		public static function wpsso_init_textdomain( $debug_enabled = false ) {

			static $loaded = null;

			if ( null !== $loaded && ! $debug_enabled ) {

				return;
			}

			$loaded = true;

			load_plugin_textdomain( 'wpsso-wc-metadata', false, 'wpsso-wc-metadata/languages/' );
		}

		public function wpsso_init_objects() {

			$this->p =& Wpsso::get_instance();

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			if ( self::get_missing_requirements() ) {	// Returns false or an array of missing requirements.

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'exiting early: have missing requirements' );
				}

				return;	// Stop here.
			}

			$this->filters = new WpssoWcmdFilters( $this->p );
			$this->search  = new WpssoWcmdSearch( $this->p );
			$this->wc      = new WpssoWcmdWooCommerce( $this->p );
		}

		public function wpsso_init_check_options() {

			if ( self::get_missing_requirements() ) {	// Returns false or an array of missing requirements.

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

		/**
		 * All WPSSO objects are instantiated and configured.
		 *
		 * The $is_admin and $doing_ajax arguments are provided since WPSSO Core v7.4.0.
		 */
		public function wpsso_init_plugin( $is_admin = null, $doing_ajax = null ) {

			$is_admin = null === $is_admin ? is_admin() : $is_admin;

			$doing_ajax = null === $doing_ajax ? SucomUtil::get_const( 'DOING_AJAX' ) : $doing_ajax;

			$missing_reqs = self::get_missing_requirements();	// Returns false or an array of missing requirements.

			self::$missing_shown = true;

			if ( ! $doing_ajax && $missing_reqs ) {

				$error_pre = sprintf( '%s error:', __METHOD__ );

				foreach ( $missing_reqs as $key => $req_info ) {

					if ( ! empty( $req_info[ 'notice' ] ) ) {

						if ( $is_admin ) {

							$this->p->notice->err( $req_info[ 'notice' ] );

							SucomUtil::safe_error_log( $error_pre . ' ' . $req_info[ 'notice' ], $strip_html = true );
						}
			
						if ( $this->p->debug->enabled ) {

							$this->p->debug->log( strtolower( $req_info[ 'notice' ] ) );
						}
					}
				}

				return;	// Stop here.
			}
		}

		public static function maybe_show_notices() {

			if ( self::$missing_shown ) {	// Nothing to do.

				return;	// Stop here.
			}

			$missing_reqs = self::get_missing_requirements();	// Returns false or an array of missing requirements.

			if ( ! $missing_reqs ) {

				return;	// Stop here.
			}

			foreach ( $missing_reqs as $key => $req_info ) {

				if ( ! empty( $req_info[ 'notice' ] ) ) {

					echo '<div class="notice notice-error error"><p>';
					echo $req_info[ 'notice' ];
					echo '</p></div>';
				}
			}
		}

		/**
		 * Returns false or an array of the missing requirements (ie. 'wpsso', 'woocommerce', etc.).
		 */
		private static function get_missing_requirements() {

			static $local_cache = null;

			if ( null !== $local_cache ) {

				return $local_cache;
			}

			$local_cache = array();

			$info = WpssoWcmdConfig::$cf[ 'plugin' ][ self::$ext ];

			foreach ( $info[ 'req' ] as $key => $req_info ) {

				if ( ! empty( $req_info[ 'home' ] ) ) {

					$req_name = '<a href="' . $req_info[ 'home' ] . '">' . $req_info[ 'name' ] . '</a>';

				} else {

					$req_name = $req_info[ 'name' ];
				}

				if ( ! empty( $req_info[ 'version_global' ] ) && ! empty( $GLOBALS[ $req_info[ 'version_global' ] ] ) ) {

					$req_info[ 'version' ] = $GLOBALS[ $req_info[ 'version_global' ] ];

				} elseif ( ! empty( $req_info[ 'version_const' ] ) && defined( $req_info[ 'version_const' ] ) ) {

					$req_info[ 'version' ] = constant( $req_info[ 'version_const' ] );

				} elseif ( ! empty( $req_info[ 'plugin_class' ] ) && ! class_exists( $req_info[ 'plugin_class' ] ) ) {

					self::wpsso_init_textdomain();	// If not already loaded, load the textdomain now.

					$notice_msg = __( 'The %1$s version %2$s add-on requires the %3$s plugin &mdash; please activate the missing plugin.', 'wpsso-wc-metadata' );

					$req_info[ 'notice' ] = sprintf( $notice_msg, $info[ 'name' ], $info[ 'version' ], $req_name );
				}

				if ( ! empty( $req_info[ 'version' ] ) ) {

					if ( ! empty( $req_info[ 'min_version' ] ) ) {

						if ( version_compare( $req_info[ 'version' ], $req_info[ 'min_version' ], '<' ) ) {

							self::wpsso_init_textdomain();	// If not already loaded, load the textdomain now.

							$notice_msg = __( 'The %1$s version %2$s add-on requires %3$s version %4$s or newer (version %5$s is currently installed).', 'wpsso-wc-metadata' );

							$req_info[ 'notice' ] = sprintf( $notice_msg, $info[ 'name' ], $info[ 'version' ], $req_name, $req_info[ 'min_version' ], $req_info[ 'version' ] );
						}
					}
				}

				if ( ! empty( $req_info[ 'notice' ] ) ) {

					$local_cache[ $key ] = $req_info;
				}
			}

			if ( empty( $local_cache ) ) {

				$local_cache = false;
			}

			return $local_cache;
		}
	}

	WpssoWcmd::get_instance();	// Self-instantiate.
}
