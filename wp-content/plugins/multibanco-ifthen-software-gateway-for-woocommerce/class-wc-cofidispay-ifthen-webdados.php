<?php
/**
 * Cofidis Pay class
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'COFIDISPAY_IFTHEN_DESC_LEN', 200 );

/**
 * CofidisPay IfThen Class.
 */
if ( ! class_exists( 'WC_CofidisPay_IfThen_Webdados' ) ) {

	class WC_CofidisPay_IfThen_Webdados extends WC_Payment_Gateway {

		/* Single instance */
		protected static $_instance = null;
		public static $instances    = 0;

		/* Properties */
		public $debug;
		public $debug_email;
		public $version;
		public $secret_key;
		public $api_url;
		public $limits_api_url;
		public $cofidispaykey;
		public $settings_saved;
		public $send_to_admin;
		public $only_portugal;
		public $only_above;
		public $only_below;

		/**
		 * Constructor for your payment class
		 *
		 * @access public
		 * @return void
		 */
		public function __construct() {

			self::$instances++;

			$this->id = WC_IfthenPay_Webdados()->cofidispay_id;

			// Logs
			$this->debug       = ( $this->get_option( 'debug' ) == 'yes' ? true : false );
			$this->debug_email = $this->get_option( 'debug_email' );

			// Check version and upgrade
			$this->version = WC_IfthenPay_Webdados()->get_version();
			$this->upgrade();

			$this->has_fields = false;

			$this->method_title       = __( 'Cofidis Pay (IfthenPay)', 'multibanco-ifthen-software-gateway-for-woocommerce' );
			$this->method_description = __( 'Pay for your order in 3 to 12 interest-free and fee-free installments using your debit or credit card.', 'multibanco-ifthen-software-gateway-for-woocommerce' );
			/*
			if ( $this->get_option( 'support_woocommerce_subscriptions' ) == 'yes' ) {
				$this->supports = array(
					'products',
					'subscription_suspension',
					'subscription_reactivation',
					'subscription_date_changes',
					'subscriptions',                           //Deprecated?
					'subscription_payment_method_change_admin' //Deprecated?
				); //products is by default
			}*/
			$this->secret_key = $this->get_option( 'secret_key' );
			if ( trim( $this->secret_key ) == '' ) {
				// First load?
				$this->secret_key = md5( home_url() . time() . wp_rand( 0, 999 ) );
				// Save
				$this->update_option( 'secret_key', $this->secret_key );
				$this->update_option( 'debug', 'yes' );
				// Let's set the callback activation email as NOT sent
				update_option( $this->id . '_callback_email_sent', 'no' );
			}

			// Webservice
			$this->api_url            = 'https://ifthenpay.com/api/cofidis/init/'; // production and test mode, depends on Cofidis Pay Key
			$this->limits_api_url     = 'https://ifthenpay.com/api/cofidis/limits/';

			// Plugin options and settings
			$this->init_form_fields();
			$this->init_settings();

			// User settings
			$this->title                     = $this->get_option( 'title' );
			$this->description               = $this->get_option( 'description' );
			$this->cofidispaykey             = $this->get_option( 'cofidispaykey' );
			$this->settings_saved            = $this->get_option( 'settings_saved' );
			$this->send_to_admin             = ( $this->get_option( 'send_to_admin' ) == 'yes' ? true : false );
			$this->only_portugal             = ( $this->get_option( 'only_portugal' ) == 'yes' ? true : false );
			$this->only_above                = $this->get_option( 'only_above' );
			$this->only_below                = $this->get_option( 'only_bellow' );

			// Actions and filters
			if ( self::$instances === 1 ) { // Avoid duplicate actions and filters if it's initiated more than once (if WooCommerce loads after us)

				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'send_callback_email' ) );
				if ( WC_IfthenPay_Webdados()->wpml_active ) {
					add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'register_wpml_strings' ) );
				}
				add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou' ) );
				add_action( 'woocommerce_order_details_after_order_table', array( $this, 'order_details_after_order_table' ), 9 );
				add_filter( 'woocommerce_available_payment_gateways', array( $this, 'disable_if_settings_missing' ) );
				add_filter( 'woocommerce_available_payment_gateways', array( $this, 'disable_if_currency_not_euro' ) );
				add_filter( 'woocommerce_available_payment_gateways', array( $this, 'disable_unless_portugal' ) );
				add_filter( 'woocommerce_available_payment_gateways', array( $this, 'disable_only_above_or_below' ) );

				// NO SMS Integrations for Cofidis Pay

				// Customer Emails
				// Regular orders
				add_action(
					apply_filters( 'cofidispay_ifthen_email_hook', 'woocommerce_email_before_order_table' ),
					array( $this, 'email_instructions_1' ), // Avoid "Hyyan WooCommerce Polylang Integration" remove_action
					apply_filters( 'cofidispay_ifthen_email_hook_priority', 10 ),
					4
				);

				// Payment listener - Return from payment gateway
				add_action( 'woocommerce_api_wc_cofidispayreturn_ifthen_webdados', array( $this, 'return_payment_gateway' ) );

				// Payment listener - IfthenPay callback
				add_action( 'woocommerce_api_wc_cofidispay_ifthen_webdados', array( $this, 'callback' ) );

				// Admin notices
				add_action( 'admin_notices', array( $this, 'admin_notices' ) );

				// Sandbox mode for known keys
				add_filter( 'cofidispay_ifthen_sandbox', function( $bool ) {
					$known_sandbox_keys = array(
						'AAA-000001'
					);
					if ( in_array( $this->cofidispaykey , $known_sandbox_keys )) {
						return true;
					}
					return $bool;
				});

				// Method title in sandbox mode
				if ( apply_filters( 'cofidispay_ifthen_sandbox', false ) ) {
					$this->title .= ' - SANDBOX (TEST MODE)';
				}

				// Add info to description
				$this->description = sprintf(
					'%1$s<br/><small>%2$s<br/>%3$s</small>',
					$this->description,
					__( 'You will be redirected to a secure page to make the payment.', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
					__( 'Payment of installments will be made to the customer’s debit or credit card through a payment solution based on a factoring contract between Cofidis and the Merchant. Find out more at Cofidis, registered with Banco de Portugal under number 921.', 'multibanco-ifthen-software-gateway-for-woocommerce' ),

				);
			}

			// Ensures only one instance of our plugin is loaded or can be loaded - works if WooCommerce loads the payment gateways before we do
			if ( is_null( self::$_instance ) ) {
				self::$_instance = $this;
			}

		}

		/* Ensures only one instance of our plugin is loaded or can be loaded */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Upgrades (if needed)
		 */
		function upgrade() {
			if ( $this->get_option( 'version' ) < $this->version ) {
				$current_options = get_option( 'woocommerce_' . $this->id . '_settings', '' );
				if ( ! is_array( $current_options ) ) {
					$current_options = array();
				}
				// Upgrade
				$this->debug_log( 'Upgrade to ' . $this->version . ' started' );
				// Nothing so far
				// ...
				// Upgrade on the database - Risky?
				$current_options['version'] = $this->version;
				update_option( 'woocommerce_' . $this->id . '_settings', $current_options );
				$this->debug_log( 'Upgrade to ' . $this->version . ' finished' );
			}
		}

		/**
		 * WPML compatibility
		 */
		function register_wpml_strings() {
			// These are already registered by WooCommerce Multilingual
			/*
			$to_register=array(
				'title',
				'description',
			);*/
			$to_register = array();
			foreach ( $to_register as $string ) {
				icl_register_string( $this->id, $this->id . '_' . $string, $this->settings[ $string ] );
			}
		}

		/**
		 * Initialise Gateway Settings Form Fields
		 * 'setting-name' => array(
		 *      'title' => __( 'Title for setting', 'woothemes' ),
		 *      'type' => 'checkbox|text|textarea',
		 *      'label' => __( 'Label for checkbox setting', 'woothemes' ),
		 *      'description' => __( 'Description for setting' ),
		 *      'default' => 'default value'
		 *  ),
		 */
		function init_form_fields() {

			$this->form_fields = array(
				'enabled'       => array(
					'title'       => __( 'Enable/Disable', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
					'type'        => 'checkbox',
					'label'       => __( 'Enable “Cofidis Pay” (using IfthenPay)', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
					'description' => __( 'Requires a separate contract with Cofidis.', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
					'default'     => 'no',
				),
				'cofidispaykey' => array(
					'title'             => __( 'Cofidis Pay Key', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
					'type'              => 'text',
					'description'       => __( 'Cofidis Pay Key provided by IfthenPay when signing the contract.', 'multibanco-ifthen-software-gateway-for-woocommerce' ) . ( apply_filters( 'cofidispay_ifthen_sandbox', false ) ? '<br><span style="color: red;">Sandbox</span>' : '' ),
					'default'           => '',
					'css'               => 'width: 130px;',
					'placeholder'       => 'XXX-000000',
					'custom_attributes' => array(
						'maxlength' => 10,
						'size'      => 14,
					),
				),
			);
			// if ( strlen( trim( $this->get_option( 'cofidispaykey' ) ) ) == 10 && trim( $this->secret_key ) != '' ) {
				$this->form_fields = array_merge(
					$this->form_fields,
					array(
						'secret_key'         => array(
							'title'       => __( 'Anti-phishing key', 'multibanco-ifthen-software-gateway-for-woocommerce' ) . ' (Cofidis Pay)',
							'type'        => 'hidden',
							'description' => '<strong id="woocommerce_' . $this->id . '_secret_key_label">' . $this->secret_key . '</strong><br/>' . __( 'To ensure callback security, generated by the system and which must be provided to IfthenPay when asking for the callback activation.', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
							'default'     => $this->secret_key,
						),
						'title'         => array(
							'title'       => __( 'Title', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
							'type'        => 'text',
							'description' => __( 'This controls the title which the user sees during checkout.', 'multibanco-ifthen-software-gateway-for-woocommerce' )
											. ( WC_IfthenPay_Webdados()->wpml_active ? '<br/>' . WC_IfthenPay_Webdados()->wpml_translation_info : '' ),
							'default'     => 'Cofidis Pay - Up to 12x interest-free',
							//PT: Cofidis Pay - Até 12x sem juros
						),
						'description'   => array(
							'title'       => __( 'Description', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
							'type'        => 'textarea',
							'description' => __( 'This controls the description which the user sees during checkout.', 'multibanco-ifthen-software-gateway-for-woocommerce' )
											. ( WC_IfthenPay_Webdados()->wpml_active ? '<br/>' . WC_IfthenPay_Webdados()->wpml_translation_info : '' ),
							'default'     => $this->get_method_description(),
						),
						'only_portugal' => array(
							'title'   => __( 'Only for Portuguese customers?', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
							'type'    => 'checkbox',
							'label'   => __( 'Enable only for customers whose billing or shipping address is in Portugal', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
							'default' => 'no',
						),
						'only_above'    => array(
							'title'       => __( 'Only for orders from', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
							'type'        => 'number',
							'description' =>
								__( 'Enable only for orders with a value from x &euro;. Leave blank (or zero) to allow for any order value.', 'multibanco-ifthen-software-gateway-for-woocommerce' )
								.
								' <br/> '
								.
								__( 'This was set automatically based on your Cofidis contract, but you can adjust if the contract was changed or you want to further limit the values interval.', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
							'default'     => '',
						),
						'only_bellow'   => array(
							'title'       => __( 'Only for orders up to', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
							'type'        => 'number',
							'description' =>
								__( 'Enable only for orders with a value up to x &euro;. Leave blank (or zero) to allow for any order value.', 'multibanco-ifthen-software-gateway-for-woocommerce' )
								.
								' <br/> '
								.
								__( 'This was set automatically based on your Cofidis contract, but you can adjust if the contract was changed or you want to further limit the values interval.', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
							'default'     => '',
						),
					)
				);
				$this->form_fields = array_merge(
					$this->form_fields,
					array(
						'debug'       => array(
							'title'       => __( 'Debug Log', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
							'type'        => 'checkbox',
							'label'       => __( 'Enable logging', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
							'default'     => 'yes',
							'description' => sprintf(
								__( 'Log plugin events in %s', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
								( ( defined( 'WC_LOG_HANDLER' ) && 'WC_Log_Handler_DB' === WC_LOG_HANDLER ) || version_compare( WC_VERSION, '8.6', '>=' ) )
								?
								'<a href="admin.php?page=wc-status&tab=logs&source=' . esc_attr( $this->id ) . '" target="_blank">' . __( 'WooCommerce &gt; Status &gt; Logs', 'multibanco-ifthen-software-gateway-for-woocommerce' ) . '</a>'
								:
								'<code>' . wc_get_log_file_path( $this->id ) . '</code>'
							),
						),
						'debug_email' => array(
							'title'       => __( 'Debug to email', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
							'type'        => 'email',
							'label'       => __( 'Enable email logging', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
							'default'     => '',
							'description' => __( 'Send main plugin events to this email address.', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
						),
					)
				);
			// }
			// PRO fake fields
			$pro_fake_fields = array(
				// Product banner
				'_pro_show_product_banner' => array(
					'type'     => 'checkbox',
					'title'    => __( 'Show product banner', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
					'label'    => __( 'Show Cofidis payment information banner below the price on product pages, with the number of instalments and price to pay per month', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
					'disabled' => true,
				),
			);
			foreach( $pro_fake_fields as $key => $temp ) {
				$pro_fake_fields[$key]['title'] = '⭐️ ' . $pro_fake_fields[$key]['title'];
				if ( isset( $pro_fake_fields[$key]['description'] ) ) {
					$pro_fake_fields[$key]['description'] .= '<br/>';
				} else {
					$pro_fake_fields[$key]['description'] = '';
				}
				$pro_fake_fields[$key]['description'] .= sprintf(
					__( 'Available on the %sPRO Add-on%s', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
					'<a href="https://ptwooplugins.com/product/multibanco-mbway-credit-card-payshop-ifthenpay-woocommerce-pro-add-on/'.esc_attr( WC_IfthenPay_Webdados()->out_link_utm ).'" target="_blank">',
					'</a>'
				);
			}
			$this->form_fields = array_merge(
				$this->form_fields,
				$pro_fake_fields
			);
			$this->form_fields = array_merge(
				$this->form_fields,
				array(
					'settings_saved' => array(
						'title'   => '',
						'type'    => 'hidden',
						'default' => 0,
					),
				)
			);

			// Allow other plugins to add settings fields
			$this->form_fields = array_merge( $this->form_fields, apply_filters( 'multibanco_ifthen_cofidispay_settings_fields', array() ) );
			// And to manipulate them
			$this->form_fields = apply_filters( 'multibanco_ifthen_cofidispay_settings_fields_all', $this->form_fields );

		}
		public function admin_options() {
			$title = esc_html( $this->get_method_title() );
			?>
			<div id="wc_ifthen">
				<?php
				if ( ! apply_filters( 'multibanco_ifthen_hide_settings_right_bar', false ) ) {
					WC_IfthenPay_Webdados()->admin_pro_banner();
				}
				?>
				<?php
				if ( ! apply_filters( 'multibanco_ifthen_hide_settings_right_bar', false ) ) {
					WC_IfthenPay_Webdados()->admin_right_bar();
				}
				?>
				<div id="wc_ifthen_settings">
					<h2>
						<img src="<?php echo esc_url( WC_IfthenPay_Webdados()->cofidispay_banner ); ?>" alt="<?php echo esc_attr( $title ); ?>" width="200" height="48"/>
						<br/>
						<?php echo $title; ?>
						<small>v.<?php echo $this->version; ?></small>
						<?php
						if ( function_exists( 'wc_back_link' ) ) {
							echo wc_back_link( __( 'Return to payments', 'woocommerce' ), admin_url( 'admin.php?page=wc-settings&tab=checkout' ) );}
						?>
					</h2>
					<?php echo wp_kses_post( wpautop( $this->get_method_description() ) ); ?>
					<p><strong><?php _e( 'In order to use this plugin you <u>must</u>:', 'multibanco-ifthen-software-gateway-for-woocommerce' ); ?></strong></p>
					<ul class="wc_ifthen_list">
						<li><?php printf( __( 'Set WooCommerce currency to <strong>Euros (&euro;)</strong> %1$s', 'multibanco-ifthen-software-gateway-for-woocommerce' ), '<a href="admin.php?page=wc-settings&amp;tab=general">&gt;&gt;</a>.' ); ?></li>
						<li><?php printf( __( 'Sign a contract with %1$s. To know more about this service, please go to %2$s.', 'multibanco-ifthen-software-gateway-for-woocommerce' ), '<strong><a href="https://ifthenpay.com/' . esc_attr( WC_IfthenPay_Webdados()->out_link_utm ) . '" target="_blank">IfthenPay</a></strong>', '<a href="https://ifthenpay.com/' . esc_attr( WC_IfthenPay_Webdados()->out_link_utm ) . '" target="_blank">https://ifthenpay.com</a>' ); ?></li>
						<!--<li><?php printf( __( 'Sign a contract with %1$s. To know more about this service, please go to %2$s.', 'multibanco-ifthen-software-gateway-for-woocommerce' ), '<strong><a href="https://www.cofidis.pt/' . esc_attr( WC_IfthenPay_Webdados()->out_link_utm ) . '" target="_blank">Cofidis</a></strong>', '<a href="https://www.cofidis.pt/cofidispay/ecommerce' . esc_attr( WC_IfthenPay_Webdados()->out_link_utm ) . '" target="_blank">https://www.cofidis.pt/cofidispay/ecommerce</a>' ); ?></li>-->
						<li><?php printf( __( 'Sign a contract with %s.', 'multibanco-ifthen-software-gateway-for-woocommerce' ), '<strong><a href="https://www.cofidis.pt/' . esc_attr( WC_IfthenPay_Webdados()->out_link_utm ) . '" target="_blank">Cofidis</a></strong>' ); ?></li>
						<li><?php _e( 'Fill out all the details (Cofidis Pay Key) provided by <strong>IfthenPay</strong> in the fields below.', 'multibanco-ifthen-software-gateway-for-woocommerce' ); ?>
						<li>
						<?php
						printf(
							__( 'Never use the same %1$s on more than one website or any other system, online or offline. Ask %2$s for new ones for each single platform.', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
							__( 'Cofidis Pay Key', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
							'<a href="https://ifthenpay.com/' . esc_attr( WC_IfthenPay_Webdados()->out_link_utm ) . '" target="_blank">IfthenPay</a>'
						);
						?>
						</li>
						<li class="mb_hide_extra_fields"><?php printf( __( 'Ask IfthenPay to activate “Cofidis Pay Callback” on your account using this exact URL: %1$s and this Anti-phishing key: %2$s', 'multibanco-ifthen-software-gateway-for-woocommerce' ), '<br/><code><strong>' . WC_IfthenPay_Webdados()->cofidispay_notify_url . '</strong></code><br/>', '<br/><code><strong>' . $this->secret_key . '</strong></code>' ); ?></li>
					</ul>
					<?php
					if (
						strlen( trim( $this->cofidispaykey ) ) == 10
						&&
						trim( $this->secret_key ) != ''
					) {
						if ( $callback_email_sent = get_option( $this->id . '_callback_email_sent' ) ) { // No notice for older versions
							if ( $callback_email_sent == 'no' ) {
								if ( ! isset( $_GET['callback_warning'] ) ) {
									?>
									<div id="message" class="error">
										<p><strong><?php _e( 'You haven’t yet asked IfthenPay for the “Callback” activation. The orders will NOT be automatically updated upon payment.', 'multibanco-ifthen-software-gateway-for-woocommerce' ); ?></strong></p>
									</div>
									<?php
								}
							}
						}
						?>
						<p id="wc_ifthen_callback_open_p"><a href="#" id="wc_ifthen_callback_open" class="button button-small"><?php _e( 'Click here to ask IfthenPay to activate the “Callback”', 'multibanco-ifthen-software-gateway-for-woocommerce' ); ?></a></p>
						<div id="wc_ifthen_callback_div">
							<p><?php _e( 'This will submit a request to IfthenPay, asking them to activate the “Callback” on your account. The following details will be sent to IfthenPay:', 'multibanco-ifthen-software-gateway-for-woocommerce' ); ?></p>
							<table class="form-table">
								<tr valign="top">
									<th scope="row" class="titledesc"><?php _e( 'Email', 'multibanco-ifthen-software-gateway-for-woocommerce' ); ?></th>
									<td class="forminp">
										<?php echo get_option( 'admin_email' ); ?>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row" class="titledesc">Cofidis Pay Key</th>
									<td class="forminp">
										<?php echo $this->cofidispaykey; ?>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row" class="titledesc"><?php _e( 'Anti-phishing key', 'multibanco-ifthen-software-gateway-for-woocommerce' ) . ' (Cofidis Pay)'; ?></th>
									<td class="forminp">
										<?php echo $this->secret_key; ?>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row" class="titledesc"><?php _e( 'Callback URL', 'multibanco-ifthen-software-gateway-for-woocommerce' ); ?></th>
									<td class="forminp">
										<?php echo WC_IfthenPay_Webdados()->cofidispay_notify_url; ?>
									</td>
								</tr>
							</table>
							<p style="text-align: center;">
								<strong><?php _e( 'Attention: if you ever change from HTTP to HTTPS or vice versa, or the permalinks structure,<br/>you may have to ask IfthenPay to update the callback URL.', 'multibanco-ifthen-software-gateway-for-woocommerce' ); ?></strong>
							</p>
							<p style="text-align: center; margin-bottom: 0px;">
								<input type="hidden" id="wc_ifthen_callback_send" name="wc_ifthen_callback_send" value="0"/>
								<input type="hidden" id="wc_ifthen_callback_bo_key" name="wc_ifthen_callback_bo_key" value=""/>
								<button id="wc_ifthen_callback_submit_webservice" class="button-primary" type="button"><?php _e( 'Ask for Callback activation', 'multibanco-ifthen-software-gateway-for-woocommerce' ); ?> - <?php _e( 'Via webservice (recommended)', '' ); ?></button>
								<br/><br/>
								<button id="wc_ifthen_callback_submit" class="button" type="button"><?php _e( 'Ask for Callback activation', 'multibanco-ifthen-software-gateway-for-woocommerce' ); ?> - <?php _e( 'Via email (old method)', '' ); ?></button>
								<input id="wc_ifthen_callback_cancel" class="button" type="button" value="<?php _e( 'Cancel', 'multibanco-ifthen-software-gateway-for-woocommerce' ); ?>"/>
								<input type="hidden" name="save" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>"/> <!-- Force action woocommerce_update_options_payment_gateways_ to run, from WooCommerce 3.5.5 -->
							</p>
						</div>
						<?php
					} else {
						if ( $this->settings_saved == 1 ) {
							?>
							<div id="message" class="error">
								<p><strong><?php _e( 'Invalid Cofidis Pay Key (exactly 10 characters).', 'multibanco-ifthen-software-gateway-for-woocommerce' ); ?></strong></p>
							</div>
							<?php
						} else {
							?>
							<div id="message" class="error">
								<p><strong><?php _e( 'Set the Cofidis Pay Key and Save changes to set other plugin options.', 'multibanco-ifthen-software-gateway-for-woocommerce' ); ?></strong></p>
							</div>
							<?php
						}
					}
					?>
					<hr/>
					<?php
					if ( trim( get_woocommerce_currency() ) === 'EUR' || apply_filters( 'ifthen_allow_settings_woocommerce_not_euro', false ) ) {
						?>
						<table class="form-table">
							<?php $this->generate_settings_html(); ?>
						</table>
						<?php
					} else {
						?>
						<p><strong><?php _e( 'ERROR!', 'multibanco-ifthen-software-gateway-for-woocommerce' ); ?> <?php printf( __( 'Set WooCommerce currency to <strong>Euros (&euro;)</strong> %1$s', 'multibanco-ifthen-software-gateway-for-woocommerce' ), '<a href="admin.php?page=wc-settings&amp;tab=general">' . __( 'here', 'multibanco-ifthen-software-gateway-for-woocommerce' ) . '</a>.' ); ?></strong></p>
						<style type="text/css">
							#mainform .submit,
							.wp-core-ui .button-primary.woocommerce-save-button {
								display: none;
							}
						</style>
						<?php
					}
					?>
				</div>
			</div>
			<div class="clear"></div>
			<?php
		}

		/* Intercept process_admin_options and set min and max values for this gateway from the IfthenPay limits API endpoint */
		public function process_admin_options() {
			if ( isset( $_POST[ 'woocommerce_' . $this->id . '_cofidispaykey' ] ) ) {
				$new_key = trim( $_POST[ 'woocommerce_' . $this->id . '_cofidispaykey' ] );
				if ( strlen( $new_key ) == 10 && trim( $_POST[ 'woocommerce_' . $this->id . '_cofidispaykey' ] ) != trim( $this->get_option( 'cofidispaykey' ) ) ) {
					$url = $this->limits_api_url . $new_key;
					$response = wp_remote_get( $url );
					if ( ! is_wp_error( $response ) ) {
						if ( isset( $response['response']['code'] ) && intval( $response['response']['code'] ) == 200 && isset( $response['body'] ) && trim( $response['body'] ) != '' ) {
							if ( $body = json_decode( trim( $response['body'] ) ) ) {
								if ( isset( $body->message ) && $body->message === 'success' && isset( $body->limits->maxAmount ) && intval( $body->limits->maxAmount ) > 0 && isset( $body->limits->minAmount ) && intval( $body->limits->minAmount ) > 0 ) {
									// Override min and max values
									$_POST[ 'woocommerce_' . $this->id . '_only_above' ]  = intval( $body->limits->minAmount );
									$_POST[ 'woocommerce_' . $this->id . '_only_bellow' ] = intval( $body->limits->maxAmount );
								}
							}
						}
					}
				}
			}
			return parent::process_admin_options();
		}

		public function send_callback_email() {
			if ( isset( $_POST['wc_ifthen_callback_send'] ) && intval( $_POST['wc_ifthen_callback_send'] ) == 2 && trim( $_POST['wc_ifthen_callback_bo_key'] ) != '' ) {
				// Webservice
				$result = WC_IfthenPay_Webdados()->callback_webservice( trim( $_POST['wc_ifthen_callback_bo_key'] ), 'COFIDIS', $this->cofidispaykey, $this->secret_key, WC_IfthenPay_Webdados()->cofidispay_notify_url );
				if ( $result['success'] ) {
					update_option( $this->id . '_callback_email_sent', 'yes' );
					WC_Admin_Settings::add_message( __( 'The “Callback” activation request has been submited to IfthenPay via webservice and is now active.', 'multibanco-ifthen-software-gateway-for-woocommerce' ) );
				} else {
					WC_Admin_Settings::add_error(
						__( 'The “Callback” activation request via webservice has failed.', 'multibanco-ifthen-software-gateway-for-woocommerce' )
						. ' - ' .
						$result['message']
					);
				}
			} elseif ( isset( $_POST['wc_ifthen_callback_send'] ) && intval( $_POST['wc_ifthen_callback_send'] ) == 1 ) {
				// Email
				$to      = WC_IfthenPay_Webdados()->callback_email;
				$cc      = get_option( 'admin_email' );
				$subject = 'Activação de Callback Cofidis Pay (Key: ' . $this->cofidispaykey . ')';
				$message = 'Por favor activar Callback Cofidis Pay com os seguintes dados:

Cofidis Pay Key:
' . $this->cofidispaykey . '

Chave anti-phishing (Cofidis Pay):
' . $this->secret_key . '

URL:
' . WC_IfthenPay_Webdados()->cofidispay_notify_url . '

Email enviado automaticamente do plugin WordPress “Multibanco, MB WAY, Credit card, Payshop and Cofidis Pay (IfthenPay) for WooCommerce” para ' . $to . ' com CC para ' . $cc;
				$headers = array(
					'From: ' . get_option( 'admin_email' ) . ' <' . get_option( 'admin_email' ) . '>',
					'Cc: ' . $cc,
				);
				if ( wp_mail( $to, $subject, $message, $headers ) ) {
					update_option( $this->id . '_callback_email_sent', 'yes' );
					WC_Admin_Settings::add_message( __( 'The “Callback” activation request has been submited to IfthenPay. Wait for their feedback.', 'multibanco-ifthen-software-gateway-for-woocommerce' ) );
				} else {
					WC_Admin_Settings::add_error( __( 'The “Callback” activation request could not be sent. Check if your WordPress install can send emails.', 'multibanco-ifthen-software-gateway-for-woocommerce' ) );
				}
			}
		}

		/**
		 * Icon HTML
		 */
		public function get_icon() {
			$alt       = ( WC_IfthenPay_Webdados()->wpml_active ? icl_t( $this->id, $this->id . '_title', $this->title ) : $this->title );
			$icon_html = '<img src="' . esc_attr( WC_IfthenPay_Webdados()->cofidispay_icon ) . '" alt="' . esc_attr( $alt ) . '" width="28" height="24"/>';
			return apply_filters( 'woocommerce_gateway_icon', $icon_html, $this->id );
		}

		/**
		 * Thank you page
		 */
		function thankyou( $order_id ) {
			if ( is_object( $order_id ) ) {
				$order = $order_id;
			} else {
				$order = wc_get_order( $order_id );
			}
			if ( $this->id === $order->get_payment_method() ) {
				if ( WC_IfthenPay_Webdados()->order_needs_payment( $order ) ) {

					echo $this->thankyou_instructions_table_html( $order->get_id(), round( WC_IfthenPay_Webdados()->get_order_total_to_pay( $order ), 2 ) );
					if ( is_wc_endpoint_url( 'order-received' ) ) {
						do_action( 'cofidispay_ifthen_after_thankyou_instructions_table', $order );
					}

					if ( is_wc_endpoint_url( 'order-received' ) ) {
						if ( apply_filters( 'cofidispay_ifthen_enable_check_order_status_thankyou', true, $order->get_id() ) ) { // return false to cofidispay_ifthen_enable_check_order_status_thankyou in order to stop the ajax checking
							// Check order status
							?>
							<input type="hidden" id="cofidispay-order-id" value="<?php echo intval( $order->get_id() ); ?>"/>
							<input type="hidden" id="cofidispay-order-key" value="<?php echo esc_attr( $order->get_order_key() ); ?>"/>
							<?php
							wp_enqueue_script( 'cofidispay-ifthenpay', plugins_url( 'assets/cofidispay.js', __FILE__ ), array( 'jquery' ), $this->version . ( WP_DEBUG ? '.' . wp_rand( 0, 99999 ) : '' ), true );
							wp_localize_script(
								'cofidispay-ifthenpay',
								'cofidispay_ifthenpay',
								array(
									'interval'           => apply_filters( 'cofidispay_ifthen_check_order_status_thankyou_interval', 10 ),
									'cofidispay_minutes' => 5,
								)
							);
						}
					}

				} else {
					// Processing
					if ( ( $order->has_status( 'processing' ) || $order->has_status( 'completed' ) ) && ! is_wc_endpoint_url( 'view-order' ) ) {
						echo $this->email_instructions_payment_received( $order->get_id() );
					}
				}
			}
		}
		function thankyou_instructions_table_html_css() {
			ob_start();
			?>
			<style type="text/css">
				table.<?php echo $this->id; ?>_table {
					width: auto !important;
					margin: auto;
					margin-top: 2em;
					margin-bottom: 2em;
					max-width: 325px !important;
				}
				table.<?php echo $this->id; ?>_table td,
				table.<?php echo $this->id; ?>_table th {
					background-color: #FFFFFF;
					color: #000000;
					padding: 10px;
					vertical-align: middle;
					white-space: nowrap;
				}
				table.<?php echo $this->id; ?>_table td.mb_value {
					text-align: right;
				}
				@media only screen and (max-width: 450px)  {
					table.<?php echo $this->id; ?>_table td,
					table.<?php echo $this->id; ?>_table th {
						white-space: normal;
					}
				}
				table.<?php echo $this->id; ?>_table th {
					text-align: center;
					font-weight: bold;
				}
				table.<?php echo $this->id; ?>_table th img {
					margin: auto;
					margin-top: 10px;
					max-height: 48px;
				}
				table.<?php echo $this->id; ?>_table td.extra_instructions {
					font-size: small;
					white-space: normal;
				}
			</style>
			<?php
			return ob_get_clean();
		}
		function thankyou_instructions_table_html( $order_id, $order_total ) {
			$alt                      = ( WC_IfthenPay_Webdados()->wpml_active ? icl_t( $this->id, $this->id . '_title', $this->title ) : $this->title );
			$cofidispay_order_details = WC_IfthenPay_Webdados()->get_cofidispay_order_details( $order_id );
			$order                    = wc_get_order( $order_id );
			ob_start();
			echo $this->thankyou_instructions_table_html_css();
			?>
			<table class="<?php echo $this->id; ?>_table" cellpadding="0" cellspacing="0">
				<tr>
					<th colspan="2">
						<?php _e( 'Payment information', 'multibanco-ifthen-software-gateway-for-woocommerce' ); ?>
						<br/>
						<img src="<?php echo esc_url( WC_IfthenPay_Webdados()->cofidispay_banner ); ?>" alt="<?php echo esc_attr( $alt ); ?>" title="<?php echo esc_attr( $alt ); ?>"/>
					</th>
				</tr>
				<tr>
					<td><?php _e( 'Value', 'multibanco-ifthen-software-gateway-for-woocommerce' ); ?>:</td>
					<td class="mb_value"><?php echo wc_price( $cofidispay_order_details['val'], array( 'currency' => 'EUR' ) ); ?></td>
				</tr>
				<tr>
					<td colspan="2" class="extra_instructions">
						<?php _e( 'Waiting for confirmation from Cofidis Pay', 'multibanco-ifthen-software-gateway-for-woocommerce' ); ?>
					</td>
				</tr>
			</table>
			<?php
			return apply_filters( 'cofidispay_ifthen_thankyou_instructions_table_html', ob_get_clean(), round( $order_total, 2 ), $order_id );
		}

		function order_details_after_order_table( $order ) {
			if ( is_wc_endpoint_url( 'view-order' ) ) {
				$this->thankyou( $order );
			}
		}

		/**
		 * Email instructions
		 */
		function email_instructions_1( $order, $sent_to_admin, $plain_text, $email = null ) {
			// "Hyyan WooCommerce Polylang" Integration removes "email_instructions" so we use "email_instructions_1"
			$this->email_instructions( $order, $sent_to_admin, $plain_text, $email );
		}
		function email_instructions( $order, $sent_to_admin, $plain_text, $email = null ) {
			// Avoid duplicate email instructions on some edge cases
			$send = false;
			if ( ( $sent_to_admin ) ) {
				// if ( ( $sent_to_admin ) && ( !WC_IfthenPay_Webdados()->instructions_sent_to_admin ) ) { //Fixed by checking class instances
				// WC_IfthenPay_Webdados()->instructions_sent_to_admin = true;
				$send = true;
			} else {
				if ( ( ! $sent_to_admin ) ) {
					// if ( ( !$sent_to_admin ) && ( !WC_IfthenPay_Webdados()->instructions_sent_to_client ) ) { //Fixed by checking class instances
					// WC_IfthenPay_Webdados()->instructions_sent_to_client = true;
					$send = true;
				}
			}
			// Apply filter
			$send = apply_filters( 'cofidispay_ifthen_send_email_instructions', $send, $order, $sent_to_admin, $plain_text, $email );
			// Send
			if ( $send ) {
				// Go
				if ( $this->id === $order->get_payment_method() ) {
					$show = false;
					if ( ! $sent_to_admin ) {
						$show = true;
					} else {
						if ( $this->send_to_admin ) {
							$show = true;
						}
					}
					if ( $show ) {
						// Force correct language
						WC_IfthenPay_Webdados()->maybe_change_locale( $order );
						// On Hold or pending
						if ( WC_IfthenPay_Webdados()->order_needs_payment( $order ) ) {
							// Show instructions - We should never get here as we're not allowing to change the status to On hold.
							if ( apply_filters( 'cofidispay_ifthen_email_instructions_pending_send', true, $order->get_id() ) ) {
								echo $this->email_instructions_table_html( $order->get_id(), round( WC_IfthenPay_Webdados()->get_order_total_to_pay( $order ), 2 ) );
							}
						} else {
							// Processing
							if ( $order->has_status( 'processing' ) || $order->has_status( 'completed' ) ) {
								if ( apply_filters( 'cofidispay_ifthen_email_instructions_payment_received_send', true, $order->get_id() ) ) {
									echo $this->email_instructions_payment_received( $order->get_id() );
								}
							}
						}
					}
				}
			}
		}
		function email_instructions_table_html( $order_id, $order_total ) {
			$alt                      = ( WC_IfthenPay_Webdados()->wpml_active ? icl_t( $this->id, $this->id . '_title', $this->title ) : $this->title );
			// We actually do not use $ent, $ref or $order_total - We'll just get the details
			$cofidispay_order_details = WC_IfthenPay_Webdados()->get_cofidispay_order_details( $order_id );
			$order                    = wc_get_order( $order_id );
			ob_start();
			?>
			<table cellpadding="10" cellspacing="0" align="center" border="0" style="margin: auto; margin-top: 2em; margin-bottom: 2em; border-collapse: collapse; border: 1px solid #1465AA; border-radius: 4px !important; background-color: #FFFFFF;">
				<tr>
					<td style="border: 1px solid #1465AA; border-top-right-radius: 4px !important; border-top-left-radius: 4px !important; text-align: center; color: #000000; font-weight: bold;" colspan="2">
						<?php _e( 'Payment instructions', 'multibanco-ifthen-software-gateway-for-woocommerce' ); ?>
						<br/>
						<img src="<?php echo esc_url( WC_IfthenPay_Webdados()->cofidispay_banner_email ); ?>" alt="<?php echo esc_attr( $alt ); ?>" title="<?php echo esc_attr( $alt ); ?>" style="margin-top: 10px; max-height: 48px"/>
					</td>
				</tr>
				<tr>
					<td style="border-top: 1px solid #1465AA; color: #000000;"><?php _e( 'Information', 'multibanco-ifthen-software-gateway-for-woocommerce' ); ?>:</td>
					<td style="border-top: 1px solid #1465AA; color: #000000; white-space: nowrap; text-align: right;"><?php echo apply_filters( 'cofidispay_ifthen_webservice_desc', get_bloginfo( 'name' ) . ' #' . $order->get_order_number(), $order_id ); ?></td>
				</tr>
				<tr>
					<td style="border-top: 1px solid #1465AA; color: #000000;"><?php _e( 'Value', 'multibanco-ifthen-software-gateway-for-woocommerce' ); ?>:</td>
					<td style="border-top: 1px solid #1465AA; color: #000000; white-space: nowrap; text-align: right;"><?php echo wc_price( $cofidispay_order_details['val'], array( 'currency' => 'EUR' ) ); ?></td>
				</tr>
				<tr>
					<td style="font-size: x-small; border: 1px solid #1465AA; border-bottom-right-radius: 4px !important; border-bottom-left-radius: 4px !important; color: #000000; text-align: center;" colspan="2">
						<?php _e( 'Waiting for confirmation from Cofidis Pay', 'multibanco-ifthen-software-gateway-for-woocommerce' ); ?>
					</td>
				</tr>
			</table>
			<?php
			return apply_filters( 'cofidispay_ifthen_email_instructions_table_html', ob_get_clean(), round( $order_total, 2 ), $order_id );
		}
		function email_instructions_payment_received( $order_id ) {
			$alt = ( WC_IfthenPay_Webdados()->wpml_active ? icl_t( $this->id, $this->id . '_title', $this->title ) : $this->title );
			ob_start();
			?>
			<p style="text-align: center; margin: auto; margin-top: 2em; margin-bottom: 2em;">
				<img src="<?php echo esc_url( WC_IfthenPay_Webdados()->cofidispay_banner_email ); ?>" alt="<?php echo esc_attr( $alt ); ?>" title="<?php echo esc_attr( $alt ); ?>" style="margin: auto; margin-top: 10px; max-height: 48px;"/>
				<br/>
				<strong><?php _e( 'Cofidis Pay payment approved.', 'multibanco-ifthen-software-gateway-for-woocommerce' ); ?></strong>
				<br/>
				<?php _e( 'We will now process your order.', 'multibanco-ifthen-software-gateway-for-woocommerce' ); ?>
			</p>
			<?php
			return apply_filters( 'cofidispay_ifthen_email_instructions_payment_received', ob_get_clean(), $order_id );
		}

		/**
		 * API Init Payment
		 */
		function api_init_payment( $order_id ) {
			$id                = $order_id; // We could randomize this...
			$order             = wc_get_order( $order_id );
			$valor             = (string) round( floatval( WC_IfthenPay_Webdados()->get_order_total_to_pay( $order ) ), 2 );
			$cofidispaykey     = apply_filters( 'multibanco_ifthen_base_cofidispaykey', $this->cofidispaykey, $order );
			$wd_secret         = substr( strrev( md5( time() ) ), 0, 10 ); // Set a secret on our end for extra validation
			$id_for_backoffice = apply_filters( 'ifthen_webservice_send_order_number_instead_id', false ) ? $order->get_order_number() : $order->get_id();
			$desc              = trim( get_bloginfo( 'name' ) );
			$desc              = substr( $desc, 0, COFIDISPAY_IFTHEN_DESC_LEN - strlen( ' #' . $order->get_order_number() ) );
			$desc             .= ' #' . $order->get_order_number();
			$url               = $this->api_url . $cofidispaykey;
			$return_url        = WC_IfthenPay_Webdados()->cofidispay_return_url;
			$return_url        = add_query_arg( 'id', $id_for_backoffice, $return_url );
			$return_url        = add_query_arg( 'wd_secret', $wd_secret, $return_url );
			$return_url        = add_query_arg( 'amount', $valor, $return_url );
			$args              = array(
				'method'   => 'POST',
				'timeout'  => apply_filters( 'cofidispay_ifthen_api_timeout', 30 ),
				'blocking' => true,
				'body'     => array(
					'orderId'         => (string) $id_for_backoffice,
					'amount'          => $valor,
					'description'     => WC_IfthenPay_Webdados()->mb_webservice_filter_descricao( apply_filters( 'cofidispay_ifthen_webservice_desc', $desc, $order->get_id() ) ),
					'returnUrl'       => $return_url,
					'customerName'    => trim( $order->get_formatted_billing_full_name() ),
					'customerVat'     => apply_filters( 'cofidispay_ifthen_customer_vat', '', $order ), // Add to PRO add-on
					'customerEmail'   => trim( $order->get_billing_email() ),
					'customerPhone'   => trim( $order->get_billing_phone() ),
					'billingAddress'  => trim( trim( $order->get_billing_address_1() ) . ' ' . trim( $order->get_billing_address_2() ) ),
					'billingZipCode'  => trim( $order->get_billing_postcode() ),
					'billingCity'     => trim( $order->get_billing_city() ),
					'deliveryAddress' => trim( trim( $order->get_shipping_address_1() ) . ' ' . trim( $order->get_shipping_address_2() ) ),
					'deliveryZipCode' => trim( $order->get_shipping_postcode() ),
					'deliveryCity'    => trim( $order->get_shipping_city() ),
				),
			);
			$args['body']  = json_encode( $args['body'] ); // Json not post variables
			$response      = wp_remote_post( $url, $args );
			if ( is_wp_error( $response ) ) {
				$debug_msg       = '- Error contacting the IfthenPay servers - Order ' . $order->get_id() . ' - ' . $response->get_error_message();
				$debug_msg_email = $debug_msg . ' - Args: ' . wp_json_encode( $args ) . ' - Response: ' . wp_json_encode( $response );
				$this->debug_log( $debug_msg, 'error', true, $debug_msg_email );
				return false;
			} else {
				if ( isset( $response['response']['code'] ) && intval( $response['response']['code'] ) == 200 && isset( $response['body'] ) && trim( $response['body'] ) != '' ) {
					if ( $body = json_decode( trim( $response['body'] ) ) ) {
						if ( intval( $body->status ) == 0 ) {
							WC_IfthenPay_Webdados()->multibanco_set_order_cofidispay_details(
								$order->get_id(),
								array(
									'cofidispaykey' => $cofidispaykey,
									'request_id'    => $body->requestId,
									'id'            => $id_for_backoffice,
									'val'           => $valor,
									'payment_url'   => $body->paymentUrl,
									'wd_secret'     => $wd_secret,
								)
							);
							$this->debug_log( '- Cofidis Pay payment request created on IfthenPay servers - Redirecting to payment gateway - Order ' . $order->get_id() . ' - requestId: ' . $body->requestId );
							do_action( 'cofidispay_ifthen_created_reference', $body->requestId, $order->get_id() );
							return $body->paymentUrl;
						} else {
							$debug_msg = '- Error contacting the IfthenPay servers - Order ' . $order->get_id() . ' - Error code and message: ' . $body->status . ' / ' . $body->Message;
							$this->debug_log( $debug_msg, 'error', true, $debug_msg );
							return false;
						}
					} else {
						$debug_msg = '- Error contacting the IfthenPay servers - Order ' . $order->get_id() . ' - Can not json_decode body';
						$this->debug_log( $debug_msg, 'error', true, $debug_msg );
						return false;
					}
				} else {
					$debug_msg       = '- Error contacting the IfthenPay servers - Order ' . $order->get_id() . ' - Incorrect response code: ' . $response['response']['code'];
					$debug_msg_email = $debug_msg . ' - Args: ' . wp_json_encode( $args ) . ' - Response: ' . wp_json_encode( $response );
					$this->debug_log( $debug_msg, 'error', true, $debug_msg_email );
					return false;
				}
			}
			return false;
		}

		/**
		 * Process it
		 */
		function process_payment( $order_id ) {
			// Webservice
			$order = wc_get_order( $order_id );
			do_action( 'cofidispay_ifthen_before_process_payment', $order );
			if ( $order->get_total() > 0 ) {
				if ( $redirect_url = $this->api_init_payment( $order->get_id() ) ) {
					// Mark pending
					$order->update_status( 'pending', __( 'Awaiting Cofidis Pay payment.', 'multibanco-ifthen-software-gateway-for-woocommerce' ) );
				} else {
					throw new Exception( __( 'Error contacting IfthenPay servers to create Cofidis Pay Payment', 'multibanco-ifthen-software-gateway-for-woocommerce' ) );
				}
			} else {
				// Value = 0
				$order->payment_complete();
			}
			// Remove cart - not now, only after paid
			// if ( isset( WC()->cart ) ) {
			// WC()->cart->empty_cart();
			// }
			// Empty awaiting payment session - not now, only after paid
			// unset( WC()->session->order_awaiting_payment );
			// Return payment url redirect
			return array(
				'result'   => 'success',
				'redirect' => $redirect_url, // Payment gateway URL
			);
		}


		/**
		 * Disable if key not correctly set
		 */
		function disable_if_settings_missing( $available_gateways ) {
			if (
				strlen( trim( $this->cofidispaykey ) ) != 10
				||
				trim( $this->enabled ) != 'yes'
			) {
				unset( $available_gateways[ $this->id ] );
			}
			return $available_gateways;
		}

		/**
		 * Just for €
		 */
		function disable_if_currency_not_euro( $available_gateways ) {
			return WC_IfthenPay_Webdados()->disable_if_currency_not_euro( $available_gateways, $this->id );
		}

		/**
		 * Just for Portugal
		 */
		function disable_unless_portugal( $available_gateways ) {
			return WC_IfthenPay_Webdados()->disable_unless_portugal( $available_gateways, $this->id );
		}

		/**
		 * Just above/below certain amounts
		 */
		function disable_only_above_or_below( $available_gateways ) {
			return WC_IfthenPay_Webdados()->disable_only_above_or_below( $available_gateways, $this->id, WC_IfthenPay_Webdados()->cofidispay_min_value, WC_IfthenPay_Webdados()->cofidispay_max_value );
		}

		/* Payment complete - Stolen from PayPal method */
		function payment_complete( $order, $txn_id = '', $note = '' ) {
			$order->add_order_note( $note );
			$order->payment_complete( $txn_id );
			// As in PayPal, we only empty the cart if it was paid
			if ( isset( WC()->cart ) ) {
				WC()->cart->empty_cart();
			}
			// Empty awaiting payment session - Only now
			unset( WC()->session->order_awaiting_payment );
		}

		/**
		 * Callback - Return from payment gateway
		 */
		function return_payment_gateway() {

			$redirect_url = '';
			$error        = false;
			$order_id     = 0;
			$orders_exist = false;

			if (
				//isset( $_GET['Success'] )
				//&&
				isset( $_GET['id'] )
				&&
				isset( $_GET['amount'] )
				&&
				isset( $_GET['wd_secret'] )
			) {
				$this->debug_log( '- Return from payment gateway (' . $_SERVER['REQUEST_URI'] . ') with all arguments' );
				$id         = trim( sanitize_text_field( $_GET['id'] ) );
				$val        = trim( sanitize_text_field( $_GET['amount'] ) ); // Não fazemos float porque 7.40 passaria a 7.4 e depois não validava a hash
				$wd_secret  = trim( sanitize_text_field( $_GET['wd_secret'] ) );
				$get_order  = $this->callback_helper_get_pending_order( $id, $val, $wd_secret );
				$success    = isset( $_GET['Success'] ) ? trim( $_GET['Success'] ) : '';
				switch ( $success ) {

					case 'True':
						if ( $get_order['success'] && $get_order['order'] ) {
							$order = $get_order['order'];
							$this->debug_log_extra( 'Order found: ' . $order->get_id() . ' - Status: ' . $order->get_status() );
							$order_id      = $order->get_id();
							$order_details = WC_IfthenPay_Webdados()->get_cofidispay_order_details( $order->get_id() );
							$note          = __( 'Cofidis Pay payment pre-approval received.', 'multibanco-ifthen-software-gateway-for-woocommerce' );
							$url           = $this->get_return_url( $order );
							do_action( 'cofidispay_ifthen_return_payment_gateway_complete', $order->get_id(), $_GET );
							$debug_order = wc_get_order( $order->get_id() );
							$this->debug_log( '-- Cofidis Pay payment pre-approval received - Order ' . $order->get_id(), 'notice' );
							$this->debug_log_extra( 'Redirect to thank you page: ' . $url . ' - Order ' . $order->get_id() . ' - Status: ' . $debug_order->get_status() );
							wp_redirect( $url );
							exit;
						} else {
							$error = $get_order['error'];
							// We should set a $redirect_url
						}
						break;

					default:
						// No additional $_GET field with the error code or message?
						if ( $get_order['success'] && $get_order['order'] ) {
							$order    = $get_order['order'];
							$order_id = $order->get_id();
							$error    = __( 'Payment failed on the gateway. Please try again.', 'multibanco-ifthen-software-gateway-for-woocommerce' );
							$order->update_status( 'failed', $error );
						} else {
							$error = __( 'Payment failed on the gateway. Please try again.', 'multibanco-ifthen-software-gateway-for-woocommerce' ) . ' - ' . $get_order['error'];
						}
						wc_add_notice( $error, 'error' );
						$redirect_url = add_query_arg( 'cofidispay_ifthen_failed', '1', wc_get_checkout_url() );
						break;

				}
			} else {
				$error = 'Return from payment gateway (' . $_SERVER['REQUEST_URI'] . ') with missing arguments';
			}

			// Error and redirect
			if ( $error ) {
				$this->debug_log( '- ' . $error, 'warning', true, $error );
				do_action( 'cofidispay_ifthen_callback_payment_failed', $order_id, $error, $_GET );
				if ( $redirect_url ) {
					wp_redirect( $redirect_url );
				} else {
					// ???
				}
				exit;
			}

		}

		/**
		 * Callback - IfthenPay callback
		 */
		function callback() {
			@ob_clean();
			// We must 1st check the situation and then process it and send email to the store owner in case of error.
			if (
				isset( $_GET['key'] )
				&&
				isset( $_GET['orderId'] )
				&&
				isset( $_GET['amount'] )
				&&
				isset( $_GET['requestId'] )
			) {
				// Let's process it
				$this->debug_log( '- Callback (' . $_SERVER['REQUEST_URI'] . ') with all arguments from ' . $_SERVER['REMOTE_ADDR'] );
				$request_id      = str_replace( ' ', '+', trim( sanitize_text_field( $_GET['requestId'] ) ) ); // If there's a plus sign on the URL We'll get it as a space, so we need to get it back
				$id              = trim( sanitize_text_field( $_GET['orderId'] ) );
				$val             = floatval( $_GET['amount'] );
				$arguments_ok    = true;
				$arguments_error = '';
				if ( trim( $_GET['key'] ) != trim( $this->secret_key ) ) {
					$arguments_ok     = false;
					$arguments_error .= ' - Key';
				}
				if ( trim( $request_id ) == '' ) {
					$arguments_ok     = false;
					$arguments_error .= ' - IdPedido';
				}
				if ( ! $val >= 1 ) {
					$arguments_ok     = false;
					$arguments_error .= ' - Value';
				}
				if ( $arguments_ok ) { // Isto deve ser separado em vários IFs para melhor se identificar o erro
					// Payments
					$orders_exist   = false;
					$pending_status = apply_filters( 'cofidispay_ifthen_valid_callback_pending_status', WC_IfthenPay_Webdados()->unpaid_statuses ); // Double filter - Should we deprectate this one?
					$args           = array(
						'type'                          => array( 'shop_order' ), // Regular order
						'status'                        => $pending_status,
						'limit'                         => -1,
						'_' . $this->id . '_request_id' => $request_id,
						'_' . $this->id . '_id'         => $id,
					);
					$orders         = wc_get_orders( WC_IfthenPay_Webdados()->maybe_translate_order_query_args( $args ) );
					if ( count( $orders ) > 0 ) {
						$orders_exist = true;
						$orders_count = count( $orders );
						foreach ( $orders as $order ) {
							// Just getting the last one
						}
					} else {
						$err = 'Error: No orders found awaiting payment with these details';
						$this->debug_log( '-- ' . $err, 'warning', true, 'Callback (' . $_SERVER['HTTP_HOST'] . ' ' . $_SERVER['REQUEST_URI'] . ') from ' . $_SERVER['REMOTE_ADDR'] );
					}
					if ( $orders_exist ) {
						if ( $orders_count == 1 ) {
							if ( floatval( $val ) == floatval( WC_IfthenPay_Webdados()->get_order_total_to_pay( $order ) ) ) {
								$note = __( 'Cofidis Pay payment approval received.', 'multibanco-ifthen-software-gateway-for-woocommerce' );
								if ( isset( $_GET['datahorapag'] ) && trim( $_GET['datahorapag'] ) != '' ) {
									$note .= ' ' . trim( $_GET['datahorapag'] );
								}
								$this->payment_complete( $order, '', $note );
								do_action( 'cofidispay_ifthen_callback_payment_complete', $order->get_id(), $_GET );
								header( 'HTTP/1.1 200 OK' );
								$this->debug_log( '-- Cofidis Pay payment approval received - Order ' . $order->get_id(), 'notice' );
								echo 'OK - Cofidis Pay payment approval received';
							} else {
								header( 'HTTP/1.1 200 OK' );
								$err = 'Error: The value does not match';
								$this->debug_log( '-- ' . $err . ' - Order ' . $order->get_id(), 'warning', true, 'Callback (' . $_SERVER['HTTP_HOST'] . ' ' . $_SERVER['REQUEST_URI'] . ') from ' . $_SERVER['REMOTE_ADDR'] . ' - The value does not match' );
								echo $err;
								do_action( 'cofidispay_ifthen_callback_payment_failed', $order->get_id(), $err, $_GET );
							}
						} else {
							header( 'HTTP/1.1 200 OK' );
							$err = 'Error: More than 1 order found awaiting payment with these details';
							$this->debug_log( '-- ' . $err, 'warning', true, 'Callback (' . $_SERVER['HTTP_HOST'] . ' ' . $_SERVER['REQUEST_URI'] . ') from ' . $_SERVER['REMOTE_ADDR'] . ' - More than 1 order found awaiting payment with these details' );
							echo $err;
							do_action( 'cofidispay_ifthen_callback_payment_failed', 0, $err, $_GET );
						}
					} else {
						header( 'HTTP/1.1 200 OK' );
						$err = 'Error: No orders found awaiting payment with these details';
						$this->debug_log( '-- ' . $err, 'warning', true, 'Callback (' . $_SERVER['HTTP_HOST'] . ' ' . $_SERVER['REQUEST_URI'] . ') from ' . $_SERVER['REMOTE_ADDR'] . ' - No orders found awaiting payment with these details' );
						echo $err;
						do_action( 'cofidispay_ifthen_callback_payment_failed', 0, $err, $_GET );
					}
				} else {
					// header("Status: 400");
					$this->debug_log( '-- ' . $err . $arguments_error, 'warning', true, 'Callback (' . $_SERVER['HTTP_HOST'] . ' ' . $_SERVER['REQUEST_URI'] . ') with argument errors from ' . $_SERVER['REMOTE_ADDR'] . $arguments_error );
					do_action( 'cofidispay_ifthen_callback_payment_failed', 0, $err, $_GET );
					wp_die( $err, 'WC_CofidisPay_IfThen_Webdados', array( 'response' => 500 ) ); // Sends 500
				}
			} else {
				// header("Status: 400");
				$err = 'Callback (' . $_SERVER['REQUEST_URI'] . ') with missing arguments from ' . $_SERVER['REMOTE_ADDR'];
				$this->debug_log( '- ' . $err, 'warning', true, 'Callback (' . $_SERVER['HTTP_HOST'] . ' ' . $_SERVER['REQUEST_URI'] . ') with missing arguments from ' . $_SERVER['REMOTE_ADDR'] );
				do_action( 'cofidispay_ifthen_callback_payment_failed', 0, $err, $_GET );
				wp_die( 'Error: Something is missing...', 'WC_CofidisPay_IfThen_Webdados', array( 'response' => 500 ) ); // Sends 500
			}
		}

		function callback_helper_get_pending_order( $id, $val, $wd_secret ) {
			$return         = array(
				'success' => false,
				'error'   => false,
				'order'   => false,
			);
			$pending_status = apply_filters( 'cofidispay_ifthen_valid_callback_pending_status', WC_IfthenPay_Webdados()->unpaid_statuses ); // Double filter - Should we deprectate this one?
			$args           = array(
				'type'                         => array( 'shop_order' ), // Regular order
				'status'                       => $pending_status,
				'limit'                        => -1,
				'_' . $this->id . '_wd_secret' => $wd_secret,
				'_' . $this->id . '_id'        => $id,
			);
			$orders_exist = false;
			$orders       = WC_IfthenPay_Webdados()->wc_get_orders( $args, $this->id );
			if ( count( $orders ) > 0 ) {
				$orders_exist = true;
				$orders_count = count( $orders );
				foreach ( $orders as $order ) {
					$order = wc_get_order( $order->get_id() );
				}
			}
			if ( $orders_exist ) {
				if ( $orders_count == 1 ) {
					//var_dump( $order );
					if ( floatval( $val ) == floatval( WC_IfthenPay_Webdados()->get_order_total_to_pay( $order ) ) ) {
						$return['success'] = true;
						$return['order']   = $order;
						return $return;
					} else {
						$return['error'] = 'Error: The value does not match';
						return $return;
					}
				} else {
					$return['error'] = 'Error: More than 1 order found awaiting payment with these details';
					return $return;
				}
			} else {
				$return['error'] = 'Error: No orders found awaiting payment with these details';
				return $return;
			}
		}

		/* Debug / Log - MOVED TO WC_IfthenPay_Webdados with gateway id as first argument */
		public function debug_log( $message, $level = 'debug', $to_email = false, $email_message = '' ) {
			if ( $this->debug ) {
				WC_IfthenPay_Webdados()->debug_log( $this->id, $message, $level, ( trim( $this->debug_email ) != '' && $to_email ? $this->debug_email : false ), $email_message );
			}
		}
		public function debug_log_extra( $message, $level = 'debug', $to_email = false, $email_message = '' ) {
			if ( $this->debug ) {
				WC_IfthenPay_Webdados()->debug_log_extra( $this->id, $message, $level, ( trim( $this->debug_email ) != '' && $to_email ? $this->debug_email : false ), $email_message );
			}
		}

		/* Global admin notices */
		function admin_notices() {
			// Callback email
			if (
				trim( $this->enabled ) == 'yes'
				&&
				strlen( trim( $this->cofidispaykey ) ) == 10
				&&
				trim( $this->secret_key ) != ''
			) {
				if ( $callback_email_sent = get_option( $this->id . '_callback_email_sent' ) ) { // No notice for older versions
					if ( $callback_email_sent == 'no' ) {
						if ( ! isset( $_GET['callback_warning'] ) ) {
							if ( apply_filters( 'cofidispay_ifthen_show_callback_notice', true ) ) {
								?>
								<div id="cofidispay_ifthen_callback_notice" class="notice notice-error" style="padding-right: 38px; position: relative;">
									<p>
										<strong>Cofidis Pay (IfthenPay)</strong>
										<br/>
										<?php _e( 'You haven’t yet asked IfthenPay for the “Callback” activation. The orders will NOT be automatically updated upon payment.', 'multibanco-ifthen-software-gateway-for-woocommerce' ); ?>
										<br/>
										<strong><?php _e( 'This is important', 'multibanco-ifthen-software-gateway-for-woocommerce' ); ?>! <a href="admin.php?page=wc-settings&amp;tab=checkout&amp;section=cofidispay_ifthen_for_woocommerce&amp;callback_warning=1"><?php _e( 'Do it here', 'multibanco-ifthen-software-gateway-for-woocommerce' ); ?></a>!</strong>
									</p>
								</div>
								<?php
							}
						}
					}
				}
			}
			// New method
			if (
				(
					strlen( trim( $this->cofidispaykey ) ) != 10
					||
					trim( $this->enabled ) != 'yes'
				)
				&&
				( ! apply_filters( 'multibanco_ifthen_hide_newmethod_notifications', false ) )
			) {
				?>
				<div id="cofidispay_ifthen_newmethod_notice" class="notice notice-info is-dismissible" style="padding-right: 38px; position: relative; display: none;">
					<img src="<?php echo esc_url( WC_IfthenPay_Webdados()->cofidispay_banner ); ?>" style="float: left; margin-top: 0.5em; margin-bottom: 0.5em; margin-right: 1em; max-height: 48px; max-width: 200px;"/>
					<p>
						<?php
							echo sprintf(
								__( 'There’s a new payment method available: %s.', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
								'<strong>Cofidis Pay (IfthenPay)</strong>'
							);
						?>
						<br/>
						<?php
						echo sprintf(
							__( 'Ask IfthenPay to activate it on your account and then %1$sconfigure it here%2$s.', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
							'<strong><a href="admin.php?page=wc-settings&amp;tab=checkout&amp;section=cofidispay_ifthen_for_woocommerce">',
							'</a></strong>'
						);
						?>
					</p>
				</div>
				<script type="text/javascript">
				(function () {
					notice    = jQuery( '#cofidispay_ifthen_newmethod_notice');
					dismissed = localStorage.getItem( '<?php echo $this->id; ?>_newmethod_notice_dismiss' );
					if ( !dismissed ) {
						jQuery( notice ).show();
						jQuery( notice ).on( 'click', 'button.notice-dismiss', function() {
							localStorage.setItem( '<?php echo $this->id; ?>_newmethod_notice_dismiss', 1 );
						});
					}
				}());
				</script>
				<?php
			}
		}

	}
}