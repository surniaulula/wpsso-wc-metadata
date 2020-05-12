<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2020 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoWcmdSearch' ) ) {

	class WpssoWcmdSearch {

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

			add_action( 'pre_get_posts', array( $this, 'action_pre_get_posts' ), 10000, 1 );

			add_filter( 'posts_search', array( $this, 'filter_posts_search' ), 10000, 2 );
		}

		public function action_pre_get_posts( $wp_query ) {
		
			if ( ! $wp_query->is_main_query() ) {

				return;
			}

			/**
			 * WordPress front-end and admin searches.
			 */
			if ( ! empty( $wp_query->is_search ) ) {

				$wp_query->search_product_meta = array( 's' => isset( $wp_query->query[ 's' ] ) ? $wp_query->query[ 's' ] : '' );

			/**
			 * WooCommerce admin product search (ie. Products > All Products page > Search products button).
			 */
			} elseif ( ! empty( $wp_query->query[ 'product_search' ] ) ) {

				$wp_query->search_product_meta = array( 's' => isset( $_GET[ 's' ] ) ? sanitize_text_field( $_GET[ 's' ] ) : '' );
			}
		}

		public function filter_posts_search( $search, $wp_query ) {

			if ( ! $wp_query->is_main_query() ) {

				return $search;

			} elseif ( empty( $wp_query->search_product_meta[ 's' ] ) ) {

				return $search;
			}

			global $wpdb;

			$product_ids = $this->get_search_product_ids( $wp_query->search_product_meta[ 's' ] );	// Returns an array.

			if ( empty( $product_ids ) ) {
				return $search;
			}

			$post_id_query = ' OR (' . $wpdb->posts . '.ID IN (' . implode( ', ', $product_ids ) . ')) ';

			if ( empty( $search ) ) {

				$search = $post_id_query;

			} elseif ( preg_match( '/^( *AND  *\()(.*)(\)) *$/', $search, $matches ) ) {

				$search = $matches[ 1 ] . '(' . $matches[ 2 ] .') ' . $post_id_query . $matches[ 3 ];
			}

			return $search;
		}

		/**
		 * Always return an array.
		 */
		private function get_search_product_ids( $s ) {

			$product_ids = array();

			$post_ids = $this->get_search_post_ids( $s );	// Returns an array.

			foreach ( $post_ids as $post_id ) {

				$post_obj = get_post( $post_id );

				if ( $post_obj->post_type === 'product_variation') {

					$product_ids[] = $post_obj->post_parent;

				} else {

					$product_ids[] = $post_obj->ID;
				}
			}

			return $product_ids;

		}

		/**
		 * Always return an array.
		 */
		private function get_search_post_ids( $s ) {

			global $wpdb;

			$sql_meta_keys = $this->get_sql_meta_keys();

			if ( empty( $sql_meta_keys ) ) {
				return array();
			}

			$s = stripslashes( trim( $s  ) );

			if ( empty( $s ) ) {
				return array();
			}

			if ( preg_match_all( '/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $s, $matches ) ) {
				$search_terms = $this->get_parsed_search_terms( $matches[ 0 ] );
			} else {
				$search_terms = array( $s );
			}

			if ( empty( $search_terms ) ) {
				return array();
			}

			$db_query = 'SELECT post_id FROM ' . $wpdb->postmeta . ' WHERE meta_key IN (' . implode( ',', $sql_meta_keys ) . ') AND (';

			foreach ( $search_terms as $num => $term ) {

				$db_query .= $num > 0 ? ' OR ' : '';
				
				$db_query .= 'meta_value=\'' . esc_sql( $term ) . '\'';
			}

			$db_query .= ');';

			$post_ids = $wpdb->get_col( $db_query );

			return $post_ids;
		}

		private function get_sql_meta_keys() {

			$wcmd =& WpssoWcmd::get_instance();

			$md_config = WpssoWcmdConfig::get_md_config();

			$sql_meta_keys = array();

			foreach ( $md_config as $md_suffix => $cfg ) {

				if ( ! empty( $cfg[ 'searchable' ] ) ) {

					if ( $metadata_key = $wcmd->wc->get_enabled_metadata_key( $md_suffix, $cfg ) ) {
						
						$sql_meta_keys[] = '\'' . esc_sql( $metadata_key ) . '\'';
					}
				}
			}

			return $sql_meta_keys;
		}

		private function get_parsed_search_terms( $terms ) {

			$checked = array();

			foreach ( $terms as $term ) {

				/**
				 * Keep spaces when term is for exact match.
				 */
				if ( preg_match( '/^".+"$/', $term ) ) {
				 	$term = trim( $term, "\"'" );
				} else {
					$term = trim( $term, "\"' " );
				}

				/**
				 * Avoid single a-z and single dashes.
				 */
				if ( ! $term || ( 1 === strlen( $term ) && preg_match( '/^[a-z\-]$/i', $term ) ) ) {
					continue;
				}

				$checked[] = $term;
			}

			return $checked;
		}
	}
}
