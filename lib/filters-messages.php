<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2020-2022 Jean-Sebastien Morisset (https://wpsso.com/)
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

			$this->p->util->add_plugin_filters( $this, array(
				'messages_info'    => 2,
				'messages_tooltip' => 2,
			) );
		}

		public function filter_messages_info( $text, $msg_key ) {

			if ( 0 !== strpos( $msg_key, 'info-wcmd-' ) ) {

				return $text;
			}

			switch ( $msg_key ) {

				case 'info-wcmd-custom-fields':

					$text .= '<blockquote class="top-info">';

					$text .= __( 'Enabled WooCommerce metadata fields are included in the WooCommerce product data metabox and shown under the Additional information tab on the product page.', 'wpsso-wc-metadata' );

					$text .= '</blockquote>';

					break;
			}

			return $text;
		}

		public function filter_messages_tooltip( $text, $msg_key ) {

			if ( 0 !== strpos( $msg_key, 'tooltip-wcmd_' ) ) {

				return $text;
			}

			switch ( $msg_key ) {

				case ( 0 === strpos( $msg_key, 'tooltip-wcmd_input_label_' ) ? true : false ):

					$text .= __( 'Enable (or disable) this WooCommerce metadata field, modify the input field label, input field placeholder, and the information label shown on the product page.', 'wpsso-wc-metadata' ) . ' ';

					$opt_key = 'plugin_cf_' . substr( $msg_key, 25 );

					if ( ! empty( $this->p->options[ $opt_key ] ) ) {

						$text .= sprintf( __( 'The WooCommerce metadata field value is saved in the WooCommerce product or variation %s custom field name (aka metadata name).', 'wpsso-wc-metadata' ),  '<code>' . $this->p->options[ $opt_key ] . '</code>' );
					}

					break;
			}

			return $text;
		}
	}
}
