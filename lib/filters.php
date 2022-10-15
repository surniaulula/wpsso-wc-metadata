<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2020-2022 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoWcmdFilters' ) ) {

	class WpssoWcmdFilters {

		private $p;	// Wpsso class object.
		private $a;     // WpssoWcmd class object.
		private $msgs;	// WpssoWcmdFiltersMessages class object.

		/**
		 * Instantiated by WpssoWcmd->init_objects().
		 */
		public function __construct( &$plugin, &$addon ) {

			static $do_once = null;

			if ( true === $do_once ) {

				return;	// Stop here.
			}

			$do_once = true;

			$this->p =& $plugin;
			$this->a =& $addon;

			$this->p->util->add_plugin_filters( $this, array(
				'get_defaults' => 1,
				'option_type'  => 2,
			) );

			if ( is_admin() ) {

				require_once WPSSOWCMD_PLUGINDIR . 'lib/filters-messages.php';

				$this->msgs = new WpssoWcmdFiltersMessages( $plugin, $addon );
			}
		}

		public function filter_get_defaults( $defs ) {

			$md_config = WpssoWcmdConfig::get_md_config();

			foreach ( $md_config as $md_key => $cfg ) {

				foreach ( $cfg[ 'defaults' ] as $opt_pre => $val ) {

					$defs[ $opt_pre . '_' . $md_key ] = $val;
				}
			}

			return $defs;
		}

		public function filter_option_type( $type, $base_key ) {

			/**
			 * The custom field name may be changed from the default value, but should not be empty.
			 */
			if ( 0 === strpos( $base_key, 'plugin_cf_' ) ) {

				/**
				 * Return 'not_blank_quiet' for WooCommerce metadata custom fields.
				 */
				$md_suffix = substr( $base_key, strlen( 'plugin_cf_' ) );

				if ( isset( $this->p->options[ 'wcmd_enable_' . $md_suffix ] ) ) {

					return 'not_blank_quiet';
				}
			}

			if ( ! empty( $type ) ) {	// Return early if we already have a type.

				return $type;

			} elseif ( 0 !== strpos( $base_key, 'wcmd_' ) ) {	// Nothing to do.

				return $type;
			}

			switch ( $base_key ) {

				case ( false !== strpos( $base_key, 'wcmd_input_label_' ) ? true : false ):

					return 'not_blank';

				case ( false !== strpos( $base_key, 'wcmd_input_holder_' ) ? true : false ):

					return 'one_line';
			}

			return $type;
		}
	}
}
