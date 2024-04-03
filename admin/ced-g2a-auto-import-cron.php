<?php
require_once ('../../../../wp-blog-header.php');

if ( ! defined( 'ABSPATH' ) ) {
	die;
}
/**
 * Cron to fetch order and auto acknowledge
 *
 * @class    Class_CED_G2A_Auto_Import
 * @version  1.0.0
 * @category Class
 * @author   CedCommerce
 */

class Class_CED_G2A_Auto_Import{

	public function __construct(){

		do_action('ced_g2a_auto_import_cron_job');
	}
}
$ced_g2a_auto_Import_cron_obj =	new Class_CED_G2A_Auto_Import();
?>