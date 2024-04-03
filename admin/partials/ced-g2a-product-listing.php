<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://cedcommerce.com
 * @since      1.0.0
 *
 * @package    Woocommerce_GunBroker_Importer
 * @subpackage Woocommerce_GunBroker_Importer/admin/partials
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if( isset( $_POST['ced_g2a_nextPage'] ) ){
    $pageNumber = isset($_POST['ced_g2a_pageNumber']) ? $_POST['ced_g2a_pageNumber'] : 1;
}
else if( isset( $_POST['ced_g2a_previousPage'] ) ){
    $pageNumber = isset($_POST['ced_g2a_pageNumber']) ? $_POST['ced_g2a_pageNumber'] -2 : 1;
    if( $pageNumber <= 0 )
        $pageNumber = 1;
}
else if(isset($_POST['ced_g2a_text_field'])){
	$pageNumber = isset($_POST['ced_g2a_text_field']) ? $_POST['ced_g2a_text_field'] : 1;
	$_GET['cursor']=$pageNumber;
	// wp_safe_redirect( admin_url( 'admin.php?page=wc-settings&tab=advanced&section=webhooks' . $status . '&deleted=' . $qty ) );
	// wp_safe_redirect( admin_url( 'admin.php?page=wc-settings&tab=advanced&section=webhooks' . $status . '&deleted=' . $qty ) );
	wp_safe_redirect( ( admin_url('admin.php?page=ced-g2a-products&section=product_log_view&cursor=' . $pageNumber) ))  ;
}
else{
    $pageNumber = isset($_GET['cursor'])?$_GET['cursor']:1;
}
$fileName = CED_G2A_PATH."admin/lib/ced-g2a-product-helper.php";
$not_authorised = false;
if( file_exists($fileName) )
{
	require_once $fileName;

	$ced_g2a_g2a_details = get_option( 'ced_g2a_config_details', array() );
	if( !empty( $ced_g2a_g2a_details ) )
	{
		$Ced_G2A_Product_Helper = new Ced_G2A_Product_Helper;
		$Ced_G2A_Product_Helper_instance = $Ced_G2A_Product_Helper->get_instance();
		//var_dump($pageNumber);
		$response = $Ced_G2A_Product_Helper_instance->get_products_from_g2a($ced_g2a_g2a_details, $pageNumber);
		$response_all_product = $Ced_G2A_Product_Helper_instance->get_all_products_from_g2a($ced_g2a_g2a_details, $pageNumber);

		if( isset( $response['docs'] ) && is_array( $response['docs'] ) && !empty( $response['docs'] ) )
		{
			$products = $response['docs'];
		}
		if( isset( $response_all_product['docs'] ) && is_array( $response_all_product['docs'] ) && !empty( $response_all_product['docs'] ) )
		{
			$all_product_count = $response_all_product['docs'];
		}
		$total_products=$response_all_product['total'];
	}
	else
	{
		$not_authorised = true;
	}
}
// echo "<pre>";
// print_r($products[0]);
// die;

if( $not_authorised )
{
	?>
	<div id="message" class="updated notice notice-warning is-dismissible">
		<p><?php _e( 'You need to Authorize your G2A Account.', 'woocommerce-g2a-importer' ); ?></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'siroop-woocommerce-order-automation' ); ?></span>
		</button>
	</div>
	<?php
}
else
{
	?>

	<div id="ced-g2a-loader" class="loading-style-bg ced-g2a-loader">
		<img src="<?php echo CED_G2A_URL.'admin/images/BigCircleBall.gif' ?>">
	</div>
	<div class="ced-g2a-content-wrapper">

		<div class="ced-g2a-heading-wrapper ced-g2a-product-heading-wrapper">
			<h2><?php _e( 'G2A Products', 'woocommerce-g2a-importer' ); ?></h2>
		</div>
		<div class="ced-g2a-settings-wrapper ced-g2a-g2a-products-wrapper">
			<div class="ced-g2a-bulk-actions alignleft actions bulkactions">
				<label for="bulk-action-selector-top" class="screen-reader-text"><?php _e( 'Select bulk action', 'woocommerce-g2a-importer' ); ?></label>
				<select name="action" id="bulk-action-selector-top">
					<option value="-1"><?php _e( 'Bulk Actions', 'woocommerce-g2a-importer' ); ?></option>
					<option value="import-to-store"><?php _e( 'Import', 'woocommerce-g2a-importer' ); ?></option>
				</select>
				<input id="doaction" name="ced_g2a_bulk_import_submit" class="ced_g2a_bulk_import_submit button action" value="<?php _e( 'Apply', 'woocommerce-g2a-importer' ) ?>" type="submit">
			</div>
			<div class="ced-g2a-g2a-products-table-wrapper">
				<table class="ced-g2a-g2a-products-table">
					<thead>
						<tr>
							<th id="cb" class="manage-column column-cb check-column">
								<input type="checkbox"></input>
							</th>
							<th scope="col" id="product_image" class="manage-column column-product_image column-primary">
								<?php _e( "Image", 'woocommerce-g2a-importer' ); ?>
							</th>
							<th scope="col" id="product_name" class="manage-column column-product_name">
								<?php _e( 'Product Name', 'woocommerce-g2a-importer' ); ?>
							</th>
							<th scope="col" id="product_price" class="manage-column column-product_price">
								<?php _e( 'Product Price', 'woocommerce-g2a-importer' ); ?>
							</th>
							<th scope="col" id="product_quantity" class="manage-column column-product_quantity">
								<?php _e( 'Product Quantity', 'woocommerce-g2a-importer' ); ?>
							</th>
							<th scope="col" id="product_view_link" class="manage-column column-product_view_link">
								<?php _e( 'View On G2A', 'woocommerce-g2a-importer' ); ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						if( is_array($products) && !empty($products) )
						{
							foreach ($products as $key => $product) {
								// print_r($product);
								update_option( 'ced_g2a_g2a_item_details_'.$product['id'], $product );
								?>
								<tr>
									<td class="cb check-column column-cb" data-colname="cb">
										<?php 
										$store_product = array();
										$store_product = get_posts(
											array(
												'numberposts' => -1,
												'post_type'   => 'product',
												'meta_key' => 'ced_g2a_g2a_itemId',
												'meta_value' => $product['id'],
												'meta_compare' => '='
												) 
											);

										$store_product = wp_list_pluck( $store_product, 'ID' );
										if( !empty( $store_product ) ){
											?>
											<img style="margin-top:15px;margin-left:10px;" class="ced_g2a_already_imported_product_image" src="<?php echo CED_G2A_URL.'admin/images/check.png'; ?>" width="50%">
											<?php
										}
										else
										{
											?>
											<input type="checkbox" class="ced_g2a_select_product_for_import" value="<?php echo $product['id']; ?>"></input>
											<?php
										} 
										?>
									</td>
									<td>
										<img src="<?php echo $product['thumbnail']; ?>" width="60" height="60">
									</td>
									<td>
										<?php echo $product['name']; ?>
										<div class='row-actions'>
											<span class='edit'>
												<a href='javascript:void(0);' data-itemid ="<?php echo $product['id']; ?>" class='ced_g2a_import_to_store'><?php _e( 'Import', 'woocommerce-g2a-importer'  ); ?></a>
											</span>
										</div>
									</td>
									<td>
										<?php echo wc_price( $product['minPrice'] ); ?>
									</td>
									<td>
										<?php echo $product['qty']; ?>
									</td>
									<td>
										<a href="<?php echo "https://g2a.com".$product['slug']; ?>" target="blank"><?php _e( 'View', 'woocommerce-g2a-importer' ); ?></a>
									</td>
								</tr>
								<?php
							}
						}
						else
						{
							?>
							<tr>
								<td colspan="5">
									<?php _e( 'No Products to Import', 'woocommerce-g2a-importer' ); ?>
								</td>
							</tr>
							<?php
						}
						?>
					</tbody>
					<tfoot>
						<tr>
							<th>
								<input type="checkbox"></input>
							</th>
							<th>
								<?php _e( "Image", 'woocommerce-g2a-importer' ); ?>
							</th>
							<th>
								<?php _e( 'Product Name', 'woocommerce-g2a-importer' ); ?>
							</th>
							<th>
								<?php _e( 'Product Price', 'woocommerce-g2a-importer' ); ?>
							</th>
							<th>
								<?php _e( 'Product Quantity', 'woocommerce-g2a-importer' ); ?>
							</th>
						</tr>
					</tfoot>
				</table>
				
						<?php 
						
			echo "<div class=ced_g2a-pagination>";

			$count = 0;
			$per_page = 25;
			$last_offset =(int) ceil($total_products / $per_page);

			$previous = (isset($_GET['cursor']) && $_GET['cursor'] !=2 ) ? ( admin_url('admin.php?page=ced-g2a-products&section=product_log_view&cursor=' . ($_GET['cursor']-1)) ) :  admin_url('admin.php?page=ced-g2a-products&section=product_log_view')  ;
			$next = isset($_GET['cursor']) ? ( admin_url('admin.php?page=ced-g2a-products&section=product_log_view&cursor=' . ($_GET['cursor']+1)) ) :  admin_url('admin.php?page=ced-g2a-products&section=product_log_view&cursor='.($pageNumber+1))  ;

			echo "<span><a class='button ced_cin_nav_buttons' ".(!isset($_GET['cursor'])  ? 'style="display:none;"' : '')." href='".$previous."'><< Prev</a></span>";
			?>
			<form action="" method="post">
			<input type="text" value="<?php echo $pageNumber;?>" size=8 name="ced_g2a_text_field" class="current-page">
			</form >
			<?php
			echo "<span><a class='button ced_cin_nav_buttons' ".((isset($_GET['cursor']) && $_GET['cursor'] == $last_offset )  ? 'style="display:none;"' : '')." href='".$next."'>Next >></a></span>";
			echo "<span class='ced_cin_nav_pages'>Page ".( isset($_GET['cursor']) ? $_GET['cursor'] : '1' )." of ".$last_offset."</span>";
			echo "</div>";
                        ?>

            <!-- </div> -->
                </form>
				<?php 
				// global $wpdb;
				// $userid=get_current_user_id();
				// $orders_post_id = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE `meta_key`=%s AND `meta_value`=%d", '_is_ced_ebay_order', 1 ), 'ARRAY_A' );
				// $total_order=count($orders_post_id);
				// //$numorders = wc_get_customer_order_count( $userid );
				// var_dump($total_products);
				// die;
				$totalpage=ceil($total_products/20);
				//$current=5;
				// echo '<pre>';
				// print_r($totalpage);
				// die;
				
				
			$i=1;
            if($_GET['pageno']+1<=$totalpage)
            $next=$_GET['pageno']+1;
            if($_GET['pageno']-1>0)
            $pre=$_GET['pageno']-1;

            if($_GET['pageno']==1)
            $disable_prev="disabled";
            else
            $disable_prev='';
            if($_GET['pageno']==$totalpage)
            $disable_next="disabled";
            else
			$disable_next='';
			



$total   = isset( $totalpage ) ? $totalpage : wc_get_loop_prop( 'total_pages' );
$current = isset( $current ) ? $current : wc_get_loop_prop( 'current_page' );
$base    = isset( $base ) ? $base : esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) );
$format  = isset( $format ) ? $format : '';

// if ( $total <= 1 ) {
// 	return;
// }
?>
<!-- <nav class="woocommerce-pagination">
	<?php
	echo paginate_links(
		apply_filters(
			'woocommerce_pagination_args',
			array( // WPCS: XSS ok.
				'base'      => $base,
				'format'    => $format,
				'add_args'  => false,
				'current'   => max( 1, $current ),
				'total'     => $total,
				'prev_text' => '&larr;',
				'next_text' => '&rarr;',
				'type'      => 'p',
				'end_size'  => 3,
				'mid_size'  => 3,
			)
		)
	);
	?>
</nav> -->
	
			</div>
		</div>

	</div>
	<?php
}
?>