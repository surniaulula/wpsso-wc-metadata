<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2020-2022 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoWcmdWoocommerce' ) ) {

	class WpssoWcmdWoocommerce {

		private $p;	// Wpsso class object.
		private $a;     // WpssoWcmd class object.

		/**
		 * Instantiated by WpssoWcmd->init_objects().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			if ( is_admin() ) {

				/**
				 * Product settings.
				 */
				add_filter( 'woocommerce_products_general_settings', array( $this, 'filter_products_general_settings' ), 10, 1 );

				/**
				 * Product data.
				 */
				add_action( 'woocommerce_product_options_sku', array( $this, 'edit_metadata_options' ), -1000, 0 );
				add_action( 'woocommerce_product_options_dimensions', array( $this, 'edit_metadata_options' ), -1000, 0 );
				add_action( 'woocommerce_admin_process_product_object', array( $this, 'save_metadata_options'), -1000, 1 );

				/**
				 * Product variations.
				 */
				add_action( 'woocommerce_variation_options_pricing', array( $this, 'edit_metadata_options_variation'), -1000, 3 );
				add_action( 'woocommerce_variation_options_dimensions', array( $this, 'edit_metadata_options_variation'), -1000, 3 );
				add_action( 'woocommerce_save_product_variation', array( $this, 'save_metadata_options_variation'), -1000, 2 );

			} else {

				add_filter( 'wc_product_enable_dimensions_display', array( $this, 'filter_enable_additional_information' ), 10, 1 );
				add_filter( 'woocommerce_display_product_attributes', array( $this, 'filter_show_additional_information' ), 10, 2 );

				add_action( 'woocommerce_variable_add_to_cart', array( $this, 'enqueue_script_add_to_cart_variation' ), 10, 0 );
			}

			$this->disable_options_keys();
		}

		/**
		 * Since WPSSO WCMD v2.0.0.
		 */
		public function disable_options_keys() {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$fl_vol_unit_text = get_option( 'woocommerce_fluid_volume_unit', $default = 'ml' );

			foreach ( array(
				'og_def_fluid_volume_units' => $fl_vol_unit_text,		// Default Fluid Volume Units.
			) as $opt_key => $opt_val ) {

				$this->p->options[ $opt_key ]               = $opt_val;
				$this->p->options[ $opt_key . ':disabled' ] = true;
			}
		}

		/**
		 * Hooked to 'woocommerce_products_general_settings' filter.
		 */
		public function filter_products_general_settings( $settings ) {

			$fl_vol_units = WpssoUtilUnits::get_fluid_volume_units();

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

		/**
		 * Hooked to 'woocommerce_product_options_sku' action.
		 * Hooked to 'woocommerce_product_options_dimensions' action.
		 */
		public function edit_metadata_options() {

			$action_name = current_action();	// Since WP v3.9.
			$md_config   = WpssoWcmdConfig::get_md_config();

			foreach ( $md_config as $md_key => $cfg ) {

				if ( empty( $cfg[ 'actions' ][ $action_name ] ) ) {

					continue;
				}

				if ( $meta_key = $this->get_edit_metadata_key( $md_key ) ) {	// Always returns a string.

					$label_transl  = SucomUtil::get_key_value( 'wcmd_edit_label_' . $md_key, $this->p->options );
					$holder_transl = SucomUtil::get_key_value( 'wcmd_edit_holder_' . $md_key, $this->p->options );
					$desc_transl   = isset( $cfg[ 'desc' ] ) ? $cfg[ 'desc' ] : '';
					$unit_transl   = isset( $cfg[ 'unit_label' ] ) ? $cfg[ 'unit_label' ] : '';
					$label_transl  = sprintf( $label_transl, $unit_transl );
					$holder_transl = sprintf( $holder_transl, $unit_transl );
					$desc_transl   = sprintf( $desc_transl, $label_transl, $unit_transl );

					woocommerce_wp_text_input( array(
						'name'              => $meta_key,
						'id'                => $meta_key,
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

		/**
		 * Hooked to 'woocommerce_admin_process_product_object' action.
		 */
		public function save_metadata_options( $product ) {

			$md_config = WpssoWcmdConfig::get_md_config();

			foreach ( $md_config as $md_key => $cfg ) {

				if ( $meta_key = $this->get_edit_metadata_key( $md_key ) ) {	// Always returns a string.

					$meta_value = null;

					if ( isset( $_POST[ $meta_key ] ) ) {

						$meta_value = trim( wc_clean( wp_unslash( $_POST[ $meta_key ] ) ) );

						if ( '' === $meta_value ) {

							$meta_value = null;
						}
					}

					$product->update_meta_data( $meta_key, $meta_value );
				}
			}
		}

		/**
		 * Hooked to 'woocommerce_variation_options_pricing' action.
		 * Hooked to 'woocommerce_variation_options_dimensions' action.
		 *
		 * $loop = 1, 2, 3, etc.
		 */
		public function edit_metadata_options_variation( $loop, $variation_data, $variation ) {

			$row_input_num = 0;
			$row_input_max = 2;
			$action_name   = current_action();	// Since WP v3.9.
			$md_config     = WpssoWcmdConfig::get_md_config();

			foreach ( $md_config as $md_key => $cfg ) {

				if ( empty( $cfg[ 'actions' ][ $action_name ] ) ) {

					continue;
				}

				if ( $meta_key = $this->get_edit_metadata_key( $md_key ) ) {	// Always returns a string.

					$label_transl  = SucomUtil::get_key_value( 'wcmd_edit_label_' . $md_key, $this->p->options );
					$holder_transl = SucomUtil::get_key_value( 'wcmd_edit_holder_' . $md_key, $this->p->options );
					$desc_transl   = isset( $cfg[ 'desc' ] ) ? $cfg[ 'desc' ] : '';
					$unit_transl   = isset( $cfg[ 'unit_label' ] ) ? $cfg[ 'unit_label' ] : '';
					$var_obj       = $this->p->util->wc->get_product( $variation->ID );
					$var_meta_val  = $var_obj->get_meta( $meta_key, $single = true );
					$row_input_num = $row_input_num >= $row_input_max ? 1 : $row_input_num + 1;
					$label_transl  = sprintf( $label_transl, $unit_transl );
					$holder_transl = sprintf( $holder_transl, $unit_transl );
					$desc_transl   = sprintf( $desc_transl, $label_transl, $unit_transl );

					/**
					 * Maybe use the main product metadata value in the placeholder.
					 */
					$prod_id       = $var_obj->get_parent_id();
					$prod_obj      = $this->p->util->wc->get_product( $prod_id );
					$prod_meta_val = $prod_obj->get_meta( $meta_key, $single = true );

					if ( '' !== $prod_meta_val ) {

						$holder_transl = $prod_meta_val;
					}

					woocommerce_wp_text_input( array(
						'wrapper_class'     => 'form-row ' . ( $row_input_num === 1 ? 'form-row-first' : 'form-row-last' ),
						'value'             => $var_meta_val,
						'name'              => $meta_key . '_variable[' . $loop . ']',
						'id'                => $meta_key . '_variable_' . $loop,
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

		/**
		 * Hooked to 'woocommerce_save_product_variation' action.
		 */
		public function save_metadata_options_variation( $variation_id, $id ) {

			$variation   = $this->p->util->wc->get_product( $variation_id );
			$have_update = false;
			$md_config   = WpssoWcmdConfig::get_md_config();

			foreach ( $md_config as $md_key => $cfg ) {

				if ( $meta_key = $this->get_edit_metadata_key( $md_key ) ) {	// Always returns a string.

					$meta_value = null;

					if ( isset( $_POST[ $meta_key . '_variable' ][ $id ] ) ) {

						$meta_value = trim( wc_clean( wp_unslash( $_POST[ $meta_key . '_variable' ][ $id ] ) ) );

						if ( '' === $meta_value ) {

							$meta_value = null;
						}
					}

					$variation->update_meta_data( $meta_key, $meta_value );

					$have_update = true;
				}
			}

			if ( $have_update ) {

				$variation->save_meta_data();
			}
		}

		/**
		 * Hooked to 'wc_product_enable_dimensions_display' filter.
		 *
		 * Return true to enable the "Additional information" tab.
		 */
		public function filter_enable_additional_information( $bool ) {

			if ( $bool ) {	// Already enabled - nothing to do.

				return $bool;
			}

			global $product;

			$filter_name = 'woocommerce_display_product_attributes';
			$md_config   = WpssoWcmdConfig::get_md_config();

			foreach ( $md_config as $md_key => $cfg ) {

				if ( empty( $cfg[ 'filters' ][ $filter_name ] ) ) {

					continue;
				}

				if ( $meta_keys = $this->get_show_metadata_keys( $md_key ) ) {	// Always returns an array.

					return true;
				}
			}

			return $bool;
		}

		/**
		 * Hooked to 'woocommerce_display_product_attributes' filter.
		 */
		public function filter_show_additional_information( $product_attributes, $product ) {

			$filter_name = 'woocommerce_display_product_attributes';
			$product_id  = $this->p->util->wc->get_product_id( $product );
			$md_config   = WpssoWcmdConfig::get_md_config();

			foreach ( $md_config as $md_key => $cfg ) {

				if ( empty( $cfg[ 'filters' ][ $filter_name ] ) ) {

					continue;
				}

				if ( $meta_keys = $this->get_show_metadata_keys( $md_key ) ) {	// Always returns an array.

					$unit_transl  = isset( $cfg[ 'unit_label' ] ) ? $cfg[ 'unit_label' ] : '';
					$label_transl = SucomUtil::get_key_value( 'wcmd_show_label_' . $md_key, $this->p->options );
					$label_transl = sprintf( $label_transl, $unit_transl );
					$meta_value   = $this->get_meta_keys_values( $meta_keys, $cfg, $product );

					if ( '' !== $meta_value ) {

						$product_attributes[ $md_key ] = array(
							'label' => '<span class="wcmd_vars_metadata_label">' . $label_transl . '</span>',
							'value' => '<span class="wcmd_vars_metadata_value">' . $meta_value . '</span>',
						);
					}
				}
			}

			wp_localize_script( $handle = 'wpsso-wcmd-add-to-cart-variation',
				$object_name = 'wcmd_vars_metadata_prod_id_' . $product_id,
					$l10n = $this->get_variations_meta( $product ) );

			return $product_attributes;
		}

		/**
		 * Hooked to 'woocommerce_variable_add_to_cart' action.
		 */
		public function enqueue_script_add_to_cart_variation() {

			$doing_dev = SucomUtil::get_const( 'WPSSO_DEV' );
			$file_ext  = $doing_dev ? 'js' : 'min.js';
			$version   = WpssoWcmdConfig::get_version();

			wp_register_script( 'wpsso-wcmd-add-to-cart-variation',
				WPSSOWCMD_URLPATH . 'js/jquery-add-to-cart-variation.' . $file_ext,
					array( 'jquery' ), $version, true );

			wp_enqueue_script( 'wpsso-wcmd-add-to-cart-variation' );
		}

		/**
		 * Always returns a string.
		 *
		 * See WpssoWcmdSearch->get_sql_meta_keys().
		 */
		public function get_edit_metadata_key( $md_key ) {

			return $this->get_enabled_metadata_key( $md_key, $context = 'edit' );	// Always returns a string.
		}

		/**
		 * Always returns an array.
		 */
		public function get_show_metadata_keys( $md_key ) {

			if ( $meta_key = $this->get_enabled_metadata_key( $md_key, $context = 'show' ) ) {	// Always returns a string.

				return array( $meta_key );
			}

			$md_config = WpssoWcmdConfig::get_md_config();

			if ( ! empty( $md_config[ $md_key ][ 'implode' ][ 'md_keys' ] ) ) {

				$meta_keys = array();

				foreach ( $md_config[ $md_key ][ 'implode' ][ 'md_keys' ] as $md_key ) {

					if ( $meta_key = self::get_enabled_metadata_key( $md_key ) ) {	// Always returns a string.

						$meta_keys[] = SucomUtil::sanitize_hookname( $meta_key );
					}
				}

				return $meta_keys;	// Returns an array.
			}

			return array();
		}

		/**
		 * Always returns a string.
		 */
		public function get_enabled_metadata_key( $md_key, $context = '' ) {

			if ( empty( $context ) || ! empty( $this->p->options[ 'wcmd_' . $context . '_' . $md_key ] ) ) {

				if ( ! empty( $this->p->options[ 'plugin_cf_' . $md_key ] ) ) {	// Just in case.

					if ( $meta_key = $this->p->options[ 'plugin_cf_' . $md_key ] ) {

						return SucomUtil::sanitize_hookname( $meta_key );
					}
				}
			}

			return '';
		}

		/**
		 * See wp_localize_script() in WpssoWcmdWoocommerce->filter_show_additional_information().
		 */
		private function get_variations_meta( $product ) {

			$vars_meta  = array();
			$md_config  = WpssoWcmdConfig::get_md_config();
			$avail_vars = $this->p->util->wc->get_available_variations( $product );	// Always returns an array.

			foreach( $avail_vars as $num => $variation ) {

				$var_id = $variation[ 'variation_id' ];

				if ( $var_obj = $this->p->util->wc->get_product( $var_id ) ) {

					foreach ( $md_config as $md_key => $cfg ) {

						if ( $meta_keys = $this->get_show_metadata_keys( $md_key ) ) {	// Always returns an array.

							$meta_value = $this->get_meta_keys_values( $meta_keys, $cfg, $product, $var_obj );

							if ( '' !== $meta_value ) {

								$vars_meta[ $var_id ][ $md_key ] = $meta_value;
							}
						}
					}
				}
			}

			return $vars_meta;
		}

		private function get_meta_keys_values( array $meta_keys, array $cfg, $product, $var_obj = null ) {

			$unit_transl  = isset( $cfg[ 'unit_label' ] ) ? $cfg[ 'unit_label' ] : '';
			$meta_value   = '';
			$meta_sep     = isset( $cfg[ 'implode' ][ 'separator' ] ) ? $cfg[ 'implode' ][ 'separator' ] : '';

			foreach ( $meta_keys as $meta_key ) {	// One or more meta keys.

				$obj_meta = '';

				if ( $var_obj ) {	// If we have a variation object, get that first.

					$obj_meta = $var_obj->get_meta( $meta_key, $single = true );
				}

				if ( '' === $obj_meta  ) {

					$obj_meta = $product->get_meta( $meta_key, $single = true );
				}

				if ( '' !== $obj_meta  ) {

					if ( '' !== $meta_sep && '' !== $meta_value ) {

						$meta_value .= $meta_sep;
					}

					$meta_value .= $obj_meta;
				}
			}

			if ( '' !== $meta_value  ) {

				$meta_value .= ' ' . $unit_transl;
			}

			return $meta_value;
		}
	}
}
