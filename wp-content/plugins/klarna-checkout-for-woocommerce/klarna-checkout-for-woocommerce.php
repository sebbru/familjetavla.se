<?php
/*
 * Plugin Name: Klarna Checkout for WooCommerce
 * Plugin URI: https://krokedil.com/klarna/
 * Description: Klarna Checkout payment gateway for WooCommerce.
 * Author: Krokedil
 * Author URI: https://krokedil.com/
 * Version: 1.2.5
 * Text Domain: klarna-checkout-for-woocommerce
 * Domain Path: /languages
 *
 * WC requires at least: 3.0
 * WC tested up to: 3.3
 *
 * Copyright (c) 2017-2018 Krokedil
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Required minimums and constants
 */
define( 'KCO_WC_VERSION', '1.2.5' );
define( 'KCO_WC_MIN_PHP_VER', '5.3.0' );
define( 'KCO_WC_MIN_WC_VER', '2.5.0' );
define( 'KCO_WC_MAIN_FILE', __FILE__ );
define( 'KCO_WC_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

if ( ! class_exists( 'Klarna_Checkout_For_WooCommerce' ) ) {
	/**
	 * Class Klarna_Checkout_For_WooCommerce
	 */
	class Klarna_Checkout_For_WooCommerce {

		/**
		 * The reference the *Singleton* instance of this class.
		 *
		 * @var $instance
		 */
		protected static $instance;

		/**
		 * Reference to API class.
		 *
		 * @var $api
		 */
		public $api;

		/**
		 * Reference to merchant URLs class.
		 *
		 * @var $merchant_urls
		 */
		public $merchant_urls;

		/**
		 * Reference to order lines class.
		 *
		 * @var $order_lines
		 */
		public $order_lines;

		/**
		 * Reference to credentials class.
		 *
		 * @var $credentials
		 */
		public $credentials;

		/**
		 * Reference to logging class.
		 *
		 * @var $log
		 */
		public $logger;

		/**
		 * Returns the *Singleton* instance of this class.
		 *
		 * @return self::$instance The *Singleton* instance.
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Private clone method to prevent cloning of the instance of the
		 * *Singleton* instance.
		 *
		 * @return void
		 */
		private function __clone() {
			wc_doing_it_wrong( __FUNCTION__, __( 'Nope' ), '1.0' );
		}

		/**
		 * Private unserialize method to prevent unserializing of the *Singleton*
		 * instance.
		 *
		 * @return void
		 */
		private function __wakeup() {
			wc_doing_it_wrong( __FUNCTION__, __( 'Nope' ), '1.0' );
		}

		/**
		 * Notices (array)
		 *
		 * @var array
		 */
		public $notices = array();

		/**
		 * Protected constructor to prevent creating a new instance of the
		 * *Singleton* via the `new` operator from outside of this class.
		 */
		protected function __construct() {
			add_action( 'admin_notices', array( $this, 'admin_notices' ), 15 );
			add_action( 'plugins_loaded', array( $this, 'init' ) );
			add_action( 'admin_notices', array( $this, 'order_management_check' ) );

			// Add quantity button in woocommerce_order_review() function.
			add_filter( 'woocommerce_checkout_cart_item_quantity', array( $this, 'add_quantity_field' ), 10, 3 );
		}

		/**
		 * Init the plugin after plugins_loaded so environment variables are set.
		 */
		public function init() {
			// Init the gateway itself.
			$this->init_gateways();
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
		}

		/**
		 * Adds plugin action links
		 *
		 * @param array $links Plugin action link before filtering.
		 *
		 * @return array Filtered links.
		 */
		public function plugin_action_links( $links ) {
			$setting_link = $this->get_setting_link();
			$plugin_links = array(
				'<a href="' . $setting_link . '">' . __( 'Settings', 'klarna-checkout-for-woocommerce' ) . '</a>',
				'<a href="http://krokedil.se/">' . __( 'Support', 'klarna-checkout-for-woocommerce' ) . '</a>',
			);

			return array_merge( $plugin_links, $links );
		}

		/**
		 * Get setting link.
		 *
		 * @since 1.0.0
		 *
		 * @return string Setting link
		 */
		public function get_setting_link() {
			$section_slug = 'kco';

			return admin_url( 'admin.php?page=wc-settings&tab=checkout&section=' . $section_slug );
		}

		/**
		 * Show admin notice if Order Management plugin is not active.
		 */
		public function order_management_check() {
			/**
			 * Check if file exists
			 *  - yes: check if activated
			 *    - yes: all good
			 *    - no: show activate button
			 * - no: show install button
			 */

			$plugin_slug = 'klarna-order-management-for-woocommerce';

			// If plugin file exists.
			if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin_slug . '/' . $plugin_slug . '.php' ) ) {
				// If plugin is not active show Activate button.
				if ( ! is_plugin_active( $plugin_slug . '/' . $plugin_slug . '.php' ) && current_user_can( 'activate_plugins' ) ) {
					include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
					$plugin      = plugins_api( 'plugin_information', array(
						'slug' => $plugin_slug,
					) );
					$plugin      = (array) $plugin;
					$status      = install_plugin_install_status( $plugin );
					$name        = wp_kses( $plugin['name'], array() );
					$url         = add_query_arg( array(
						'_wpnonce' => wp_create_nonce( 'activate-plugin_' . $status['file'] ),
						'action'   => 'activate',
						'plugin'   => $status['file'],
					), network_admin_url( 'plugins.php' ) );
					$description = $name . ' is not active. Please activate it so you can capture, cancel, update and refund Klarna orders.';
					?>
					<div class="notice notice-warning">
						<p>
							<?php echo esc_html( $description ); ?>
							<a class="install-now button" data-slug="<?php esc_attr_e( $plugin_slug ); ?>"
							   href="<?php echo esc_url( $url ); ?>"
							   aria-label="Activate <?php esc_attr_e( $name ); ?> now" data-name="<?php esc_attr_e( $name ); ?>"><?php _e( 'Activate Now', 'klarna-checkout-for-woocommerce' ); ?></a>
						</p>
					</div>
					<?php
				}
			} else { // If plugin file does not exist, show Install button.
				if ( current_user_can( 'install_plugins' ) ) {
					include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
					$plugin = plugins_api( 'plugin_information', array(
						'slug' => $plugin_slug,
					) );
					$plugin = (array) $plugin;
					$status = install_plugin_install_status( $plugin );
					if ( 'install' === $status['status'] && $status['url'] ) {
						$name        = wp_kses( $plugin['name'], array() );
						$url         = $status['url'];
						$description = $name . ' is not installed. Please install and activate it so you can capture, cancel, update and refund Klarna orders.';
						?>
						<div class="notice notice-warning">
							<p>
								<?php echo esc_html( $description ); ?>
								<a class="install-now button" data-slug="<?php esc_attr_e( $plugin_slug ); ?>"
								   href="<?php echo esc_url( $url ); ?>"
								   aria-label="Install <?php esc_attr_e( $name ); ?> now" data-name="<?php esc_attr_e( $name ); ?>"><?php _e( 'Install	Now', 'klarna-checkout-for-woocommerce' ); ?></a>
							</p>
						</div>
						<?php
					}
				}
			} // End if().
		}

		/**
		 * Display any notices we've collected thus far (e.g. for connection, disconnection)
		 */
		public function admin_notices() {
			foreach ( (array) $this->notices as $notice_key => $notice ) {
				echo "<div class='" . esc_attr( $notice['class'] ) . "'><p>";
				echo wp_kses( $notice['message'], array( 'a' => array( 'href' => array() ) ) );
				echo '</p></div>';
			}
		}

		/**
		 * Initialize the gateway. Called very early - in the context of the plugins_loaded action
		 *
		 * @since 1.0.0
		 */
		public function init_gateways() {
			if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
				return;
			}

			include_once KCO_WC_PLUGIN_PATH . '/includes/class-klarna-checkout-for-woocommerce-gateway.php';
			include_once KCO_WC_PLUGIN_PATH . '/includes/class-klarna-checkout-for-woocommerce-api.php';
			include_once KCO_WC_PLUGIN_PATH . '/includes/class-klarna-checkout-for-woocommerce-api-callbacks.php';
			include_once KCO_WC_PLUGIN_PATH . '/includes/class-klarna-checkout-for-woocommerce-templates.php';
			include_once KCO_WC_PLUGIN_PATH . '/includes/class-klarna-checkout-for-woocommerce-ajax.php';
			include_once KCO_WC_PLUGIN_PATH . '/includes/class-klarna-checkout-for-woocommerce-order-lines.php';
			include_once KCO_WC_PLUGIN_PATH . '/includes/class-klarna-checkout-for-woocommerce-merchant-urls.php';
			include_once KCO_WC_PLUGIN_PATH . '/includes/class-klarna-checkout-for-woocommerce-credentials.php';
			include_once KCO_WC_PLUGIN_PATH . '/includes/class-klarna-checkout-for-woocommerce-logging.php';
			include_once KCO_WC_PLUGIN_PATH . '/includes/class-klarna-checkout-for-woocommerce-fields.php';
			include_once KCO_WC_PLUGIN_PATH . '/includes/class-klarna-checkout-for-woocommerce-confirmation.php';
			include_once KCO_WC_PLUGIN_PATH . '/includes/class-klarna-checkout-for-woocommerce-extra-checkout-fields.php';
			include_once KCO_WC_PLUGIN_PATH . '/includes/klarna-checkout-for-woocommerce-functions.php';

			if ( is_admin() ) {
				include_once KCO_WC_PLUGIN_PATH . '/includes/class-klarna-checkout-for-woocommerce-admin-notices.php';
			}

			$this->api           = new Klarna_Checkout_For_WooCommerce_API();
			$this->merchant_urls = new Klarna_Checkout_For_WooCommerce_Merchant_URLs();
			$this->order_lines   = new Klarna_Checkout_For_WooCommerce_Order_Lines();
			$this->credentials   = new Klarna_Checkout_For_WooCommerce_Credentials();
			$this->logger        = new Klarna_Checkout_For_WooCommerce_Logging();

			load_plugin_textdomain( 'klarna-checkout-for-woocommerce', false, plugin_basename( __DIR__ ) . '/languages' );
			add_filter( 'woocommerce_payment_gateways', array( $this, 'add_gateways' ) );
		}

		/**
		 * Add the gateways to WooCommerce
		 *
		 * @param  array $methods Payment methods.
		 *
		 * @return array $methods Payment methods.
		 * @since  1.0.0
		 */
		public function add_gateways( $methods ) {
			$methods[] = 'Klarna_Checkout_For_WooCommerce_Gateway';

			return $methods;
		}

		/**
		 * Filters cart item quantity output.
		 *
		 * @param string $output        HTML output.
		 * @param array  $cart_item     Cart item.
		 * @param string $cart_item_key Cart item key.
		 *
		 * @return string $output
		 */
		public function add_quantity_field( $output, $cart_item, $cart_item_key ) {
			if ( 'kco' === WC()->session->get( 'chosen_payment_method' ) ) {
				foreach ( WC()->cart->get_cart() as $cart_key => $cart_value ) {
					if ( $cart_key === $cart_item_key ) {
						$_product = $cart_item['data'];

						if ( $_product->is_sold_individually() ) {
							$return_value = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_key );
						} else {
							$return_value = woocommerce_quantity_input( array(
								'input_name'  => 'cart[' . $cart_key . '][qty]',
								'input_value' => $cart_item['quantity'],
								'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
								'min_value'   => '1',
							), $_product, false );
						}

						$output = $return_value;
					}
				}
			}

			return $output;
		}

	}

	Klarna_Checkout_For_WooCommerce::get_instance();
}

/**
 * Main instance Klarna_Checkout_For_WooCommerce WooCommerce.
 *
 * Returns the main instance of Klarna_Checkout_For_WooCommerce.
 *
 * @return Klarna_Checkout_For_WooCommerce
 */
function KCO_WC() {
	return Klarna_Checkout_For_WooCommerce::get_instance();
}
