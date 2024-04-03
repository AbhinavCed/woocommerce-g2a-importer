<?php  

global $wpdb;
$limit = 1;  
if (isset($_GET["pageno"])) { $page  = $_GET["pageno"]; } else { $page=1; };  
$start_from = ($page-1) * $limit;  
 

$Orders = $wpdb->get_results($wpdb->prepare( "SELECT `post_id` FROM $wpdb->postmeta WHERE `meta_key`='ced_g2a_order_id' group by `post_id` LIMIT $start_from, $limit", 1 ),'ARRAY_A' );
?>  
<table class="table table-bordered table-striped">  
<thead>  
<tr>  
<th><?php _e( "Woo Order Id", 'woocommerce-g2a-importer' ); ?></th>
<th><?php _e( 'G2A Order Id', 'woocommerce-g2a-importer' ); ?></th>
<th><?php _e( 'G2A Order Status', 'woocommerce-g2a-importer' ); ?></th>
<th><?php _e( 'Action', 'woocommerce-g2a-importer' ); ?></th>
</tr>  
<thead>  
<tbody>  

<?php
	foreach ($Orders as $key => $OrderId) {
		echo '<tr>';
		$order_id = $OrderId['post_id'];
		$g2aorderid = get_post_meta($order_id,"ced_g2a_order_id",true);
		$g2astatus = get_post_meta($order_id,"ced_g2a_order_status",true);
		echo '<td>'.$order_id.'</td>';
		echo '<td>'.$g2aorderid.'</td>';
		echo '<td>'.$g2astatus.'</td>';
		echo '<td>edit</td>';
		echo '</tr>';
	}
?> 
</tbody>  
</table>  
<?php    

$Orders_count = $wpdb->get_results($wpdb->prepare( "SELECT `post_id` FROM $wpdb->postmeta WHERE `meta_key`='ced_g2a_order_id' group by `post_id`", 1 ),'ARRAY_A' );
$total_records = count($Orders_count);  
$total_pages = ceil($total_records / $limit);  
$pagLink = "<div class='pagination'>";  
for ($i=1; $i<=$total_pages; $i++) {  
             $pagLink .= "<a href=".admin_url()."admin.php?page=ced-g2a-orders&pageno=".$i.">".$i."</a>";  
};  
echo $pagLink . "</div>";  
?>

<!-- 
<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
global $wpdb;
$Orders = $wpdb->get_results($wpdb->prepare( "SELECT `post_id` FROM $wpdb->postmeta WHERE `meta_key`='ced_g2a_order_id' group by `post_id`", 1 ),'ARRAY_A' );
?>


<div>
	<div class="ced_g2a_order_heading">
		<h2><?php _e( 'Orders', 'woocommerce-g2a-importer' ); ?></h2>
	</div>
	<div class="ced_g2a_order_table_wrapper">
		<table>
			<thead>
				<th><?php _e( "Woo Order Id", 'woocommerce-g2a-importer' ); ?></th>
				<th><?php _e( 'G2A Order Id', 'woocommerce-g2a-importer' ); ?></th>
				<th><?php _e( 'G2A Order Status', 'woocommerce-g2a-importer' ); ?></th>
				<th><?php _e( 'Edit', 'woocommerce-g2a-importer' ); ?></th>
			</thead>
			<tbody>
				<?php
					foreach ($Orders as $key => $OrderId) {
					echo '<tr>';
						$order_id = $OrderId['post_id'];
						$g2aorderid = get_post_meta($order_id,"ced_g2a_order_id",true);
						$g2astatus = get_post_meta($order_id,"ced_g2a_order_status",true);
						echo '<td>'.$order_id.'</td>';
						echo '<td>'.$g2aorderid.'</td>';
						echo '<td>'.$g2astatus.'</td>';
						echo '<td>edit</td>';
					echo '</tr>';
					}
				?>
			</tbody>
		</table>
	</div>
</div> -->