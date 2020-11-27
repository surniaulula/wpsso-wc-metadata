<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2020 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoWcmdWooCommerce' ) ) {

	class WpssoWcmdWooCommerce {

		private $p;	// Wpsso class object.
		private $a;     // WpssoWcmd class object.

		public function __construct( &$plugin ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			if ( is_admin() ) {

				/**
				 * Product settings.
				 */
				add_filter( 'woocommerce_products_general_settings', array( $this, 'filter_products_general_settings' ), 10, 1 );

				/**
				 * Product data.
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

			} else {

				add_filter( 'woocommerce_display_product_attributes', array( $this, 'filter_display_product_attributes' ), 10, 2 );

				add_action( 'woocommerce_variable_add_to_cart', array( $this, 'enqueue_script_add_to_cart_variation' ), 10, 0 );
			}
		}

		public function filter_products_general_settings( $settings ) {

			$fl_vol_units = WpssoUtilWooCommerce::get_fluid_volume_units();

			$fl_vol_settings = array(
				'id'       => 'woocommerce_fluid_volume_unit',
				'class'    => 'wc-enhanced-select',
				'css'      => 'min-width:300px;',
				'title'    => __( 'Fluid volume unit', 'wpsso-wc-metadata' ),
				'type'     => 'select',
				'options'  => $fl_vol_units,
				'default'  => 'ml',
				'desc_tip' => true,
				'desc'     => __( 'This controls what unit you will define fluid volumes in.', 'wpsso-wc-metadata' ),
			);

			$dim_pos = 0;

			foreach ( $settings as $pos => $val ) {

				if ( isset( $val[ 'id' ] ) && 'woocommerce_dimension_unit' === $val[ 'id' ] ) {

					$dim_pos = $pos;
				}
			}

			if ( $dim_pos ) {	// Just in case.

				array_splice( $settings, $dim_pos + 1, 0, array( $fl_vol_settings ) );
			}

			return $settings;
		}

		public function show_metadata_options() {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$action_name = current_action();	// Since WP v3.9.

			$md_config = WpssoWcmdConfig::get_md_config();

			foreach ( $md_config as $md_suffix => $cfg ) {

				if ( empty( $cfg[ 'actions' ][ $action_name ] ) ) {

					continue;
				}

				if ( $metadata_key = $this->get_enabled_metadata_key( $md_suffix, $cfg ) ) {

					$label_transl  = SucomUtil::get_key_value( 'wcmd_input_label_' . $md_suffix, $this->p->options );
					$holder_transl = SucomUtil::get_key_value( 'wcmd_input_holder_' . $md_suffix, $this->p->options );
					$desc_transl   = isset( $cfg[ 'desc' ] ) ? $cfg[ 'desc' ] : '';
					$unit_transl   = isset( $cfg[ 'unit_label' ] ) ? $cfg[ 'unit_label' ] : '';

					$label_transl  = sprintf( $label_transl, $unit_transl );
					$holder_transl = sprintf( $holder_transl, $unit_transl );
					$desc_transl   = sprintf( $desc_transl, $label_transl, $unit_transl );

					woocommerce_wp_text_input( array(
						'name'              => $metadata_key,
						'id'                => $metadata_key,
						'class'             => isset( $cfg[ 'class' ] ) ? $cfg[ 'class' ] : null,
						'style'             => isset( $cfg[ 'style' ] ) ? $cfg[ 'style' ] : null,
						'label'             => $label_transl,
						'placeholder'       => $holder_transl,
						'type'              => isset( $cfg[ 'type' ] ) ? $cfg[ 'type' ] : 'text',
						'data_type'         => isset( $cfg[ 'data_type' ] ) ? $cfg[ 'data_type' ] : '',
						'desc_tip'          => empty( $desc_transl ) ? false : true,
						'description'       => $desc_transl,
						'custom_attributes' => isset( $cfg[ 'custom_attrs' ] ) ? $cfg[ 'custom_attrs' ] : '',
					) );
				}
			}
		}

		public function save_metadata_options( $product ) {

			$md_config = WpssoWcmdConfig::get_md_config();

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

					if ( isset( $cfg[ 'unit_wc_1x' ] ) ) {

						/**
						 * Get a new metakey without the '_unit_wc' suffix.
						 */
						if ( $metadata_key = $this->get_enabled_metadata_key( $md_suffix ) ) {

							if ( null !== $value ) {

								$value *= $cfg[ 'unit_wc_1x' ];
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

			$action_name = current_action();	// Since WP v3.9.

			$md_config = WpssoWcmdConfig::get_md_config();

			foreach ( $md_config as $md_suffix => $cfg ) {

				if ( empty( $cfg[ 'actions' ][ $action_name ] ) ) {

					continue;
				}

				if ( $metadata_key = $this->get_enabled_metadata_key( $md_suffix, $cfg ) ) {

					$label_transl  = SucomUtil::get_key_value( 'wcmd_input_label_' . $md_suffix, $this->p->options );
					$holder_transl = SucomUtil::get_key_value( 'wcmd_input_holder_' . $md_suffix, $this->p->options );
					$desc_transl   = isset( $cfg[ 'desc' ] ) ? $cfg[ 'desc' ] : '';
					$unit_transl   = isset( $cfg[ 'unit_label' ] ) ? $cfg[ 'unit_label' ] : '';
					$var_obj       = $this->p->util->wc->get_product( $variation->ID );
					$var_meta_val  = $var_obj->get_meta( $metadata_key, $single = true );
					$row_input_num = $row_input_num >= $row_input_max ? 1 : $row_input_num + 1;

					$label_transl  = sprintf( $label_transl, $unit_transl );
					$holder_transl = sprintf( $holder_transl, $unit_transl );
					$desc_transl   = sprintf( $desc_transl, $label_transl, $unit_transl );

					/**
					 * Maybe use the main product metadata value in the placeholder.
					 */
					$prod_id       = $var_obj->get_parent_id();
					$prod_obj      = $this->p->util->wc->get_product( $prod_id );
					$prod_meta_val = $prod_obj->get_meta( $metadata_key, $single = true );

					if ( '' !== $prod_meta_val ) {

						$holder_transl = $prod_meta_val;
					}

					woocommerce_wp_text_input( array(
						'wrapper_class'     => 'form-row ' . ( $row_input_num === 1 ? 'form-row-first' : 'form-row-last' ),
						'value'             => $var_meta_val,
						'name'              => $metadata_key . '_variable[' . $loop . ']',
						'id'                => $metadata_key . '_variable_' . $loop,
						'class'             => isset( $cfg[ 'class' ] ) ? $cfg[ 'class' ] : null,
						'style'             => isset( $cfg[ 'style' ] ) ? $cfg[ 'style' ] : null,
						'label'             => $label_transl,
						'placeholder'       => $holder_transl,
						'type'              => isset( $cfg[ 'type' ] ) ? $cfg[ 'type' ] : 'text',
						'data_type'         => isset( $cfg[ 'data_type' ] ) ? $cfg[ 'data_type' ] : '',
						'desc_tip'          => empty( $desc_transl ) ? false : true,
						'description'       => $desc_transl,
						'custom_attributes' => isset( $cfg[ 'custom_attrs' ] ) ? $cfg[ 'custom_attrs' ] : '',
					) );
				}
			}
		}

		public function save_metadata_options_variation( $variation_id, $id ) {

			$variation = $this->p->util->wc->get_product( $variation_id );

			$have_update = false;

			$md_config = WpssoWcmdConfig::get_md_config();

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

					if ( isset( $cfg[ 'unit_wc_1x' ] ) ) {

						/**
						 * Get a new metakey without the '_unit_wc' suffix.
						 */
						if ( $metadata_key = $this->get_enabled_metadata_key( $md_suffix ) ) {

							if ( null !== $value ) {

								$value *= $cfg[ 'unit_wc_1x' ];
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

		public function filter_display_product_attributes( $product_attributes, $product ) {

			$filter_name = current_filter();

			$product_id = $this->p->util->wc->get_product_id( $product );

			$md_config = WpssoWcmdConfig::get_md_config();

			foreach ( $md_config as $md_suffix => $cfg ) {

				if ( empty( $cfg[ 'filters' ][ $filter_name ] ) ) {

					continue;
				}

				if ( $metadata_key = $this->get_enabled_metadata_key( $md_suffix, $cfg ) ) {

					$prod_meta_val = $product->get_meta( $metadata_key, $single = true );

					if ( '' !== $prod_meta_val ) {

						$label_transl = SucomUtil::get_key_value( 'wcmd_info_label_' . $md_suffix, $this->p->options );
						$unit_transl  = isset( $cfg[ 'unit_label' ] ) ? $cfg[ 'unit_label' ] : '';

						$label_transl  = sprintf( $label_transl, $unit_transl );
						$prod_meta_val = $prod_meta_val . ' ' . $unit_transl;

						if ( ! empty( $cfg[ 'insert_after' ] ) ) {

							SucomUtil::add_after_key( $product_attributes, $cfg[ 'insert_after' ], $md_suffix, null );
						}

						$product_attributes[ $md_suffix ] = array(
							'label' => '<span class="wcmd_vars_metadata_label">' . $label_transl . '</span>',
							'value' => '<span class="wcmd_vars_metadata_value">' . $prod_meta_val . '</span>',
						);
					}
				}
			}

			wp_localize_script( $handle = 'wpsso-wcmd-add-to-cart-variation',
				$object_name = 'wcmd_vars_metadata_prod_id_' . $product_id,
					$l10n = $this->get_vars_metadata( $product ) );

			return $product_attributes;
		}

		private function get_vars_metadata( $product ) {

			$md_config = WpssoWcmdConfig::get_md_config();

			$ret = array();

			foreach ( $md_config as $md_suffix => $cfg ) {

				if ( $metadata_key = $this->get_enabled_metadata_key( $md_suffix, $cfg ) ) {

					$available_vars = $this->p->util->wc->get_available_variations( $product );	// Always returns an array.

					foreach( $available_vars as $num => $variation ) {

						$var_id = $variation[ 'variation_id' ];

						if ( $var_obj = $this->p->util->wc->get_product( $var_id ) ) {

							$var_meta_val = $var_obj->get_meta( $metadata_key, $single = true );

							if ( '' !== $var_meta_val ) {

								$ret[ $var_id ][ $md_suffix ] = $var_meta_val;
							}
						}
					}
				}
			}

			return $ret;
		}

		public function enqueue_script_add_to_cart_variation() {

			$doing_dev = SucomUtil::get_const( 'WPSSO_DEV' );
			$file_ext  = $doing_dev ? 'js' : 'min.js';
			$version   = WpssoWcmdConfig::get_version();

			wp_register_script( 'wpsso-wcmd-add-to-cart-variation',
				WPSSOWCMD_URLPATH . 'js/jquery-add-to-cart-variation.' . $file_ext,
					array( 'jquery' ), $version, true );

			wp_enqueue_script( 'wpsso-wcmd-add-to-cart-variation' );
		}

		public function get_enabled_metadata_key( $md_suffix, $cfg = array() ) {

			if ( empty( $this->p->options[ 'wcmd_enable_' . $md_suffix ] ) ) {

				return null;

			} elseif ( empty( $this->p->options[ 'plugin_cf_' . $md_suffix ] ) ) {

				return null;
			}

			$metadata_key = $this->p->options[ 'plugin_cf_' . $md_suffix ];

			$metadata_key = apply_filters( 'wpsso_wc_metadata_plugin_cf_' . $md_suffix, $metadata_key );

			if ( isset( $cfg[ 'unit_wc_1x' ] ) ) {

				$metadata_key .= '_unit_wc';
			}

			return SucomUtil::sanitize_hookname( $metadata_key );
		}
	}
}
