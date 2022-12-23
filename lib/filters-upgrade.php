<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2022 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoWcmdFiltersUpgrade' ) ) {

	class WpssoWcmdFiltersUpgrade {

		private $p;	// Wpsso class object.
		private $a;	// WpssoWcmd class object.

		/**
		 * Instantiated by WpssoWcmdFilters->__construct().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			$this->p->util->add_plugin_filters( $this, array(
				'upgraded_md_options' => 2,
			) );
		}

		public function filter_upgraded_md_options( $md_opts, $mod ) {

			/**
			 * Remove old meta data.
			 */
			if ( ! empty( $mod[ 'id' ] ) && ! empty( $mod[ 'obj' ] ) ) {	// Just in case.

				$prev_version = $this->p->opt->get_version( $md_opts, 'wpssowcmd' );

				if ( $prev_version <= 17 ) {

					$md_config = WpssoWcmdConfig::get_md_config();

					foreach ( $md_config as $md_key => $cfg ) {

						$opt_key = 'plugin_cf_' . $md_key;

						$meta_value_key   = $this->p->options[ $opt_key ];
						$meta_units_key   = preg_replace( '/_value$/', '', $meta_value_key ) . '_units';
						$meta_unit_wc_key = $meta_value_key . '_unit_wc';

						$mod[ 'obj' ]->delete_meta( $mod[ 'id' ], $meta_units_key );
						$mod[ 'obj' ]->delete_meta( $mod[ 'id' ], $meta_unit_wc_key );
					}
				}
			}

			return $md_opts;
		}
	}
}
