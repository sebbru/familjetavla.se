<?php
/**
 * Stationery Class
 *
 * @author   WooThemes
 * @package  Stationery
 * @since    1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Stationery' ) ) {
	/**
	 * The main Stationery class
	 */
	class Stationery {
		/**
		 * Setup class.
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_child_styles' ), 99 );

			add_action( 'storefront_loop_columns', array( $this, 'loop_columns' ) );
			add_action( 'swc_product_columns_default', array( $this, 'loop_columns' ) );
			add_filter( 'storefront_product_categories_args', array( $this, 'stationery_homepage_categories' ) );
			add_filter( 'storefront_related_products_args',	array( $this, 'related_products_args' ) );
			add_filter( 'storefront_products_per_page',	array( $this, 'products_per_page' ) );
		}

		/**
		 * Enqueue Storefront Styles
		 *
		 * @return void
		 */
		public function enqueue_styles() {
			global $storefront_version;

			wp_enqueue_style( 'storefront-style', get_template_directory_uri() . '/style.css', $storefront_version );
		}

		/**
		 * Enqueue Storechild Styles
		 *
		 * @return void
		 */
		public function enqueue_child_styles() {
			global $storefront_version, $stationery_version;

			wp_style_add_data( 'storefront-child-style', 'rtl', 'replace' );

			wp_enqueue_style( 'karla', '//fonts.googleapis.com/css?family=Karla:400,400italic,700,700italic', array( 'storefront-child-style' ) );
			wp_enqueue_style( 'bitter', '//fonts.googleapis.com/css?family=Bitter:400,400italic,700', array( 'storefront-child-style' ) );

			/**
			 * Javascript
			 */
			wp_enqueue_script( 'stationery', get_stylesheet_directory_uri() . '/assets/js/stationery.min.js', array( 'jquery' ), $stationery_version, true );
		}


		/**
		 * Specified how many categories to display on the homepage
		 *
		 * @param array $args The arguments used to control the layout of the homepage product sections.
		 */
		public function stationery_homepage_categories( $args ) {
			$args['limit'] 		= 5;
			$args['columns'] 	= 5;

			return $args;
		}

		/**
		 * Shop columns
		 * @return int number of columns
		 */
		public function loop_columns( $columns ) {
			$columns = 4;
			return $columns;
		}

		/**
		 * Adjust related products columns
		 * @return array $args the modified arguments
		 */
		public function related_products_args( $args ) {
			$args['posts_per_page'] = 4;
			$args['columns']		= 4;

			return $args;
		}

		/**
		 * Products per page
		 * @return int products to display per page
		 */
		public function products_per_page( $per_page ) {
			$per_page = 12;
			return intval( $per_page );
		}
	}
}

return new Stationery();
