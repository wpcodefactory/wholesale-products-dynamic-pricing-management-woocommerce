<?php
/*
 * Plugin Name: WholeSale Products Dynamic Pricing Management WooCommerce
 * Plugin URI: https://extend-wp.com/wholesale-products-dynamic-pricing-management-woocommerce
 * Description: WholeSale Products Dynamic Pricing Management for Multiple User Roles plugin to manage  WooCommerce B2B Store
 * Version: 1.3.0-dev
 * Author: extendWP
 * Author URI: https://extend-wp.com
 * WC tested up to: 10.6
 * License: GPL2
 * Created On: 19-11-2019
 * Updated On: 25-03-2026
 */

defined( 'ABSPATH' ) || exit;

include_once( plugin_dir_path(__FILE__) ."/class-main.php");

class WholeProdDynWooCommerce extends WholeProdDynWooCommerceInit{

		public $plugin = 'WholeProdDynWooCommerce';
		public $name = 'WholeSale Products Dynamic Pricing Management for Multiple Roles of your WooCommerce Store';
		public $shortName = 'WholeSale Management';
		public $slug = 'wholesale-products-dynamic-pricing-management-woocommerce';
		public $dashicon = 'dashicons-editor-table';
		public $proUrl = 'https://extend-wp.com/product/wholesale-products-dynamic-pricing-management-woocommerce';
		public $menuPosition ='50';
		public $localizeBackend;
		public $localizeFrontend;
		public $description = 'Manage your WooCommerce B2B Store with WholeSale Products Dynamic Pricing Management for Multiple User Roles';

		public function __construct() {


			add_action('plugins_loaded', array($this, 'translate') );

			add_action('admin_enqueue_scripts', array($this, 'BackEndScripts') );
			add_action('admin_menu', array($this, 'SettingsPage') );
			add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array($this, 'Links') );

			register_activation_hook( __FILE__,  array($this, 'onActivation') );

			add_action("admin_init", array($this, 'adminPanels') );

			add_action('admin_init', array($this, 'addnewRoles') );

			// Display extra fields for simple product
			add_action('woocommerce_product_options_general_product_data',  array($this, 'extraFields') );
			// Save extra fields values per product
			add_action('woocommerce_process_product_meta', array( $this, 'extraFieldsSave' ) );

			//CHANGE PRICES DYNAMICALLY
			add_filter('woocommerce_product_get_price', array( $this, 'customPriceSimple'), 10, 2);

			//change prices in cart
			add_action( 'woocommerce_before_calculate_totals', array( $this, 'customPriceToCart'), 20, 1);


			//add columns with  price in product admin table for each role
			add_filter('manage_product_posts_columns', array( $this,'add_product_columns' ) );
			add_action( 'manage_product_posts_custom_column', array( $this,'add_product_column_content' ),10,2 );

			add_action( 'product_cat_edit_form_fields',  array( $this,'product_cat_custom_fields' ), 10, 2 );

			add_action( 'product_cat_add_form_fields',  array( $this,'product_cat_add_new_meta_field' ), 10, 2 );
			add_filter('manage_edit-product_cat_columns', array( $this,'add_product_cat_columns' ) );
			add_filter('manage_product_cat_custom_column', array( $this,'add_product_cat_column_content' ),10,3);

			add_filter( 'woocommerce_get_price_suffix', array( $this,'priceTextSuffix' ), 99, 4 );

			add_action("admin_footer", array($this,"proModal" ) );

			// HPOS compatibility declaration

			add_action( 'before_woocommerce_init', function() {
				if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
				}
			} );
		}

		public function onActivation(){

			//Add default Role
			$role = 'Wholesale';
					global $wp_roles;
					if ( ! isset( $wp_roles ) )
						$wp_roles = new WP_Roles();
					if ( in_array( esc_html( $role ), $wp_roles->get_names())){
						//DO NOTHING
					}else{
						//add the new user role
						$roleId = str_replace(" ","_",strtolower( $role ) );
						add_role(
							sanitize_text_field( $roleId ),
							sanitize_text_field( $role ),
							array(
								'read'         => true,
								'delete_posts' => false
							)
						);
					}
			if( get_option( esc_html( $this->plugin ).'userRoles' ) =='' ) update_option( esc_html( $this->plugin ).'userRoles',sanitize_text_field( $role ) );
		}


		public function translate() {
	         load_plugin_textdomain(  esc_html( $this->plugin ), false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
	    }

		public function BackEndScripts(){
			wp_enqueue_style( esc_html( $this->plugin )."adminCss", plugins_url( "/css/backend.css", __FILE__ ) );
			wp_enqueue_style( esc_html( $this->plugin )."adminCss");

			wp_enqueue_script(  esc_html( $this->plugin )."adminJs", plugins_url( "/js/backend.js", __FILE__ ) , array('jquery') , null, true);

		}



		public function SettingsPage(){
			add_submenu_page( 'woocommerce', esc_html( $this->shortName ), esc_html( $this->shortName ), 'manage_options',  esc_html( $this->slug ) , array($this, 'init') );
		}

		public function Links($links){
			$mylinks[] =  "<a href='" . esc_url( admin_url( "admin.php?page=".$this->slug ) ) ."'>".esc_html__('Settings','wholesale-products-dynamic-pricing-management-woocommerce' )."</a>";
			$mylinks[] = "<a href='".esc_url( $this->proUrl )."' target='_blank'>".esc_html__("Go PRO",'wholesale-products-dynamic-pricing-management-woocommerce' )."</a>";
			return array_merge( $links, $mylinks );
		}


		public function init(){
			print "<div class='".esc_html( $this->plugin )."'>";
					esc_html( $this->adminHeader() );
					esc_html( $this->adminSettings() );
					esc_html( $this->adminFooter() );
			print "</div>";

		}

		public function proModal(){ ?>
			<div id="<?php print esc_html( $this->plugin ).'Modal'; ?>">
			  <!-- Modal content -->
			  <div class="modal-content">
				<div class='<?php print esc_html( $this->plugin ); ?>clearfix'><span class="close">&times;</span></div>
				<div class='<?php print esc_html( $this->plugin ); ?>clearfix'>
					<div class='<?php print esc_html( $this->plugin ); ?>columns2'>
						<center>
							<img style='width:90%' src='<?php echo esc_url( plugins_url( 'images/'.esc_html( $this->slug ).'-pro.png', __FILE__ ) ); ?>' style='width:100%' />
						</center>
					</div>

					<div class='<?php print esc_html( $this->plugin ); ?>columns2'>
						<h3><?php esc_html_e('Go PRO and get more important features!','wholesale-products-dynamic-pricing-management-woocommerce' ); ?></h3>
						<p><i class='fa fa-check'></i> <?php esc_html_e('Dynamic Pricing for Product Variations','wholesale-products-dynamic-pricing-management-woocommerce' ); ?></p>
						<p><i class='fa fa-check'></i> <?php esc_html_e('Wholesale Minimum Quantity for Simple & Variable Products','wholesale-products-dynamic-pricing-management-woocommerce' ); ?></p>
						<p><i class='fa fa-check'></i> <?php esc_html_e('Wholesale Minimum Quantities for Simple & Variable Products','wholesale-products-dynamic-pricing-management-woocommerce' ); ?></p>
						<p><i class='fa fa-check'></i> <?php esc_html_e('Global % Discount Definition for all Products per Role','wholesale-products-dynamic-pricing-management-woocommerce' ); ?></p>
						<p><i class='fa fa-check'></i> <?php esc_html_e('Price % Discount Definition per Product Category for each role','wholesale-products-dynamic-pricing-management-woocommerce' ); ?></p>
						<p><i class='fa fa-check'></i> <?php esc_html_e('Define per Product Discount Type - % or fixed amount','wholesale-products-dynamic-pricing-management-woocommerce' ); ?></p>
						<p><i class='fa fa-check'></i> <?php esc_html_e('Limit Product View to logged in Users of specific role','wholesale-products-dynamic-pricing-management-woocommerce' ); ?></p>
						<p class='bottomToUp'>
							<br/>
							<center>
								<a target='_blank' class='proUrl' href='<?php print esc_url( $this->proUrl ); ?>'>
									<?php esc_html_e('GET IT HERE', 'wholesale-products-dynamic-pricing-management-woocommerce' ); ?>
								</a>
							</center>
						</p>
					</div>
				</div>
			  </div>
			</div>
			<?php
		}


}
$instantiate = new WholeProdDynWooCommerce();
