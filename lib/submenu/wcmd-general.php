<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2020-2021 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoWcmdSubmenuWcmdGeneral' ) && class_exists( 'WpssoAdmin' ) ) {

	class WpssoWcmdSubmenuWcmdGeneral extends WpssoAdmin {

		public function __construct( &$plugin, $id, $name, $lib, $ext ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$this->menu_id   = $id;
			$this->menu_name = $name;
			$this->menu_lib  = $lib;
			$this->menu_ext  = $ext;
		}

		/**
		 * Called by the extended WpssoAdmin class.
		 */
		protected function add_meta_boxes() {

			$metabox_id      = 'wcmd';
			$metabox_title   = _x( 'WooCommerce Metadata', 'metabox title', 'wpsso-wc-metadata' );
			$metabox_screen  = $this->pagehook;
			$metabox_context = 'normal';
			$metabox_prio    = 'default';
			$callback_args   = array(	// Second argument passed to the callback function / method.
			);

			add_meta_box( $this->pagehook . '_' . $metabox_id, $metabox_title,
				array( $this, 'show_metabox_' . $metabox_id ), $metabox_screen,
					$metabox_context, $metabox_prio, $callback_args );
		}

		public function show_metabox_wcmd() {

			$metabox_id = 'wcmd';

			$tab_key = 'general';

			$filter_name = SucomUtil::sanitize_hookname( 'wpsso_' . $metabox_id . '_' . $tab_key . '_rows' );

			$table_rows = $this->get_table_rows( $metabox_id, $tab_key );

			$table_rows = apply_filters( $filter_name, $table_rows, $this->form, $network = false );

			$this->p->util->metabox->do_table( $table_rows, 'metabox-' . $metabox_id . '-' . $tab_key );
		}

		protected function get_table_rows( $metabox_id, $tab_key ) {

			$table_rows = array();

			switch ( $metabox_id . '-' . $tab_key ) {

				case 'wcmd-general':

					$table_rows[] = '<td colspan="5">' . $this->p->msgs->get( 'info-wcmd-custom-fields' ) . '</td>';

					$table_rows[] = '' . 
						'<th></th>' .
						'<th class="xshort option_col"><h3>' . __( 'Enable', 'wpsso-wc-metadata' ) . '</h3></th>' .
						'<th class="short option_col"><h3>' . __( 'Input Label', 'wpsso-wc-metadata' ) . '</h3></th>' .
						'<th class="medium option_col"><h3>' . __( 'Input Placeholder', 'wpsso-wc-metadata' ) . '</h3></th>' .
						'<th class="wide option_col"><h3>' . __( 'Information Label', 'wpsso-wc-metadata' ) . '</h3></th>';

					$md_config = WpssoWcmdConfig::get_md_config();

					foreach ( $md_config as $md_suffix => $cfg ) {

						$table_rows[ 'wcmd_input_label_' . $md_suffix ] = '' . 
							$this->form->get_th_html_locale( _x( $cfg[ 'label' ], 'option label', 'wpsso-wc-metadata' ),
								$css_class = '', $css_id = 'wcmd_input_label_' . $md_suffix ) . 
							'<td class="checkbox">' . $this->form->get_checkbox( 'wcmd_enable_' . $md_suffix ) . '</td>' .
							'<td class="short">' . $this->form->get_input_locale( 'wcmd_input_label_' . $md_suffix, $css_class = 'short' ) . '</td>' .
							'<td class="medium">' . $this->form->get_input_locale( 'wcmd_input_holder_' . $md_suffix, $css_class = 'medium' ) . '</td>' .
							'<td class="wide">' . $this->form->get_input_locale( 'wcmd_info_label_' . $md_suffix, $css_class = 'wide' ) . '</td>';
					}

					break;
			}

			return $table_rows;
		}
	}
}
