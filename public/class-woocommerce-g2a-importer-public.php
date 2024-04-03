<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://cedcommerce.com
 * @since      1.0.0
 *
 * @package    woocommerce_g2a_importer
 * @subpackage woocommerce_g2a_importer/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    woocommerce_g2a_importer
 * @subpackage woocommerce_g2a_importer/public
 * @author     CedCommerce <plugins@cedcommerce.com>
 */
class Woocommerce_G2A_Importer_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_filter( 'woocommerce_account_orders_columns',array($this, 'add_account_orders_column'), 10, 1 );
		add_action( 'woocommerce_my_account_my_orders_column_G2A-keys',array($this,'add_account_orders_column_rows') );
		add_action( 'wp_ajax_ced_display_g2a_key', array( $this, 'ced_display_g2a_key' ) );
	}


	public function ced_display_g2a_key(){
		$orderid=$_POST['orderid'];
		$g2a_tokn=get_post_meta($orderid,'ced_g2a_order_key',true);
		print_r($g2a_tokn);
		die;
	}
	function add_account_orders_column( $columns ){
		$columns['G2A-keys'] = __( 'G2A key', 'woocommerce' );
	
		return $columns;
	}
	
	
	function add_account_orders_column_rows( $order ) {
		// print_r($order);
		// die('gg');
		// Example with a custom field
		$id=$order->get_id();
		if ( $value = $order->get_meta( 'ced_g2a_order_key' ) ) {
			echo '<input type="button" value="Get Key" data-orderid='.$id.' class="woocommerce-button button wcfm-support-action ced_get_g2akey">';
		}

echo '<div id="myModal" class="modal">

  <!-- Modal content -->
  <div class="modal-content">
    <span class="close">&times;</span>
    <p><b>Your G2A Key is:- </b></p><p id="ced_modal_text"></p>
  </div>

</div>';
	}
	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-g2a-importer-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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
		
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-g2a-importer-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, "ced_g2a_handler", array( 'ajaxUrl'=>admin_url( 'admin-ajax.php' ), 'ced_g2a_nonce' => wp_create_nonce( "ced-g2a-ajax-nonce" ) ) );
	}

}
