<?php
/**
 * WholeSale Products Dynamic Pricing Management WooCommerce - WholeProdDynWooCommerceInit Class
 *
 * @version 1.3.0
 *
 * @author extendWP
 */

defined( 'ABSPATH' ) || exit;

class WholeProdDynWooCommerceInit {

	public $tab;
	public $activeTab;
	public $userRoles = 'userRoles';
	public $reduceType = 'reduceType';
	public $globalDiscount = 'globalDiscount';
	public $pricePreText = 'pricePreText';
	public $showSpecificProductsToRole = 'showSpecificProductsToRole';

	public function adminHeader(){

		print "<h1>".esc_html( $this->name )."</h1>";?>
		<?php
	}

	public function rating(){
	?>
		<div class="notice notice-success <?php print esc_html( $this->plugin ); ?>Rating is-dismissible">
			<p>
				<?php esc_html_e( "Do you like our effort? ", 'wholesale-products-dynamic-pricing-management-woocommerce' ); ?></i>
				<i class='fa fa-2x fa-smile-o' ></i>
				<?php esc_html_e('Then please give us a ','wholesale-products-dynamic-pricing-management-woocommerce' ); ?>
				<a target='_blank' href='https://wordpress.org/support/plugin/wholesale-products-dynamic-pricing-management-woocommerce/reviews/#new-post'>
					<i class='fa fa-2x fa-star' ></i><i class='fa fa-2x fa-star' ></i><i class='fa fa-2x fa-star' ></i><i class='fa fa-2x fa-star' ></i><i class='fa fa-2x fa-star' ></i>
					<?php esc_html_e(' rating','wholesale-products-dynamic-pricing-management-woocommerce' ); ?>
				</a>
			</p>
		</div>
	<?php
	}

	public function adminSettings(){

			esc_html( $this->adminTabs() );

			?>
			<form method="post" id='<?php print esc_attr( $this->plugin ); ?>Form'
			action= "<?php echo esc_url( admin_url( "admin.php?page=".esc_attr( $this->slug ) ) ); ?>">
			<?php

			settings_fields(  esc_html( $this->plugin ).'general-options' );
			do_settings_sections(  esc_html( $this->plugin ).'general-options' );

			wp_nonce_field( esc_html( $this->plugin ) );
			submit_button();

			?></form>

			<?php esc_html( $this->rating() ); ?>
			<div class='result'><?php esc_html( $this->adminProcessSettings() ); ?> </div>
	<?php
	}



	public function adminTabs(){
			$this->tab = array( 'settings' => 'Settings','more'=>"Go PRO");
			if( isset( $_GET['tab'] ) ){
				$this->activeTab = esc_html( $_GET['tab'] ) ;
			}else $this->activeTab = 'main';
			echo '<h2 class="nav-tab-wrapper" >';
			foreach( $this->tab as $tab => $name ){
				$class = ( $tab == $this->activeTab ) ? ' nav-tab-active' : '';
				if($tab == 'more'){
					echo "<a class='nav-tab".esc_attr( $class )." proVersion' href='#'>".esc_html( $name )."</a>";
				}else echo "<a class='nav-tab".esc_attr( $class )." contant' href='?page=".esc_attr( $this->slug )."&tab=".esc_attr( $tab )."'>".esc_html( $name )."</a>";

			}
			echo '</h2>';
	}


	public function adminFooter(){ ?>
		<hr>
		<a target='_blank' class='wpeieProLogo' href='https://extend-wp.com'>
			<img  src='<?php echo esc_url( plugins_url( 'images/extendwp.png', __FILE__ ) ); ?>' alt='<?php _e("By extend-wp.com","wpeiePro");?>' title='<?php esc_html_e("By extend-wp.com","wholesale-products-dynamic-pricing-management-woocommerce");?>' />
		</a>
		<?php
	}

	public function userRoles(){

		if( isset($_REQUEST[ $this->plugin.'userRoles'] ) ){
			$userRoles =  sanitize_text_field( $_REQUEST[ $this->plugin.'userRoles'] );
		}else $userRoles = get_option( esc_html( $this->plugin ).'userRoles' ) ;

		?>
			<input name="<?php print esc_attr( $this->plugin ).esc_attr( $this->userRoles ) ; ?>" id="<?php print esc_attr( $this->plugin ).esc_attr( $this->userRoles );?>"   placeholder='Roles comma separated' value='<?php if(!empty($userRoles) ) print esc_attr( $userRoles ); ?>' />
		<?php


	}

	/**
	 * pricePreText.
	 *
	 * @version 1.3.0
	 */
	public function pricePreText(){

		if( isset($_REQUEST[$this->plugin.'userRoles'] ) ){
			$userRoles =  sanitize_text_field(strtolower($_REQUEST[$this->plugin.'userRoles']));
		}else $userRoles = get_option($this->plugin.'userRoles');

		$roles = explode(",", $userRoles );

		foreach( $roles as $role ){

			if( isset($_REQUEST[ $role .'pricePreText'] ) ){
				$pricePreText =  sanitize_text_field($_REQUEST[$role .'pricePreText']);
			}else $pricePreText = get_option($role .'pricePreText');

			?>
				<input name="<?php echo esc_attr( $role . $this->pricePreText ); ?>" id="<?php echo esc_attr( $role . $this->pricePreText ); ?>"    placeholder='<?php echo esc_attr( $role ); ?> <?php esc_html_e('Prices prefix Text','wholesale-products-dynamic-pricing-management-woocommerce' ); ?>' value='<?php if(!empty($pricePreText) ) print esc_attr( $pricePreText ); ?>' />
			<?php

		}



	}

	public function showSpecificProductsToRole(){

		?>
			<input class='proVersion' type='checkbox'  />
		<?php

	}

	public function reduceType(){

		?>
			<select class='proVersion'  >
				<option><?php esc_html_e('Select...','wholesale-products-dynamic-pricing-management-woocommerce' ); ?></option>
				<option>
					<?php esc_html_e('Fixed Amount','wholesale-products-dynamic-pricing-management-woocommerce' ); ?>
				</option>
				<option>
					<?php esc_html_e('Percentage','wholesale-products-dynamic-pricing-management-woocommerce' ); ?>
				</option>
			</select>
		<?php
	}

	public function globalDiscount(){

		$userRoles = get_option( esc_html( $this->plugin ).'userRoles' );
		$roles = explode(",", $userRoles );

		foreach( $roles as $role ){

			?>
			<input class='proVersion'  placeholder='<?php print esc_attr( $role );?> discount'  />
			<?php

		}
	}

	public function adminPanels(){
		add_settings_section( esc_html( $this->plugin )."general", "", null,  esc_html( $this->plugin )."general-options");

		add_settings_field( 'userRoles',esc_html__( "Add User Roles",'wholesale-products-dynamic-pricing-management-woocommerce' ), array($this, 'userRoles'),  esc_html( $this->plugin )."general-options",  esc_html( $this->plugin )."general");
		register_setting( $this->plugin."general", esc_html( $this->plugin ).esc_html( $this->userRoles ) );



		add_settings_field( 'reduceType',"<span class='proVersion'>".esc_html__( "Product Price Type - in PRO",'wholesale-products-dynamic-pricing-management-woocommerce' )."</span>", array($this, 'reduceType'),   esc_html( $this->plugin )."general-options",  esc_html( $this->plugin )."general");
		register_setting( esc_html( $this->plugin )."general", esc_html( $this->plugin ).esc_html( $this->reduceType ) );

		$userRoles = get_option( esc_html( $this->plugin ).'userRoles' );
		$roles = explode(",", $userRoles );

		foreach( $roles as $role ){
			add_settings_field( 'globalDiscount',"<span class='proVersion'>".esc_html__( "Global % Discount  - in PRO",'wholesale-products-dynamic-pricing-management-woocommerce' )."</span>", array($this, 'globalDiscount'),   esc_html( $this->plugin )."general-options",  esc_html( $this->plugin )."general" );
			register_setting( esc_html( $this->plugin )."general", esc_html( $role ).esc_html( $this->globalDiscount ) );

			add_settings_field('pricePreText',esc_html__( "Price Prefix Text",'wholesale-products-dynamic-pricing-management-woocommerce' ), array($this, 'pricePreText'),   $this->plugin."general-options",  $this->plugin."general");
			register_setting( $this->plugin."general", $role.$this->pricePreText);

		}


		add_settings_field('showSpecificProductsToRole',"<span class='proVersion'>".esc_html__( "Show Only Specific Products to Users based on Price per Role - in PRO",'wholesale-products-dynamic-pricing-management-woocommerce' )."</span>", array($this, 'showSpecificProductsToRole'),   esc_html( $this->plugin )."general-options",  esc_html( $this->plugin )."general" );
		register_setting( esc_html( $this->plugin )."general", esc_html( $this->plugin ).esc_html( $this->showSpecificProductsToRole ) );

	}

	public function adminProcessSettings(){

		if($_SERVER['REQUEST_METHOD'] == 'POST' && current_user_can('administrator') ){

			check_admin_referer(  esc_html( $this->plugin ) );
			check_ajax_referer(  esc_html( $this->plugin ) );
			//if($_REQUEST[ $this->plugin .$this->userRoles  ] && !empty( $_REQUEST[  $this->plugin . $this->userRoles  ] ) ){

				//first remove old roles
				$userRoles = esc_html( get_option( $this->plugin.'userRoles' )  );
				$roles = explode(",", $userRoles );

				foreach( $roles as $role ){
					remove_role( sanitize_text_field( strtolower( $role ) ) );

				}
				update_option( esc_html( $this->plugin ).esc_html( $this->userRoles ),sanitize_text_field( strtolower($_REQUEST[$this->plugin.$this->userRoles])));

			$userRoles = get_option($this->plugin.'userRoles');
			$roles = explode(",", $userRoles );
			foreach( $roles as $role ){

				if( isset( $_REQUEST[$role.'pricePreText'] ) ){
					update_option($role.$this->pricePreText,sanitize_text_field($_REQUEST[$role.$this->pricePreText]));
				}


			}

		}
	}


	public function addnewRoles(){

		$userRoles = get_option( esc_html( $this->plugin ).'userRoles' );


		if( !empty( $userRoles ) ) {

			$roles = explode( "," , $userRoles );

				foreach( $roles as $role ){

					global $wp_roles;
					if ( ! isset( $wp_roles ) )
						$wp_roles = new WP_Roles();
					if (in_array( esc_html( $role ), $wp_roles->get_names() ) ){
						//DO NOTHING
					}else{
						//add the new user role
						$roleId = htmlspecialchars( str_replace(" ","_",strtolower( $role  ) ) );
						add_role(
							sanitize_text_field( $roleId ),
							sanitize_text_field( $role ),
							array(
								'read'         => true,
								'delete_posts' => false
							)
						);
					}
				}
		}
	}


	public function extraFields() {
		global $woocommerce, $post , $product;

		$post_id = (int)$post->ID;

		$userRoles = get_option( esc_html( $this->plugin ).'userRoles' );
		$roles = explode( ",", $userRoles );

		foreach( $roles as $role ){


			?>
			<p class="form-field">
				<label for="<?php print esc_html( $role )."minQuant"; ?>"><?php esc_html_e($role.' Min Quantity', 'wholesale-products-dynamic-pricing-management-woocommerce' );?></label>
				<input type="number" name="<?php esc_attr( $role )."minQuant"; ?>" class="<?php print esc_attr( $role )."minQuant"; ?> small-text proVersion" placeholder='Pro Version' />

			</p>
			<?php
			$price = esc_html( get_post_meta( $post_id,  $role .'price', true ) );
			?>
			<p class="form-field">
				<label for="<?php print esc_html( $role )."price"; ?>"><?php esc_html_e($role.' Price','wholesale-products-dynamic-pricing-management-woocommerce' );?>

				</label>
				<input type="number" step='any' name="<?php print esc_attr( $role )."price"; ?>" class="<?php print esc_attr( $role )."price"; ?> small-text" value="<?php echo esc_attr( $price ); ?>" />

			</p>
			<?php
			?>
			<p class="form-field">
				<label for="<?php print esc_html( $role )."step"; ?>"><?php esc_html_e($role.' Step', 'wholesale-products-dynamic-pricing-management-woocommerce'  );?>
				</label>
				<input type="number" name="<?php print esc_attr( $role )."step"; ?>" class="<?php print esc_attr( $role )."step"; ?> small-text proVersion" placeholder='Pro Version' />

			</p>
			<?php
		}

	}

	public function extraFieldsSave($post_id) {

		$userRoles = get_option( esc_html( $this->plugin ).'userRoles' );
		$roles = explode(",", $userRoles );

		foreach( $roles as $role ){

			$price = $_POST[ $role ."price" ];

			if (isset($price)) {
				update_post_meta($post_id, esc_html( $role )."price", sanitize_text_field( $price ) );
			}


		}

	}



	public function customPriceSimple($price, $product) {

		global $woocommerce, $post, $product;

		if( !is_object($post) ) return;

		$post_id = (int)$post->ID;

		if ( !is_user_logged_in()  ) return esc_html( $price );


		$userRoles = get_option( esc_html( $this->plugin ).'userRoles' );
		$roles = explode(",", $userRoles );
		foreach( $roles as $role ){


			if ($this->hasRole( esc_html( $role ) ) && !is_cart() ){
						// apply product specific discount if any
						if(get_post_meta( $post_id, esc_html( $role ).'price', true) !='' ){
							 $newprice = get_post_meta($post_id, esc_html( $role ).'price', true);
							 $price =  $newprice ;
							return esc_html( $price );
						}

			}

		}

		return esc_html( $price );

	}


	public function customPriceToCart( $cart ) {

		// This is necessary for WC 3.0+
		if ( is_admin() && ! defined( 'DOING_AJAX' ) )
			return;

		// Avoiding hook repetition (when using price calculations for example)
		if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
			return;

		$userRoles = get_option( esc_html( $this->plugin ).'userRoles' );
		$roles = explode( ",", $userRoles );
		foreach( $roles as $role ){

			if ($this->hasRole( esc_html( $role ) ) ){
				// Loop through cart items
				foreach ( $cart->get_cart() as $item ) {
					//variation_id
					if( $item['variation_id']=='' ){

						if( get_post_meta( $item['product_id'], esc_html( $role ).'price', true) !='' ){
							 $newprice = get_post_meta( $item['product_id'], esc_html( $role ).'price', true );
							 $item['data']->set_price( esc_html( $newprice ) );
						}else{
							$price  = get_post_meta( $item['product_id'], '_price', true );
							$item['data']->set_price( esc_html( $price ) );
						}
					}else{
						$price  = get_post_meta( $item['variation_id'], '_price', true );
							$item['data']->set_price( esc_html( $price ) );

					}

				}
			}

		}

	}

	public function hasRole( $role = '',$user_id = null ){
		if ( is_numeric( $user_id ) )
			$user = get_user_by( 'id',$user_id );
		else
			$user = wp_get_current_user();

		if ( empty( $user ) )
			return false;

		return in_array( strtolower($role), (array) $user->roles );
	}


	public function add_product_columns( $columns ){

		$userRoles = get_option( esc_html( $this->plugin ).'userRoles' );
		if( !empty($userRoles) ) {
			$roles = explode(",", $userRoles );

				foreach( $roles as $role ){
					$columns[$role] = esc_html__( $role. " Price", 'wholesale-products-dynamic-pricing-management-woocommerce'  );
				}
		}
		return $columns;
	}

	public function add_product_column_content($column_name, $post_id){
		global $post;
		$userRoles = get_option( esc_html( $this->plugin ).'userRoles' );
		if( !empty($userRoles) ) {
			$roles = explode(",", $userRoles );

				foreach( $roles as $role ){

					if( $column_name == $role ) {
						global $product;
						if ( $product->is_type( 'variable' ) ) {
							// print nothing here
						}else{
							if( !empty( get_post_meta( $post_id, esc_html( $role ).'price', true ) ) ) print esc_html( get_post_meta( $post_id, esc_html( $role ).'price', true ) );
						}
					}
				}
		}
	}

	/* ADD PERCENTAGE TO PRODUCT CAT */

	public function product_cat_add_new_meta_field() {
		// this will add the custom meta field to the add new term page
		?>
		<div class="form-field">
		<?php
		$userRoles = get_option( esc_html( $this->plugin ).'userRoles' );
		$roles = explode(",", $userRoles );
		foreach( $roles as $role ){
			?>
			<label class='proVersion' for="DiscountPrice<?php print esc_html( $role ); ?>"><?php esc_html_e( $role ) . esc_html_e( ' Discount Price %', 'wholesale-products-dynamic-pricing-management-woocommerce' ); ?></label>
			<input type="text" class='DiscountPrice proVersion' name="" size="25" style="width:60%;" placeholder='PRO Version' ><br />
			<span class="description"><?php esc_html_e( 'Discount Price % for', 'wholesale-products-dynamic-pricing-management-woocommerce' ); ?> <?php print esc_html( $role ); ?></span>
			<?php
		}
		?>

		</div>
	<?php
	}

	// A callback function to add a custom field to product category
	public function product_cat_custom_fields( $tag ) {
		$userRoles = get_option( esc_html( $this->plugin ).'userRoles' );
		$roles = explode(",", $userRoles );
		foreach( $roles as $role ){
			?>
			<tr class="form-field proVersion">
				<th scope="row" valign="top">
					<label for="DiscountPrice<?php print esc_html( $role ); ?>">
						<?php esc_html_e( $role ) . esc_html_e( ' Discount Price %', 'wholesale-products-dynamic-pricing-management-woocommerce' ); ?>
					</label>
				</th>
				<td>
					<input type="text" class='DiscountPrice proVersion' name="" size="25" style="width:60%;" placeholder='PRO Version' ><br />
					<span class="description"><?php esc_html_e( 'Discount Price % for', 'wholesale-products-dynamic-pricing-management-woocommerce' ); ?> <?php print esc_html( $role ); ?></span>
				</td>
			</tr>
			<?php
		}
	}


	public function add_product_cat_columns( $columns ){

			$userRoles = get_option( esc_html( $this->plugin ).'userRoles' );
			$roles = explode(",", $userRoles );
			foreach( $roles as $role ){
				$columns[ 'discount'.esc_html( $role ) ] = esc_html__( 'Discount Price % - ', 'wholesale-products-dynamic-pricing-management-woocommerce' ). esc_html( $role );
			}

		return $columns;
	}


	public function add_product_cat_column_content($content,$column_name,$term_id){
		$term= get_term($term_id, 'product_cat');

		$userRoles = get_option( esc_html( $this->plugin ).'userRoles' );
		if( !empty($userRoles) ) {
			$roles = explode(",", $userRoles );

				foreach( $roles as $role ){

					if( $column_name == 'discount'.esc_html( $role ) ) {
						$cont = "<span class='proVersion'>PRO Version only<span>";
						$content = $cont;
					}
				}
		}

		return $content ;
	}


	public function priceTextSuffix( $html, $product, $price, $qty ){

		if (!is_user_logged_in()  ) return esc_html( $price );
		$userRoles = get_option(  esc_html( $this->plugin  ).'userRoles' );
		$roles = explode(",", $userRoles );
		foreach( $roles as $role ){

			if ($this->hasRole( esc_html( $role ) )  ){
				$text = get_option( esc_html( $role ).'pricePreText' );
				if(!empty(get_post_meta( (int)$product->get_id(), esc_html( $role ) .'price', true))) $html .= " ". esc_html( $text ) . " ";

			}
		}

		return $html;

	}


}
