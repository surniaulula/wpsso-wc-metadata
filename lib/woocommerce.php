<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2017-2020 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoWcMdWooCommerce' ) ) {

	class WpssoWcMdWooCommerce {

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

			if ( is_admin() ) {

				/**
				 * Product.
				 */
				add_action( 'woocommerce_product_options_sku', array( $this, 'show_metadata_options' ), -1000, 0 );
				add_action( 'woocommerce_product_options_dimensions', array( $this, 'show_metadata_options' ), -1000, 0 );
				add_action( 'woocommerce_admin_process_product_object', array( $this, 'save_metadata_options'), -1000, 1 ); 
		
				/**
				 * Product variations.
				 */
				add_action( 'woocommerce_variation_options_pricing', array( $this, 'show_metadata_options_variation'), -1000, 3 );
				add_action( 'woocommerce_variation_options_dimensions', array( $this, 'show_metadata_options_variation'), -1000, 3 );
				add_action( 'woocommerce_save_product_variation', array( $this, 'save_metadata_options_variation'), -1000, 2 );
			}
		}

		public function show_metadata_options() {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$action = current_action();	// Since WP v3.9.

			$md_config = WpssoWcMdConfig::get_md_config();

			foreach ( $md_config as $md_suffix => $cfg ) {

				if ( empty( $cfg[ 'action' ][ $action ] ) ) {
					continue;
				}

				if ( $metadata_key = $this->get_enabled_metadata_key( $md_suffix, $cfg ) ) {

					$label_transl  = SucomUtil::get_key_value( 'wcmd_label_' . $md_suffix, $this->p->options );
					$holder_transl = SucomUtil::get_key_value( 'wcmd_holder_' . $md_suffix, $this->p->options );
					$cf_fragments  = $this->p->msgs->get_cf_tooltip_fragments( $md_suffix );

					if ( ! empty( $cfg[ 'printf_args' ] ) ) {
						$label_transl  = vsprintf( $label_transl, $cfg[ 'printf_args' ] );
						$holder_transl = vsprintf( $holder_transl, $cfg[ 'printf_args' ] );
					}

					woocommerce_wp_text_input( array(
						'name'        => $metadata_key,
						'id'          => $metadata_key,
						'label'       => $label_transl,
						'placeholder' => $holder_transl,
						'type'        => isset( $cfg[ 'type' ] ) ? $cfg[ 'type' ] : 'text',
						'data_type'   => isset( $cfg[ 'data_type' ] ) ? $cfg[ 'data_type' ] : '',
						'desc_tip'    => isset( $cf_fragments[ 1 ] ) ? true : false,
						'description' => isset( $cf_fragments[ 1 ] ) ? sprintf( __( '%1$s refers to %2$s.', 'wpsso-wc-metadata' ),
							$label_transl, $cf_fragments[ 1 ] ) : '',
					) );
				}
			}
		}

		public function save_metadata_options( $product ) {

			$md_config = WpssoWcMdConfig::get_md_config();

			foreach ( $md_config as $md_suffix => $cfg ) {

				if ( $metadata_key = $this->get_enabled_metadata_key( $md_suffix, $cfg ) ) {

					$value = null;

					if ( isset( $_POST[ $metadata_key ] ) ) {

						$value = trim( wc_clean( wp_unslash( $_POST[ $metadata_key ] ) ) );

						if ( '' === $value ) {
							$value = null;
						}
					}

					$product->update_meta_data( $metadata_key, $value );

					if ( isset( $cfg[ 'unit_1x' ] ) ) {

						if ( $metadata_key = $this->get_enabled_metadata_key( $md_suffix ) ) {

							if ( null !== $value ) {
								$value *= $cfg[ 'unit_1x' ];
							}
						}

						$product->update_meta_data( $metadata_key, $value );
					}
				}
			}
		}

		/**
		 * $loop = 1, 2, 3, etc.
		 */
		public function show_metadata_options_variation( $loop, $variation_data, $variation ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$row_input_num = 0;
			$row_input_max = 2;

			$action = current_action();	// Since WP v3.9.

			$md_config = WpssoWcMdConfig::get_md_config();

			foreach ( $md_config as $md_suffix => $cfg ) {

				if ( empty( $cfg[ 'action' ][ $action ] ) ) {
					continue;
				}

				if ( $metadata_key = $this->get_enabled_metadata_key( $md_suffix, $cfg ) ) {

					$label_transl  = SucomUtil::get_key_value( 'wcmd_label_' . $md_suffix, $this->p->options );
					$holder_transl = SucomUtil::get_key_value( 'wcmd_holder_' . $md_suffix, $this->p->options );
					$cf_fragments  = $this->p->msgs->get_cf_tooltip_fragments( $md_suffix );
					$var_obj       = wc_get_product( $variation->ID );
					$var_meta_val  = $var_obj->get_meta( $metadata_key, $single = true );
					$row_input_num = $row_input_num >= $row_input_max ? 1 : $row_input_num + 1;

					if ( ! empty( $cfg[ 'printf_args' ] ) ) {
						$label_transl  = vsprintf( $label_transl, $cfg[ 'printf_args' ] );
						$holder_transl = vsprintf( $holder_transl, $cfg[ 'printf_args' ] );
					}

					if ( '' === $var_meta_val ) {

						$prod_id       = $var_obj->get_parent_id();
						$prod_obj      = wc_get_product( $prod_id );
						$prod_meta_val = $prod_obj->get_meta( $metadata_key, $single = true );

						if ( '' !== $prod_meta_val ) {
							$holder_transl = $prod_meta_val;
						}
					}

					woocommerce_wp_text_input( array(
						'wrapper_class' => 'form-row ' . ( $row_input_num === 1 ? 'form-row-first' : 'form-row-last' ),
						'value'         => $var_meta_val,
						'name'          => $metadata_key . '_variable[' . $loop . ']',
						'id'            => $metadata_key . '_variable_' . $loop,
						'label'         => $label_transl,
						'placeholder'   => $holder_transl,
						'type'          => isset( $cfg[ 'type' ] ) ? $cfg[ 'type' ] : 'text',
						'data_type'     => isset( $cfg[ 'data_type' ] ) ? $cfg[ 'data_type' ] : '',
						'desc_tip'      => isset( $cf_fragments[ 1 ] ) ? true : false,
						'description'   => isset( $cf_fragments[ 1 ] ) ? sprintf( __( '%1$s refers to %2$s.', 'wpsso-wc-metadata' ),
							$label_transl, $cf_fragments[ 1 ] ) : '',
					) );
				}
			}
		}

		public function save_metadata_options_variation( $variation_id, $id ) {

			$variation = wc_get_product( $variation_id );

			$have_update = false;

			$md_config = WpssoWcMdConfig::get_md_config();

			foreach ( $md_config as $md_suffix => $cfg ) {

				if ( $metadata_key = $this->get_enabled_metadata_key( $md_suffix, $cfg ) ) {

					$value = null;

					if ( isset( $_POST[ $metadata_key . '_variable' ][ $id ] ) ) {

						$value = trim( wc_clean( wp_unslash( $_POST[ $metadata_key . '_variable' ][ $id ] ) ) );

						if ( '' === $value ) {
							$value = null;
						}
					}

					$variation->update_meta_data( $metadata_key, $value );

					if ( isset( $cfg[ 'unit_1x' ] ) ) {

						if ( $metadata_key = $this->get_enabled_metadata_key( $md_suffix ) ) {

							if ( null !== $value ) {
								$value *= $cfg[ 'unit_1x' ];
							}
						}

						$variation->update_meta_data( $metadata_key, $value );
					}
			
					$have_update = true;
				}
			}

			if ( $have_update ) {
				$variation->save_meta_data();
			}
		}

		private function get_enabled_metadata_key( $md_suffix, $cfg = array() ) {

			if ( empty( $this->p->options[ 'wcmd_enable_' . $md_suffix ] ) ) {
				return null;
			} elseif ( empty( $this->p->options[ 'plugin_cf_' . $md_suffix ] ) ) {
				return null;
			}

			$metadata_key = $this->p->options[ 'plugin_cf_' . $md_suffix ];

			$metadata_key = apply_filters( 'wpsso_wc_metadata_plugin_cf_' . $md_suffix, $metadata_key );

			if ( ! empty( $cfg[ 'unit_1x' ] ) ) {
				$metadata_key .= '_unit_1x' . $cfg[ 'unit_1x' ];
			}

			return SucomUtil::sanitize_hookname( $metadata_key );
		}
	}
}
