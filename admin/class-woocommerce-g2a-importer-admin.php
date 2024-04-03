<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://cedcommerce.com
 * @since      1.0.0
 *
 * @package    woocommerce_g2a_importer
 * @subpackage woocommerce_g2a_importer/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    woocommerce_g2a_importer
 * @subpackage woocommerce_g2a_importer/admin
 * @author     CedCommerce <plugins@cedcommerce.com>
 */
class Woocommerce_G2A_Importer_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		// update_post_meta('1085','ced_g2a_order_id',"3332353");
		// update_post_meta('1085','ced_g2a_order_transaction_id',"34234dsfdsfsdfs333");
		// update_post_meta('1085','ced_g2a_order_key',"ssdnjdhnsdjkhdwsndjsdscndjkg");	
		// $actions = wc_get_account_orders_actions( $order );
		// echo '<pre>';
		// print_r($actions);
		// die;
		$this->plugin_name = $plugin_name;
		$this->version = $version;
        add_action( 'woocommerce_after_order_itemmeta', array($this,'order_meta_customized_display'),10, 3 );
		add_action( "wp_ajax_ced_g2a_import_to_store", array( $this, "ced_g2a_import_to_store" ) );
		add_action( 'ced_g2a_sync_products_cron_job', array( $this, "ced_g2a_sync_existing_product" ) );
		add_action( 'ced_g2a_auto_import_cron_job', array( $this, "ced_g2a_auto_import_cron_job" ) );
        add_filter('manage_edit-product_columns', array($this,'ced_add_column'), 10,2);
         add_filter('manage_product_posts_custom_column', array($this,'ced_modify_column'),10,2);
		add_filter('cron_schedules',array($this,'my_cron_schedules'));
		add_action( 'wp_ajax_ced_g2a_place_order_on_g2a', array( $this, 'ced_g2a_place_order_on_g2a' ) );
		add_action( 'wp_ajax_pay_for_g2a_order', array( $this, 'pay_for_g2a_order' ) );
		add_action( 'wp_ajax_ced_g2a_get_order_key', array( $this, 'ced_g2a_get_order_key' ) );
		add_action( 'wp_ajax_ced_g2a_send_email_key', array( $this, 'ced_g2a_send_email_key_functn' ) );
		add_action( 'add_meta_boxes',array($this, 'ced_g2a_add_order_metabox') ) ;
		add_action('wp_ajax_ced_g2a_save_included_category', array($this,'ced_g2a_save_included_category'));
		add_filter('cron_schedules',array($this,'my_g2a_cron_schedules'));
		//add_action( 'sync_existing_product',array($this, 'ced_g2a_sync_existing_product') );
		add_action('admin_init',array($this,'ced_add_schedulers'));
		
		}
		

		public function ced_add_schedulers(){
			if(!wp_get_schedule('sync_existing_product'))
			{
			wp_schedule_event(time(),'ced_g2a_10min', 'sync_existing_product');
			}
		}
		public function ced_g2a_sync_existing_product()
		{
			$ced_g2a_g2a_details = get_option( 'ced_g2a_settings', array() );
		
			if($ced_g2a_g2a_details['ced_g2a_enable_price_sync'] != "yes")
				return;

			$products_to_sync = get_option("ced_g2a_chunk_product",array());
				if(empty($products_to_sync))
				{
					$store_products = get_posts(
						array(
			    			'numberposts' => -1,
			    			'post_type'   => 'product',
			    			'meta_key' => 'ced_g2a_g2a_itemId',
	                        'meta_value' => "",
						    'meta_compare' => '!='
						) 
					);
					$store_products = wp_list_pluck( $store_products, 'ID' );
					$products_to_sync = array_chunk($store_products,100);

				}
				if( is_array( $products_to_sync[0] ) && !empty( $products_to_sync[0] ) )
				{
					$this->sync_chunk_products( $products_to_sync[0] );
					unset($products_to_sync[0]);
					$products_to_sync = array_values($products_to_sync);
					update_option("ced_g2a_chunk_product",$products_to_sync);
			
				}
		}

		public function sync_chunk_products($productIds = array()){
			$fileName = CED_G2A_PATH."admin/lib/ced-g2a-product-helper.php";
			require_once $fileName;
			$Ced_G2A_Product_Helper = new Ced_G2A_Product_Helper;
			$Ced_G2A_Product_Helper_instance = $Ced_G2A_Product_Helper->get_instance();
			$ced_g2a_settings = get_option( 'ced_g2a_settings', array() );
			foreach ($productIds as $key => $ID) {
				$itemId = get_post_meta($ID,'ced_g2a_g2a_itemId',true);
				$itemData = $Ced_G2A_Product_Helper_instance->get_item_data($itemId);
                
				$itemData = isset( $itemData['docs'] ) ? $itemData['docs'][0] : array();
				if(!empty($itemData))
				{
                 	if(isset($itemData['retail_min_price']) && !empty($itemData['retail_min_price']))
                    {
                        $reg_price = $this->setProductPrice( $ID, $itemData['retail_min_price'], $ced_g2a_settings );
                        $sale_price = $this->setProductPrice( $ID, $itemData['minPrice'], $ced_g2a_settings );
                        update_post_meta( $ID, '_price', $sale_price );
                        update_post_meta( $ID, '_sale_price', $sale_price );
                        update_post_meta( $ID, '_regular_price', $reg_price );
                    }
                    else
                    {
                        $reg_price = $this->setProductPrice( $ID, $itemData['minPrice'], $ced_g2a_settings );
                        update_post_meta( $ID, '_price', $reg_price );
                        update_post_meta( $ID, '_regular_price', $reg_price );    
                        delete_post_meta( $ID, '_sale_price', true );
                    }
					if( isset( $itemData['qty'] ) && $itemData['qty'] > 0 )
					{
						update_post_meta( $ID, '_stock', $itemData['qty'] );
						update_post_meta( $ID, '_stock_status', "instock" );
					}
					else
					{
						update_post_meta( $ID, '_stock_status', "outofstock" );
					}
				}
			}
		}
        
			public function my_g2a_cron_schedules($schedules){
		    if(!isset($schedules["ced_g2a_6min"])){
		        $schedules["ced_g2a_6min"] = array(
		            'interval' => 6*60,
		            'display' => __('Once every 6 minutes'));
		    }
		    if(!isset($schedules["ced_g2a_10min"])) {
		        $schedules["ced_g2a_10min"] = array(
		            'interval' => 10*60,
		            'display' => __('Once every 10 minutes'));
		    }
		    if(!isset($schedules["ced_g2a_15min"])){
		        $schedules["ced_g2a_15min"] = array(
		            'interval' => 15*60,
		            'display' => __('Once every 15 minutes'));
		    }
		    if(!isset($schedules["ced_g2a_30min"])){
		        $schedules["ced_g2a_30min"] = array(
		            'interval' => 30*60,
		            'display' => __('Once every 30 minutes'));
		    }
		    return $schedules;
		}
	public function ced_g2a_save_included_category()
	{
		$included_category=isset($_POST['included']) ? $_POST['included'] : '' ;
		if(!empty($included_category))
		{
			update_option("included_category",$included_category);
		}
		wp_die();
	}
    
    function my_cron_schedules($schedules){
	    if(!isset($schedules["ced_g2a_6min"])){
	        $schedules["ced_g2a_6min"] = array(
	            'interval' => 6*60,
	            'display' => __('Once every 6 minutes'));
	    }
	    if(!isset($schedules["ced_g2a_10min"])) {
	        $schedules["ced_g2a_10min"] = array(
	            'interval' => 10*60,
	            'display' => __('Once every 10 minutes'));
	    }
	    if(!isset($schedules["ced_g2a_15min"])){
	        $schedules["ced_g2a_15min"] = array(
	            'interval' => 15*60,
	            'display' => __('Once every 15 minutes'));
	    }
	    if(!isset($schedules["ced_g2a_30min"])){
	        $schedules["ced_g2a_30min"] = array(
	            'interval' => 30*60,
	            'display' => __('Once every 30 minutes'));
	    }
	    return $schedules;
	}
	public function ced_g2a_add_order_metabox()
	{

		add_meta_box(
			'ced_g2a_manage_orders_metabox'
			,__('Manage G2A Orders','ced-g2a'). wc_help_tip( __( 'Manage G2A Orders.', 'ced-g2a' ) )
			,array($this,'ced_g2a_render_orders_metabox')
			,'shop_order'
			,'advanced'
			,'high'
		);
	}

	public function ced_g2a_send_email_key_functn(){
		$orderId=isset($_POST['orderId']) ? $_POST['orderId'] : "" ;
		$order = new WC_Order($orderId);
		$email =  $order->get_billing_email();

		$isKey=get_post_meta($orderId,'ced_g2a_order_key',true);
		if(isset($isKey) && isset($email)){

			$admin_email = get_option('admin_email');
			$subject = "Thank you for purchase";
			$message = 'Thank you for purchase...Here is the key : "'.$isKey.'"';
			$headers = array('Content-Type: text/html; charset=UTF-8');
			$sent = wp_mail($email, $subject, strip_tags($message), $headers);
			//echo $sent;die;
			  if($sent) {
			  	echo 'message sent!';
			  }//message sent!
			  else  {
			  	echo 'message wasn not sent';
			  }//message wasn't sent
		}
	}

	public function ced_g2a_render_orders_metabox($order)
	{	
		$orderId=$order->ID;
		?>
		<div id="ced-g2a-loader" class="loading-style-bg ced-g2a-loader">
			<img src="<?php echo CED_G2A_URL.'admin/images/BigCircleBall.gif' ?>">
		</div>
		<div id="ced_g2a_manage_order_wrapper">
			<div id="ced_g2a_manage_order_container">
			<?php
			$isPlaced=get_post_meta($orderId,'ced_g2a_order_id',true);
			if(!$isPlaced)
			{
			?>
				<div class="ced_g2a_manage_order_button" id="ced_g2a_place"><input type="button" data-orderId="<?php echo $orderId ?>" id="ced_g2a_place_order" value="Place Order On G2A"></div>
			<?php
			}
			else
			{
				echo '<div class="ced_g2a_manage_order_button">Order Placed !!</div>';
			}
			$isPaid=get_post_meta($orderId,'ced_g2a_order_transaction_id',true);
			if(!$isPaid)
			{
			    ?>
			    <div class="ced_g2a_manage_order_button" id="ced_g2a_pay_order"><input type="button" data-orderId="<?php echo $orderId ?>" id="ced_g2a_pay_for_order" value="Pay For Order"></div>;
		    	<?php
			}
			else
			{
			    echo '<div class="ced_g2a_manage_order_button"> Order Paid !!</div>';
			}
			
			$isKey=get_post_meta($orderId,'ced_g2a_order_key',true);
			if(!$isKey)
			{
			    ?>
			    <div class="ced_g2a_manage_order_button" id="ced_g2a_get_key"><input type="button" data-orderId="<?php echo $orderId ?>" id="ced_g2a_get_order_key" value="Get Order Key"></div>;
			    <?php

			}
			else
			{
			    echo '<div class="ced_g2a_manage_order_button">'.$isKey.'</div>';
			    echo '<div class="ced_g2a_email_key_button"><input type="button" class="ced_g2a_email_key_button" value="Send mail" data-orderId="'.$orderId .'"></input></div>';
			}
			?>
			
			</div>
		</div>

		<?php
	}
	public function ced_g2a_place_order_on_g2a()
	{	
		$orderId=isset($_POST['orderId']) ? $_POST['orderId'] : "" ;
		$fileName = CED_G2A_PATH."admin/lib/ced-g2a-product-helper.php";
		require_once $fileName;
		
		if( $orderId == "" )
				return;

		$order = wc_get_order( $orderId );
		$orderItems = $order->get_items();
		$productIdsToadd = array();
		foreach ($orderItems as $item_id => $item_info) {
			$item_data = $item_info->get_data();
			$g2a_product_id = get_post_meta( $item_data['product_id'] , 'ced_g2a_g2a_itemId', true );
			$productIdsToadd[] = $g2a_product_id ;
		}
		$ced_g2a_g2a_details = get_option( 'ced_g2a_config_details', array() );
		if(!empty($ced_g2a_g2a_details))
		{
			$Ced_G2A_Product_Helper = new Ced_G2A_Product_Helper;
			$Ced_G2A_Product_Helper_instance = $Ced_G2A_Product_Helper->get_instance();
			$response = $Ced_G2A_Product_Helper_instance->add_g2a_order($ced_g2a_g2a_details, $productIdsToadd);
			if($response['order_id'])
			{
				update_post_meta($orderId,"ced_g2a_order_id",$response['order_id']);
				update_post_meta($orderId,"ced_g2a_order_status",'pending');
				echo 'success';wp_die();
			}
			echo $response['message'];wp_die();
		}
		
	}


	public function g2a_order_status()
	{
		$fileName = CED_G2A_PATH."admin/lib/ced-g2a-product-helper.php";
		require_once $fileName;
		$ced_g2a_g2a_details = get_option( 'ced_g2a_config_details', array() );
		
		$g2a_orders = wc_get_orders(
					array(
		    			'numberposts' => -1,
		    			'post_type'   => 'shop_order',
		    			'meta_key' => 'ced_g2a_order_id',
                        'meta_value' => "",
					    'meta_compare' => '!='
					) 
				);
		$g2a_orders = wp_list_pluck( $store_products, 'ID' );
		// $g2a_orders = array('22' =>'1111111');
		foreach ($g2a_orders as $key => $order_id) {
			$order_status = get_post_meta($order_id,"ced_g2a_order_status",true);
			if($order_status == 'complete')
			{
				continue;
			}
			else
			{
				if(!empty($ced_g2a_g2a_details))
				{

					$g2aOrder = get_post_meta($order_id,"ced_g2a_order_id",true);
					// $g2aOrder = 2423456;
					$Ced_G2A_Product_Helper = new Ced_G2A_Product_Helper;
					$Ced_G2A_Product_Helper_instance = $Ced_G2A_Product_Helper->get_instance();
					$response = $Ced_G2A_Product_Helper_instance->get_g2a_order_status($ced_g2a_g2a_details, $g2aOrder);
					if($response['status'])
					{
						update_post_meta($order_id,"ced_g2a_order_status",$response['status']);
					}
				}
			}
		}
	}

	public function pay_for_g2a_order()
	{	
		$order_id=isset($_POST['orderId']) ? $_POST['orderId'] : "" ;
		$ced_g2a_g2a_details = get_option( 'ced_g2a_config_details', array() );
			if(!empty($ced_g2a_g2a_details))
				{
					$fileName = CED_G2A_PATH."admin/lib/ced-g2a-product-helper.php";
					require_once $fileName;
					$g2aOrder = get_post_meta($order_id,"ced_g2a_order_id",true);
					$Ced_G2A_Product_Helper = new Ced_G2A_Product_Helper;
					$Ced_G2A_Product_Helper_instance = $Ced_G2A_Product_Helper->get_instance();
					$response = $Ced_G2A_Product_Helper_instance->pay_order($ced_g2a_g2a_details, $g2aOrder);
					if($response['transaction_id'])
					{
						update_post_meta($order_id,"ced_g2a_order_transaction_id",$response['transaction_id']);
						update_post_meta($orderId,"ced_g2a_order_status",'paid');
						echo 'success';wp_die();
					}
					echo $response['message'];wp_die();
				}
	}
	
	public function ced_g2a_get_order_key()
	{
		$order_id=isset($_POST['orderId']) ? $_POST['orderId'] : "" ;
		$ced_g2a_g2a_details = get_option( 'ced_g2a_config_details', array() );
		if(!empty($ced_g2a_g2a_details))
		{
			$fileName = CED_G2A_PATH."admin/lib/ced-g2a-product-helper.php";
			require_once $fileName;
			$g2aOrder = get_post_meta($order_id,"ced_g2a_order_id",true);
			$Ced_G2A_Product_Helper = new Ced_G2A_Product_Helper;
			$Ced_G2A_Product_Helper_instance = $Ced_G2A_Product_Helper->get_instance();
			$response = $Ced_G2A_Product_Helper_instance->get_orderKey($ced_g2a_g2a_details, $g2aOrder);
			if($response['key'])
			{
				update_post_meta($order_id,"ced_g2a_order_key",$response['key']);
				update_post_meta($orderId,"ced_g2a_order_status",'completed');
				$order = new WC_Order($order_id);
				if(!empty($order))
				{
					$order->update_status('completed');
				}
				echo 'success';wp_die();
			}
			echo $response['message'];wp_die();
		}
	}
	public function order_meta_customized_display( $item_id, $item, $product){
		if(!is_object($product))
		return;
	    $productId = $product->get_id();
		$is_g2a_product = get_post_meta($productId,"ced_g2a_g2a_itemId",true);
            if(!empty($is_g2a_product))
            {
                $link = get_post_meta($productId,'ced_g2a_productData');
                ?>
                
                <a href='https://g2a.com<?php echo $link[0]["slug"] ?>' target="_blank">View on G2A</a>
                
                <?php
                
            }
		}
		
      public function ced_add_column($columns){
        $columns['ced_view_G2A'] = __('View On G2A');
        return $columns;
    }
    
    public function ced_modify_column($column,$post_id){
        if($column == 'ced_view_G2A')
        {
            $is_g2a_product = get_post_meta($post_id,"ced_g2a_g2a_itemId",true);
            if(!empty($is_g2a_product))
            {
                $link = get_post_meta($post_id,'ced_g2a_productData');
                ?>
                
                <a href='https://g2a.com<?php echo $link[0]["slug"] ?>' target="_blank">View</a>
                
                <?php
                
            }
            else{
                ?>
                
                <span>Not a G2A Product</span>
                
                <?php
            }
        }
        
        
    }
    public function ced_g2a_set_scheduler()
    {
        wp_schedule_event(time(), 'ced_g2a_15min', 'ced_g2a_auto_import_cron_job');
        
        wp_schedule_event(time(), 'ced_g2a_15min', 'sync_existing_product');
    }
    
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_G2A_Importer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_G2A_Importer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-g2a-importer-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_G2A_Importer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_G2A_Importer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-g2a-importer-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, "ced_g2a_handler", array( 'ajaxUrl'=>admin_url( 'admin-ajax.php' ), 'ced_g2a_nonce' => wp_create_nonce( "ced-g2a-ajax-nonce" ) ) );

	}

	public function ced_g2a_load_admin_pages(){

		add_menu_page( __( 'G2A','woocommerce-g2a-importer'),__('G2A','woocommerce-g2a-importer'), 'manage_woocommerce', 'ced-g2a-config', array( $this, 'ced_g2a_configuration_display' ), "dashicons-admin-generic", 66 );

		add_submenu_page('ced-g2a-config', __('Configuration','woocommerce-g2a-importer'), __('Configuration','woocommerce-g2a-importer'), 'manage_woocommerce', 'ced-g2a-config', array( $this, 'ced_g2a_configuration_display' ) );
		
		add_submenu_page('ced-g2a-config', __('Settings','woocommerce-g2a-importer'), __('Settings','woocommerce-g2a-importer'), 'manage_woocommerce', 'ced-g2a-settings', array( $this, 'ced_g2a_settings_display' ) );

		add_submenu_page('ced-g2a-config', __('Products','woocommerce-g2a-importer'), __('Products','woocommerce-g2a-importer'), 'manage_woocommerce', 'ced-g2a-products', array( $this, 'ced_g2a_products_display' ) );
		// add_submenu_page('ced-g2a-config', __('Orders','woocommerce-g2a-importer'), __('Orders','woocommerce-g2a-importer'), 'manage_woocommerce', 'ced-g2a-orders', array( $this, 'ced_g2a_orders_display' ) );
		// add_submenu_page('ced-g2a-config', __('Categories to Import','woocommerce-g2a-importer'), __('Categories to Import','woocommerce-g2a-importer'), 'manage_woocommerce', 'ced-g2a-category', array( $this, 'ced_g2a_category_display' ) );
	}

	public function ced_g2a_configuration_display(){

		if( file_exists(CED_G2A_PATH."admin/partials/ced-g2a-configuration.php") )
		{
			require_once CED_G2A_PATH."admin/partials/ced-g2a-configuration.php";
		}
	}

	public function ced_g2a_settings_display(){

		if( file_exists(CED_G2A_PATH."admin/partials/ced-g2a-settings.php") )
		{
			require_once CED_G2A_PATH."admin/partials/ced-g2a-settings.php";
		}
	}

	public function ced_g2a_products_display(){

		if( file_exists(CED_G2A_PATH."admin/partials/ced-g2a-product-listing.php") )
		{
			require_once CED_G2A_PATH."admin/partials/ced-g2a-product-listing.php";
		}
	}

	public function ced_g2a_orders_display(){

		if( file_exists(CED_G2A_PATH."admin/partials/ced-g2a-order-listing.php") )
		{
			require_once CED_G2A_PATH."admin/partials/ced-g2a-order-listing.php";
		}
	}

	public function ced_g2a_category_display(){

		if( file_exists(CED_G2A_PATH."admin/partials/ced-g2a-category-listing.php") )
		{
			require_once CED_G2A_PATH."admin/partials/ced-g2a-category-listing.php";
		}
	}
	
    
    public function ced_g2a_auto_import_cron_job()
    {
		$ced_g2a_g2a_setting_details = get_option( 'ced_g2a_settings', array() );
		
		if($ced_g2a_g2a_setting_details['ced_g2a_enable_auto_import'] != "yes")
			return;
// 		print_r($ced_g2a_g2a_details);
        $fileName = CED_G2A_PATH."admin/lib/ced-g2a-product-helper.php";
		$not_authorised = false;
		if( file_exists($fileName) )
		{

			require_once $fileName;

			$ced_g2a_g2a_details = get_option( 'ced_g2a_config_details', array() );
			$pageNumber = get_option( 'ced_g2a_imported_page_number', "" );
			
			if( empty($pageNumber) )
				$pageNumber = 1;
			else
				$pageNumber = $pageNumber + 1;

// 			print_r( $pageNumber );die("3444");
			if( !empty( $ced_g2a_g2a_details ) )
			{
				$Ced_G2A_Product_Helper = new Ced_G2A_Product_Helper;
				$Ced_G2A_Product_Helper_instance = $Ced_G2A_Product_Helper->get_instance();

				$response = $Ced_G2A_Product_Helper_instance->get_products_from_g2a($ced_g2a_g2a_details, $pageNumber);

//               echo "<pre>"; 
//                 print_r( $response );die("g");
				if( isset( $response['docs'] ) && is_array( $response['docs'] ) && !empty( $response['docs'] ) )
				{
					$products = $response['docs'];
					if( is_array($products) && !empty($products) )
					{
						foreach ($products as $key => $product) {
							// print_r($product);
							update_option( 'ced_g2a_g2a_item_details_'.$product['id'], $product );
							$itemId = $product['id'];
							if( $itemId == "" )
								continue;
							
							$store_product = array();
                			$store_product = get_posts(
                				array(
                					'numberposts' => -1,
                					'post_type'   => 'product',
                					'meta_key' => 'ced_g2a_g2a_itemId',
                					'meta_value' => $itemId,
                					'meta_compare' => '='
                					) 
                				);
                
                			$store_product = wp_list_pluck( $store_product, 'ID' );
                			
                			if( empty($store_product) )
                			{
                			    $response = $Ced_G2A_Product_Helper_instance->get_g2a_item_details($itemId);
                			}
							
							    
						}

						update_option( 'ced_g2a_imported_page_number', $pageNumber );
					}
				}
				else
				{
					update_option( 'ced_g2a_imported_page_number', "" );
				}
			}
			else
			{
				$not_authorised = true;
			}
		}
		die('Ok');
    }
    
	public function ced_g2a_import_to_store() {
		$check_ajax = check_ajax_referer( 'ced-g2a-ajax-nonce', 'check_nonce' );
		if ( $check_ajax ) {

			$method = isset( $_POST['method'] ) ? $_POST['method'] : "single_import";
			if( $method == "single_import" )
			{
				$itemId = isset( $_POST['itemId'] ) ? $_POST['itemId'] : "";
				if( $itemId == "" )
					return "Please Select a Product to Import";

				$fileName = CED_G2A_PATH."admin/lib/ced-g2a-product-helper.php";
				if( !file_exists($fileName) )
					return "Missing Helper Class";

				require_once $fileName;
				$Ced_G2A_Product_Helper = new Ced_G2A_Product_Helper;
				$Ced_G2A_Product_Helper_instance = $Ced_G2A_Product_Helper->get_instance();

				$response = $Ced_G2A_Product_Helper_instance->get_g2a_item_details($itemId);
			}
			else if( $method == "bulk_import" )
			{
				$itemIds = isset( $_POST['itemId'] ) ? $_POST['itemId'] : array();
				$itemIds = array_values($itemIds);
				if( !empty( $itemIds ) )
				{
					foreach ($itemIds as $key => $itemId) {
						$fileName = CED_G2A_PATH."admin/lib/ced-g2a-product-helper.php";
						if( !file_exists($fileName) )
							return "Missing Helper Class";

						require_once $fileName;
						$Ced_G2A_Product_Helper = new Ced_G2A_Product_Helper;
						$Ced_G2A_Product_Helper_instance = $Ced_G2A_Product_Helper->get_instance();

						$response = $Ced_G2A_Product_Helper_instance->get_g2a_item_details($itemId);
					}
				}
			}
		}
	}

	public function ced_g2a_sync_products_cron_job(){
		$ced_g2a_g2a_details = get_option( 'ced_g2a_settings', array() );
		
		if($ced_g2a_g2a_details['ced_g2a_enable_price_sync'] != "yes")
			return;

		ini_set( 'max_execution_time', -1 );
		$store_products = array();
		$store_products = get_posts(
			array(
				'numberposts' => -1,
				'post_type'   => 'product',
				'meta_key' => 'ced_g2a_g2a_itemId',
				'meta_compare' => 'EXISTS'
				) 
			);

		$store_products = wp_list_pluck( $store_products, 'ID' );

		if( is_array( $store_products ) && !empty($store_products) )
		{
			foreach ($store_products as $key => $productId) {

				$gunBrokerId = get_post_meta( $productId, 'ced_g2a_g2a_itemId', true );
				$details = array(
					'gunBrokerId' => $gunBrokerId,
					'wooProductId' => $productId
				);
				$ced_g2a_settings = get_option( 'ced_g2a_settings', array() );

				$fileName = CED_G2A_PATH."admin/lib/ced-g2a-product-helper.php";
				if( !file_exists($fileName) )
					return "Missing Helper Class";

				require_once $fileName;
				$Ced_G2A_Product_Helper = new Ced_G2A_Product_Helper;
				$Ced_G2A_Product_Helper_instance = $Ced_G2A_Product_Helper->get_instance();
				
				$itemDetails = $Ced_G2A_Product_Helper_instance->get_item_data($gunBrokerId);
				$itemDetails = isset( $itemDetails['docs'] ) ? $itemDetails['docs'][0] : array();

				update_option( 'ced_g2a_g2a_item_details_'.$gunBrokerId, $itemDetails );

				$price = $this->setProductPrice( $productId, $itemDetails['minPrice'], $ced_g2a_settings );

				update_post_meta( $productId, '_price', $price );
				update_post_meta( $productId, '_regular_price', $price );
			
				if( isset( $itemDetails['qty'] ) && $itemDetails['qty'] > 0 )
				{
					update_post_meta( $productId, '_stock', $itemDetails['qty'] );
					update_post_meta( $productId, '_stock_status', "instock" );
				}
				else
				{
					update_post_meta( $productId, '_stock_status', "outofstock" );
				}
				update_post_meta( $productId, 'ced_g2a_productData', $itemDetails );
			}
		}
		die('Ok');
	}

	public function setProductPrice( $productId = "", $price = "", $ced_g2a_settings = array() )
	{
		if( empty($productId) )
			return ;

		if( isset( $ced_g2a_settings['ced_g2a_price_markup_type'] ) && !empty( $ced_g2a_settings['ced_g2a_price_markup_type'] ) )
		{
			$markup_type = $ced_g2a_settings['ced_g2a_price_markup_type'] ;
			if( $markup_type == "flat" && isset( $ced_g2a_settings['ced_g2a_price_markup_value'] ) && !empty( $ced_g2a_settings['ced_g2a_price_markup_value'] ) )
			{
				$price = $price + $ced_g2a_settings['ced_g2a_price_markup_value'];
				return $price;
			}
			else if(  $markup_type == "percent" && isset( $ced_g2a_settings['ced_g2a_price_markup_value'] ) && !empty( $ced_g2a_settings['ced_g2a_price_markup_value'] )  )
			{
				$price = $price + ( $ced_g2a_settings['ced_g2a_price_markup_value']*$price/100 ) ;
				return $price;
			}
		}
		return $price;
	}

}