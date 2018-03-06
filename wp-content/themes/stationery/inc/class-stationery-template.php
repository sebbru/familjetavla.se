<?php
/**
 * Stationery_Template Class
 *
 * @author   WooThemes
 * @package  Stationery
 * @since    1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Stationery_Template' ) ) {
	/**
	 * The Stationery Template Class
	 */
	class Stationery_Template {

		/**
		 * Setup class.
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_action( 'storefront_header', array( $this, 'primary_navigation_wrapper' ),       45 );
			add_action( 'storefront_header', array( $this, 'primary_navigation_wrapper_close' ), 65 );

			add_action( 'storefront_before_footer',  array( $this, 'footer_bar_wrapper' ), 10 );
			add_action( 'storefront_before_footer',  array( $this, 'footer_bar_wrapper_close' ), 11 );

			add_action( 'woocommerce_before_subcategory_title', array( $this, 'stationery_category_title_wrapper' ), 11 );
			add_action( 'woocommerce_after_subcategory_title', array( $this, 'stationery_category_title_wrapper_close' ), 2 );

			add_action( 'init', array( $this, 'stationery_woocommerce_image_dimensions' ), 1 );
			add_action( 'init', array( $this, 'stationery_custom_storefront_markup' ) );
		}

		/**
		 * Primary navigation wrapper
		 *
		 * @return void
		 */
		public function primary_navigation_wrapper() {
			echo '<section class="stationery-primary-navigation">';
		}

		/**
		 * Primary navigation wrapper close
		 *
		 * @return void
		 */
		public function primary_navigation_wrapper_close() {
			echo '</section>';
		}

		/**
		 * Top bar wrapper
		 * @return void
		 */
		public static function top_bar_wrapper() {
			echo '<section class="s-top-bar">';
		}
		/**
		 * Top bar wrapper close
		 * @return void
		 */
		public static function top_bar_wrapper_close() {
			echo '</section>';
		}

		/**
		 * Product title wrapper
		 * @return void
		 */
		public function stationery_category_title_wrapper() {
			echo '<section class="s-category-title">';
		}

		/**
		 * Product title wrapper close
		 * @return void
		 */
		public function stationery_category_title_wrapper_close() {
			echo '</section>';
		}

		/**
		 * Wrap homepage section titles inside a `span`.
		 *
		 * @param  array $args homepage section arguments.
		 * @return array       homepage section arguments
		 */
		public function homepage_section_title( $args ) {
			$title          = $args['title'];
			$args['title']  = '<span>' . $title . '</span>';
			return $args;
		}

		/**
		 * Wrap homepage blog section titles inside a `span`.
		 *
		 * @param  string $blog_section_title blog section title markup
		 * @param  string $title 			  blog section title
		 * @return string      				  homepage section arguments
		 */
		public function stationery_blog_title( $blog_section_title, $title ) {
		 	return '<h2 class="section-title"><span>' . esc_attr( $title ) . '</span></h2>';
		 }

		/**
		 * Footer bar wrapper
		 * @return void
		 */
		public function footer_bar_wrapper() {
			if ( class_exists( 'Storefront_Footer_Bar' ) && is_active_sidebar( 'footer-bar-1' ) ) {
				echo '<div class="stationery-footer-bar-wrapper">';
			}
		}
		/**
		 * Footer bar wrapper (closing tag)
		 * @return void
		 */
		public function footer_bar_wrapper_close() {
			if ( class_exists( 'Storefront_Footer_Bar' ) && is_active_sidebar( 'footer-bar-1' ) ) {
				echo '</div><!-- .stationery-footer-bar-wrapper -->';
			}
		}

		/**
		 * Define custom Catalog image size
		 * @return void
		 */
		public function stationery_woocommerce_image_dimensions() {
		  	$catalog = array(
				'width' 	=> '578',	// px
				'height'	=> '578',	// px
				'crop'		=> 1 		// true
			);

			update_option( 'shop_catalog_image_size', $catalog );
		}

		/**
		 * Custom markup tweaks
		 *
		 * @return void
		 */
		public function stationery_custom_storefront_markup() {

			if ( class_exists( 'WooCommerce' ) ) {
				remove_action( 'storefront_header', 'storefront_header_cart', 60 );
				add_action( 'storefront_header', 'storefront_header_cart', 4 );

				remove_action( 'storefront_header', 'storefront_product_search', 40 );
				add_action( 'storefront_header', 'storefront_product_search', 3 );
			}

			remove_action( 'storefront_header', 'storefront_secondary_navigation', 30 );
			add_action( 'storefront_header', 'storefront_secondary_navigation', 1 );

			remove_action( 'storefront_header', 'storefront_site_branding', 20 );
			add_action( 'storefront_header', 'storefront_site_branding', 5 );

			add_action( 'storefront_header', array( 'Stationery_Template', 'top_bar_wrapper' ), 2 );
			add_action( 'storefront_header', array( 'Stationery_Template', 'top_bar_wrapper_close' ), 6 );

			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
			add_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_sale_flash', 10 );

			add_filter( 'storefront_product_categories_args',   	   array( $this, 'homepage_section_title' ), 999 );
			add_filter( 'storefront_featured_products_args',    	   array( $this, 'homepage_section_title' ), 999 );
			add_filter( 'storefront_recent_products_args',      	   array( $this, 'homepage_section_title' ), 999 );
			add_filter( 'storefront_popular_products_args',     	   array( $this, 'homepage_section_title' ), 999 );
			add_filter( 'storefront_on_sale_products_args',     	   array( $this, 'homepage_section_title' ), 999 );
			add_filter( 'storefront_homepage_blog_section_title_html', array( $this, 'stationery_blog_title' ), 10, 2 );
		}
	}
}

return new Stationery_Template();
