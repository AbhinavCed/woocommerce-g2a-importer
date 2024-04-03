<?php
require_once ('../../../../wp-blog-header.php');

if ( ! defined( 'ABSPATH' ) ) {
	die;
}
/**
 * Cron to fetch order and auto acknowledge
 *
 * @class    Class_CED_G2A_Sycn_Products
 * @version  1.0.0
 * @category Class
 * @author   CedCommerce
 */

class Class_CED_G2A_Sycn_Products{

	public function __construct(){

		do_action('ced_g2a_sync_products_cron_job');
	}
}
$ced_g2a_sync_cron_obj =	new Class_CED_G2A_Sycn_Products();
?>