<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2020 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoWcmdFilters' ) ) {

	class WpssoWcmdFilters {

		private $p;

		public function __construct( &$plugin ) {

			/**
			 * Just in case - prevent filters from being hooked and executed more than once.
			 */
			static $do_once = null;

			if ( true === $do_once ) {
				return;	// Stop here.
			}

			$do_once = true;

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$this->p->util->add_plugin_filters( $this, array( 
				'option_type'  => 2,
				'get_defaults' => 1,
			) );

			if ( is_admin() ) {

				$this->p->util->add_plugin_filters( $this, array( 
					'messages_tooltip' => 2,
				) );
			}
		}

		public function filter_option_type( $type, $base_key ) {

			if ( 0 === ( $pos = strpos( $base_key, 'plugin_cf_' ) ) ) {

				$md_suffix = substr( $base_key, strlen( 'plugin_cf_' ) );

				if ( ! empty( $this->p->options[ 'wcmd_enable_' . $md_suffix ] ) ) {

					return 'not_blank';
				}
			}

			if ( ! empty( $type ) ) {	// Already have a type.

				return $type;

			} elseif ( 0 !== strpos( $base_key, 'wcmd_' ) ) {	// Only handle our own options.

				return $type;
			}

			switch ( $base_key ) {

				case ( false !== strpos( $base_key, 'wcmd_input_label_' ) ? true : false ):

					return 'not_blank';

					break;

				case ( false !== strpos( $base_key, 'wcmd_input_holder_' ) ? true : false ):

					return 'one_line';

					break;
			}

			return $type;
		}

		public function filter_get_defaults( $def_opts ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$md_config = WpssoWcmdConfig::get_md_config();

			foreach ( $md_config as $md_key => $cfg ) {

				foreach ( $cfg[ 'defaults' ] as $opt_pre => $val ) {

					$def_opts[ $opt_pre . '_' . $md_key ] = $val;
				}
			}

			return $def_opts;
		}

		public function filter_messages_tooltip( $text, $msg_key ) {

			if ( 0 !== strpos( $msg_key, 'tooltip-wcmd_' ) ) {	// Only handle our own tooltips.
				return $text;
			}

			switch ( $msg_key ) {

				case ( false !== strpos( $msg_key, 'tooltip-wcmd_input_label_' ) ? true : false ):

					$text .= __( 'Enable or disable additional information fields, modify the input label, input placeholder (can also be blank), and the information label.', 'wpsso-wc-metadata' );

					break;
			}

			return $text;
		}
	}
}
