<?php
/**
 * Stationery_Customizer Class
 * Makes adjustments to Storefront cores Customizer implementation.
 *
 * @author   WooThemes
 * @package  Stationery
 * @since    1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Stationery_Customizer' ) ) {
	/**
	 * The Stationery Customizer class
	 */
	class Stationery_Customizer {

		/**
		 * Setup class.
		 *
		 * @since 1.0
		 */
		public function __construct() {
			global $storefront_version;

			add_action( 'wp_enqueue_scripts', 					array( $this, 'add_customizer_css' ),               999 );
			add_action( 'customize_register', 					array( $this, 'edit_default_controls' ),            99 );
			add_filter( 'storefront_custom_background_args',	array( $this, 'stationery_background' ) );
			add_filter( 'storefront_setting_default_values',    array( $this, 'get_stationery_defaults' ) );

			/**
			 * The following can be removed when Storefront 2.1 lands
			 */
			add_action( 'customize_register', 					array( $this, 'edit_default_customizer_settings' ), 99 );
			add_action( 'init',									array( $this, 'default_theme_mod_values' ) );
			if ( version_compare( $storefront_version, '2.0.0', '<' ) ) {
				add_action( 'init',           array( $this, 'default_theme_settings' ) );
			}
		}

		/**
		 * Returns an array of the desired default Storefront options
		 *
		 * @return array
		 */
		public function get_stationery_defaults() {
			return apply_filters( 'stationery_default_settings', $args = array(
				'background_color'                       => 'ffffff',
				'storefront_heading_color'               => '#404040',
				'storefront_text_color'                  => '#888888',
				'storefront_accent_color'                => '#1c4e86',
				'storefront_header_background_color'     => '#ffffff',
				'storefront_header_link_color'           => '#1c4e86',
				'storefront_header_text_color'           => '#aaaaaa',
				'storefront_footer_background_color'     => '#ffffff',
				'storefront_footer_link_color'           => '#404040',
				'storefront_button_background_color'     => '#fb3241',
				'storefront_button_text_color'           => '#ffffff',
				'storefront_button_alt_background_color' => '#17bebb',
				'storefront_button_alt_text_color'       => '#ffffff',
			) );
		}

		/**
		 * Set default Customizer settings based on Stationery design.
		 *
		 * @param  array $wp_customize the Customizer object.
		 * @uses   get_stationery_defaults()
		 * @return void
		 */
		public function edit_default_customizer_settings( $wp_customize ) {
			foreach ( Stationery_Customizer::get_stationery_defaults() as $mod => $val ) {
				$wp_customize->get_setting( $mod )->default = $val;
			}
		}

		/**
		 * Returns a default theme_mod value if there is none set.
		 *
		 * @uses   get_stationery_defaults()
		 * @return void
		 */
		public function default_theme_mod_values() {
			foreach ( Stationery_Customizer::get_stationery_defaults() as $mod => $val ) {
				add_filter( 'theme_mod_' . $mod, function( $setting ) use ( $val ) {
					return $setting ? $setting : $val;
				});
			}
		}

		/**
		 * Sets default theme color filters for storefront color values.
		 * This function is required for Storefront < 2.0.0 support
		 *
		 * @uses   get_stationery_defaults()
		 * @return void
		 * @todo   remove when Storefront 2.1 is released.
		 */
		public function default_theme_settings() {
			$prefix_regex = '/^storefront_/';
			foreach ( self::get_stationery_defaults() as $mod => $val ) {
				if ( preg_match( $prefix_regex, $mod ) ) {
					$filter = preg_replace( $prefix_regex, 'storefront_default_', $mod );
					add_filter( $filter, function( $setting ) use ( $val ) {
						return $val;
					}, 99 );
				}
			}
		}

		/**
		 * Modify the default controls
		 *
		 * @param array $wp_customize the Customizer object.
		 * @return void
		 */
		public function edit_default_controls( $wp_customize ) {
			$wp_customize->get_setting( 'storefront_header_text_color' )->transport = 'refresh';
		}

		/**
		 * Add CSS using settings obtained from the theme options.
		 *
		 * @return void
		 */
		public function add_customizer_css() {
			$accent_color = get_theme_mod( 'storefront_accent_color' );
			$header_text_color = get_theme_mod( 'storefront_header_text_color' );
			$footer_background_color = get_theme_mod( 'storefront_footer_background_color' );
			$button_alt_text_color = get_theme_mod( 'storefront_button_alt_text_color' );
			$button_alt_background_color = get_theme_mod( 'storefront_button_alt_background_color' );
			$button_background_color = get_theme_mod( 'storefront_button_background_color' );

			$style = '
				.main-navigation ul li.smm-active li ul.products li.product h3 {
					color: ' . $header_text_color . ';
				}
				.site-info {
					background: ' . $footer_background_color . ';
				}
				.page-template-template-homepage-php ul.tabs li a.active {
					color: ' . $button_alt_background_color . ';
				}
				.page-template-template-homepage-php ul.tabs li a:after,
				.single-product div.product .woocommerce-product-rating a,
				.comment-form-rating .stars a,
				.widget h3.widget-title:before,
				.widget h3.widget-title:after,
				.widget h2.widgettitle:before,
				.widget h2.widgettitle:after {
					color: ' . $button_alt_background_color . ';
				}
				ul.products li.product-category h3:before,
				ul.products li.product-category h2:before,
				ul.products li.product-category mark:after,
				.woocommerce-active .site-header .main-navigation ul.menu > li > a:before,
				.woocommerce-active .site-header .main-navigation ul.nav-menu > li > a:before,
				.site-header .main-navigation ul.menu > li > a:before,
				.site-header .main-navigation ul.nav-menu > li > a:before,
				.star-rating span:before,
				.star-rating:before,
				.entry-meta a,
				.entry-meta a:visited,
				.posted-on a,
				.posted-on a:visited,
				.byline a,
				.byline a:visited {
					color: ' . $button_alt_background_color . ';
				}
				.site-header-cart .widget_shopping_cart .buttons .button,
				.widget-area .widget_shopping_cart .buttons .button {
					background: ' . $button_alt_background_color . ';
				}
				.site-header-cart .widget_shopping_cart .buttons .button:hover,
				.widget-area .widget_shopping_cart .buttons .button:hover {
					background: ' . storefront_adjust_color_brightness( $button_alt_background_color, -25 ) . ';
				}
				.onsale {
					background: ' . $button_background_color . ';
				}
				.single_add_to_cart_button.button.alt {
					background: ' . $button_background_color . ';
				}
				.single_add_to_cart_button.button.alt:hover {
					background: ' . storefront_adjust_color_brightness( $button_background_color, -25 ) . ';
				}
				.sfb-footer-bar input[type="button"],
				.sfb-footer-bar input[type="submit"],
				.sfb-footer-bar .button {
					background: ' . $button_alt_background_color . ';
				}
				.sfb-footer-bar input[type="button"]:hover,
				.sfb-footer-bar input[type="submit"]:hover,
				.sfb-footer-bar .button:hover {
					background: ' . storefront_adjust_color_brightness( $button_alt_background_color, -25 ) . ';
				}
				.woocommerce-active .site-header .col-full:before,
				.site-header .col-full:before,
				.site-footer .col-full:after {
					background-image: linear-gradient(90deg, ' . $accent_color . ' 2px,transparent 2px),linear-gradient(90deg, ' . $accent_color . ' 2px, transparent 2px);
					background-image: -ms-linear-gradient(90deg, ' . $accent_color . ' 1px,transparent 1px),-ms-linear-gradient(90deg, ' . $accent_color . ' 1px,transparent 1px);
				}
				.main-navigation ul li a,
				.site-title a, ul.menu li a,
				.site-branding h1 a,
				a.cart-contents,
				.widget-area .widget a:hover,
				.site-header-cart .widget_shopping_cart a,
				.woocommerce-active .site-header .site-search form:before,
				.site-footer .storefront-handheld-footer-bar a:not(.button),
				.site-header .site-search form:before,
				ul.products li.product a > h3,
				ul.products li.product-category a h3,
				.woocommerce-breadcrumb a,
				a.woocommerce-review-link,
				.product_meta a {
					color: ' . $accent_color . ';
				}
				button.menu-toggle,
				button.menu-toggle:hover {
					border-color: ' . $accent_color . ';
					color: ' . $accent_color . ';
				}
				.storefront-handheld-footer-bar ul li.cart .count,
				button.menu-toggle:after,
				button.menu-toggle:before,
				button.menu-toggle span:before {
					background-color: ' . $accent_color . ';
				}
				.main-navigation ul li.smm-active li:hover a {
					color: ' . $accent_color . ' !important;
				}
				.page-template-template-homepage-php ul.products li.product-category .s-category-title {
					background: ' . $accent_color . ';
				}
				.sph-hero .sph-inner h1 span,
				.sph-hero .sph-hero-content p {
					background-color: ' . $accent_color . ';
				}
				.main-navigation ul li a:hover,
				.main-navigation ul li:hover > a,
				.site-title a:hover, a.cart-contents:hover,
				.site-header-cart .widget_shopping_cart a:hover,
				.site-header-cart:hover > li > a,
				ul.menu li.current-menu-item > a {
					color: ' . storefront_adjust_color_brightness( $accent_color, 50 ) . ';
				}
				';

			wp_add_inline_style( 'storefront-child-style', $style );
		}

		/**
		 * Stationery background settings
		 *
		 * @param array $args the background arguments.
		 * @return array $args the modified args.
		 */
		public function stationery_background( $args ) {
			$args['default-image'] 		= get_stylesheet_directory_uri() . '/assets/images/texture.png';
			$args['default-attachment'] = 'repeat';

			return $args;
		}
	}
}

return new Stationery_Customizer();
