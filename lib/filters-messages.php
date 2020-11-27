<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2020 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoWcmdFiltersMessages' ) ) {

	class WpssoWcmdFiltersMessages {

		private $p;	// Wpsso class object.
		private $a;     // WpssoWcmd class object.

		/**
		 * Instantiated by WpssoWcmdFilters->__construct().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			if ( is_admin() ) {

				$this->p->util->add_plugin_filters( $this, array( 
					'messages_tooltip'  => 2,
				) );
			}
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
