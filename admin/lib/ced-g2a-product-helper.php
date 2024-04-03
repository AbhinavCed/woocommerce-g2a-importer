<?php
if(!class_exists('Ced_G2A_Product_Helper')){

	class Ced_G2A_Product_Helper{

		private static $_instance;
		/**
		 * get_instance Instance.
		 *
		 * Ensures only one instance of Ced_G2A_Product_Helper is loaded or can be loaded.
		 *
		 * @author CedCommerce <plugins@cedcommerce.com>
		 * @since 1.0.0
		 * @static
		 * @return get_instance instance.
		 */
		public static function get_instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/*public function __construct(){

		}*/

		public function get_products_from_g2a($ced_g2a_g2a_details = array(), $pageNumber = ""){

			if( empty($ced_g2a_g2a_details) )
				return ;

			if( $pageNumber == "" )
				$pageNumber = 1;
			
			$ced_g2a_sellerEmail = isset($ced_g2a_g2a_details['ced_g2a_sellerEmail']) ? $ced_g2a_g2a_details['ced_g2a_sellerEmail'] : "";
			$endpointUrl = ced_g2a_getEndpointUrl( $ced_g2a_g2a_details );

			$endpointUrl = $endpointUrl."products?page=$pageNumber";

			$response = $this->sendHttpRequest( $ced_g2a_g2a_details, $endpointUrl );
			return $response;
		}

		public function get_all_products_from_g2a($ced_g2a_g2a_details = array(), $pageNumber = ""){
			if( empty($ced_g2a_g2a_details) )
			return ;

			// if( $pageNumber == "" )
			// $pageNumber = 1;		
			$ced_g2a_sellerEmail = isset($ced_g2a_g2a_details['ced_g2a_sellerEmail']) ? $ced_g2a_g2a_details['ced_g2a_sellerEmail'] : "";
			$endpointUrl = ced_g2a_getEndpointUrl( $ced_g2a_g2a_details );

			$endpointUrl = $endpointUrl."products?page=$pageNumber";

			$response = $this->sendHttpRequest( $ced_g2a_g2a_details, $endpointUrl );
	
			return $response;
		}
		public function add_g2a_order($ced_g2a_g2a_details = array(), $productIdsToadd = array()){

			if( empty($ced_g2a_g2a_details) )
				return ;

			$ced_g2a_sellerEmail = isset($ced_g2a_g2a_details['ced_g2a_sellerEmail']) ? $ced_g2a_g2a_details['ced_g2a_sellerEmail'] : "";
			$endpointUrl = ced_g2a_getEndpointUrl( $ced_g2a_g2a_details );

			$endpointUrl = $endpointUrl."order";
			$parameter["product_id"] = $productIdsToadd[0];
			$response = $this->sendHttpRequestPost( $ced_g2a_g2a_details, $endpointUrl, $parameter );
			return $response;
		}
		public function get_g2a_order_status($ced_g2a_g2a_details = array(), $order_id =""){

			if( empty($ced_g2a_g2a_details) )
				return ;

			$ced_g2a_sellerEmail = isset($ced_g2a_g2a_details['ced_g2a_sellerEmail']) ? $ced_g2a_g2a_details['ced_g2a_sellerEmail'] : "";
			$endpointUrl = ced_g2a_getEndpointUrl( $ced_g2a_g2a_details );

			$endpointUrl = $endpointUrl."order/details";
			$parameter["id"] = $order_id;
			$response = $this->sendHttpRequestPost( $ced_g2a_g2a_details, $endpointUrl, $parameter );
			return $response;
		}

		public function pay_order($ced_g2a_g2a_details = array(), $order_id =""){

			if( empty($ced_g2a_g2a_details) )
				return ;

			$ced_g2a_sellerEmail = isset($ced_g2a_g2a_details['ced_g2a_sellerEmail']) ? $ced_g2a_g2a_details['ced_g2a_sellerEmail'] : "";
			$endpointUrl = ced_g2a_getEndpointUrl( $ced_g2a_g2a_details );

			$endpointUrl = $endpointUrl."order/pay/".$order_id;
			//$parameter["id"] = $order_id;
			$response = $this->putsendHttpRequest( $ced_g2a_g2a_details, $endpointUrl );
			return $response;
		}
		public function get_orderKey($ced_g2a_g2a_details = array(), $order_id =""){

			if( empty($ced_g2a_g2a_details) )
				return ;

			$ced_g2a_sellerEmail = isset($ced_g2a_g2a_details['ced_g2a_sellerEmail']) ? $ced_g2a_g2a_details['ced_g2a_sellerEmail'] : "";
			$endpointUrl = ced_g2a_getEndpointUrl( $ced_g2a_g2a_details );

			$endpointUrl = $endpointUrl."order/key/".$order_id;
			$parameter["id"] = $order_id;
			$response = $this->sendHttpRequest( $ced_g2a_g2a_details, $endpointUrl, $parameter);
		
			return $response;
		}
		
		public function sendHttpRequest($ced_g2a_g2a_details, $endpointUrl, $requestBody = array()){

			$header = $this->prepareHeader($ced_g2a_g2a_details);
			$connection = curl_init();

			curl_setopt($connection, CURLOPT_URL, $endpointUrl);
			curl_setopt($connection, CURLOPT_HTTPHEADER, $header);
			//stop CURL from verifying the peer's certificate
			curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
			
			curl_setopt($connection, CURLOPT_CUSTOMREQUEST, "GET");
			// curl_setopt($connection, CURLOPT_POSTFIELDS, json_encode($requestBody));
			curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
	        
			// print_r($connection);

			$response = curl_exec($connection);
			curl_close($connection);
			return $this->ParseResponse($response);
		}
		
		public function putsendHttpRequest($ced_g2a_g2a_details, $endpointUrl, $requestBody = array()){

			$header = $this->prepareHeader($ced_g2a_g2a_details);
			$connection = curl_init();

			curl_setopt($connection, CURLOPT_URL, $endpointUrl);
			curl_setopt($connection, CURLOPT_HTTPHEADER, $header);
			//stop CURL from verifying the peer's certificate
			curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
			
			curl_setopt($connection, CURLOPT_CUSTOMREQUEST, "PUT");
			curl_setopt($connection, CURLOPT_POSTFIELDS, json_encode($requestBody));
			curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
	        
			// print_r($connection);

			$response = curl_exec($connection);
			print_r( curl_error( $connection ) );
			curl_close($connection);
			return $this->ParseResponse($response);
		}

		public function sendHttpRequestPost($ced_g2a_g2a_details, $endpointUrl, $parameter = array()){

			$header = $this->prepareHeader($ced_g2a_g2a_details);
			$connection = curl_init();
			curl_setopt($connection, CURLOPT_URL, $endpointUrl);
			curl_setopt($connection, CURLOPT_HTTPHEADER, $header);
			//stop CURL from verifying the peer's certificate
			curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
			//set method as POST
			curl_setopt($connection, CURLOPT_POST, 1);
			curl_setopt($connection, CURLOPT_POSTFIELDS, json_encode($parameter));
			curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec($connection);
			// print_r($response);
			// die("111");
			curl_close($connection);
			return $this->ParseResponse($response);
		}

		public function prepareHeader( $ced_g2a_g2a_details = array() )
	 	{
	 		$apiHash = isset( $ced_g2a_g2a_details['ced_g2a_hashKey'] ) ? $ced_g2a_g2a_details['ced_g2a_hashKey'] : "";
	 		$apiSecret = isset( $ced_g2a_g2a_details['ced_g2a_secretKey'] ) ? $ced_g2a_g2a_details['ced_g2a_secretKey'] : "";
	 		$g2aEmail = isset( $ced_g2a_g2a_details['ced_g2a_sellerEmail'] ) ? $ced_g2a_g2a_details['ced_g2a_sellerEmail'] : "";


	 		$apiKey = hash('sha256', $apiHash . $g2aEmail . $apiSecret);

	 		$header = array(
		        "Authorization: $apiHash, $apiKey",
		        "Content-Type: application/json",
		    );
			return $header;
	 	}

		public function ParseResponse($response) {

			if( !empty( $response ) ) {
				return json_decode( $response, true );
			}
		}

		public function get_g2a_item_details( $itemId = "" ) {

			if( $itemId == "" ) {
				return "Please select a product to import";
			}

			$itemData = $this->get_item_data( $itemId );

			if( is_array( $itemData ) && !empty( $itemData ) ){
				$this->create_product_on_woo_store( $itemId, $itemData );
			}
		}

		public function get_item_data($itemId) {

			$ced_g2a_g2a_details	= get_option( 'ced_g2a_config_details', array() );
			$endpointUrl 			= ced_g2a_getEndpointUrl( $ced_g2a_g2a_details );
			$endpointUrl 			= $endpointUrl."products?id=$itemId";
			$response 				= $this->sendHttpRequest( $ced_g2a_g2a_details, $endpointUrl );
			return $response;
		}

		public function create_product_on_woo_store( $itemId, $itemData = array() ) {
			
			$itemData 			= isset( $itemData['docs'] ) ? $itemData['docs'][0] : array();
			$included_category 	= get_option("included_category",true);
			$itemDetails 		= get_option( 'ced_g2a_g2a_item_details_'.$itemId, array() );
			$allowed_category 	= false;
			$alreadyCreated 	= false;
			$store_product 		= array();
			$store_product 		= get_posts(
			array(
				'numberposts' => -1,
				'post_type'   => 'product',
				'meta_key' => 'ced_g2a_g2a_itemId',
				'meta_value' => $itemId,
				'meta_compare' => '='
				) 
			);

			$store_product 		= wp_list_pluck( $store_product, 'ID' );
			$product_state 		= "publish";
			$ced_g2a_settings 	= get_option( 'ced_g2a_settings', array() );

			if( isset( $ced_g2a_settings['ced_g2a_product_state'] ) && $ced_g2a_settings['ced_g2a_product_state'] != "" ) {
				$product_state = $ced_g2a_settings['ced_g2a_product_state'];
			}
			
			if( empty( $store_product ) ) {

				$productId = wp_insert_post( array(
				    'post_title' => isset($itemData['name']) ? $itemData['name'] : $itemData['name'] ,
				    'post_status' => $product_state,
				    'post_type' => "product",
				    'post_content'=> "" ,
				) );

				$description = 	$itemData['description'];

				if(isset($itemData['region']) && !empty($itemData['region'])) {
					$this->ced_g2a_createAttributes($productId,'Region',$itemData['region']);
					$description .= "<p><span>Region : </span><span>".$itemData['region']."</span></p>";
				}

				if(isset($itemData['developer']) && !empty($itemData['developer'])) {
					$description .= "<p><span>Developer : </span><span>".$itemData['developer']."</span></p>";
				}

				if(isset($itemData['publisher']) && !empty($itemData['publisher'])) {
					$description .= "<p><span>Publisher : </span><span>".$itemData['publisher']."</span></p>";
				}

				if(isset($itemData['platform']) && !empty($itemData['platform'])) {
					if($itemData['platform'] != "Other") {
						$this->ced_g2a_createAttributes($productId,'Platform',$itemData['platform']);
					}
					$description .= "<p><span>PlatForm : </span><span>".$itemData['platform']."</span></p>";
				}

				if(isset($itemData['restrictions']) && !empty($itemData['restrictions'])) {
					foreach ($itemData['restrictions'] as $key => $value) {
						$description1 = "";
						if(!empty($value)) {
							$description1 .="<li>".$key." : ".$value."</li>"; 
						}
					}
					if(!empty($description1)) {
						$description .= "<p><span>Restrictions</span><ul>";
						$description .= $description1;
						$description .= "</ul></p>";
					}
				}

				if(isset($itemData['requirements']['minimal']) && !empty($itemData['requirements']['minimal'])) {
					
					foreach ($itemData['requirements']['minimal'] as $key => $value) {
						if(!empty($value)) {
							$description2 .="<li>".$key." : ".$value."</li>"; 
						}
					}
					if(!empty($description2)) {
						$description .= "<p><span>Requirements (Minimum)</span><ul>";
						$description .= $description2;
						$description .= "</ul></p>";
					}
				}

				if(isset($itemData['requirements']['recommended']) && !empty($itemData['requirements']['recommended'])) {
					
					foreach ($itemData['requirements']['recommended'] as $key => $value) {
						if(!empty($value)) {
							$description3 .="<li>".$key." : ".$value."</li>"; 
						}
					}
					if(!empty($description3)) {
						$description .= "<p><span>Requirements (Recommended)</span><ul>";
						$description .= $description3;
						$description .= "</ul></p>";
					}
				}

				if(isset($itemData['videos']) && !empty($itemData['videos'])) {
					$description .= "<p><span>Video URL : </span><span>".$itemData['videos'][0]['url']."</span></p>";
				}
				global $wpdb;
				$wpdb->update( $wpdb->prefix."posts", array('post_content'=>$description), array('ID'=>$productId) );
			}
			else {
				foreach ($store_product as $key => $value) {
					$g2aId = get_post_meta( $value, 'ced_g2a_g2a_itemId', true );
					if( $g2aId == $itemId ){
						$productId = $value;
						$alreadyCreated = true;
						break;
					}
				}
			}

			if( !$productId ) {
				return array( 'status' => "201", 'message' => 'Product Not Created' );
			}

			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);

			// Main Image
			$images = isset( $itemData["coverImage"] ) ? $itemData["coverImage"] : $itemData["smallImage"];
			if( !empty( $images ) ) {
				$main_image_id = $this->ced_g2a_InsertProductImage( $productId, $images );
				if( $main_image_id != "" ) {
					set_post_thumbnail( $productId, $main_image_id );
				}
			}

			// Gallery Image
			$imageUrlArray[] = isset( $itemData["images"] ) ? $itemData["images"] : $itemData["images"];
			foreach ( $imageUrlArray[0] as $key => $value ) {
				$image_ids[] = $this->ced_g2a_InsertProductImage( $productId, $value );
			}
			if ( ! empty( $image_ids ) ) {
				update_post_meta( $productId, '_product_image_gallery', implode( ',', $image_ids ) );
			}

			update_post_meta( $productId, 'ced_g2a_productData', $itemData );
			update_post_meta( $productId, '_visibility', 'visible' );
			update_post_meta( $productId, '_virtual', 'yes' );

			if( isset( $itemData['sku'] ) && $itemData['sku'] != "" )
				update_post_meta( $productId, '_sku', $itemData['sku'] );
			else
				update_post_meta( $productId, '_sku', $itemData['id'] );
			
			if( isset( $itemData['mfgPartNumber'] ) && $itemData['mfgPartNumber'] != "" )
				update_post_meta( $productId, 'ced_g2a_mpn', $itemData['mfgPartNumber'] );

			if( isset( $itemData['upc'] ) && $itemData['upc'] != "" )
				update_post_meta( $productId, 'ced_g2a_upc', $itemData['upc'] );

			update_post_meta( $productId, 'ced_g2a_g2a_itemId', $itemData['id'] );
			update_post_meta( $productId, '_manage_stock', "yes" );

			if(isset($itemData['retail_min_price']) && !empty($itemData['retail_min_price'])) {
			    $reg_price = $this->setProductPrice( $productId, $itemData['retail_min_price'], $ced_g2a_settings );
			    $sale_price = $this->setProductPrice( $productId, $itemData['minPrice'], $ced_g2a_settings );
			    update_post_meta( $productId, '_price', $sale_price );
			    update_post_meta( $productId, '_sale_price', $sale_price );
			    update_post_meta( $productId, '_regular_price', $reg_price );
			} else {
			    $reg_price = $this->setProductPrice( $productId, $itemData['minPrice'], $ced_g2a_settings );
			    update_post_meta( $productId, '_price', $reg_price );
			    update_post_meta( $productId, '_regular_price', $reg_price );    
			}
		
			if( isset( $itemData['qty'] ) && $itemData['qty'] > 0 ) {
				update_post_meta( $productId, '_stock', $itemData['qty'] );
				update_post_meta( $productId, '_stock_status', "instock" );
			} else {
				update_post_meta( $productId, '_stock_status', "outofstock" );
			}
			
			$this->ced_g2a_createProductCategory( $productId, $itemData['categories'],$itemData['name'] );
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

		public function ced_g2a_createProductCategory( $productId, $categories ){

			if( !empty( $categories ) )
			{
				foreach ($categories as $key => $value) 
				{
					$wooCatName = $value['name'];
					$term = wp_insert_term( $wooCatName, 'product_cat', [
						'description'=> $wooCatName,
						]
					);
					if( isset( $term->error_data['term_exists'] ) )
					{
						$term_id = $term->error_data['term_exists'];
					} 
					else if ( isset( $term['term_id'] ) ) {
						$term_id = $term['term_id'];
					}

					if( $term_id )
					{
						$term = get_term_by('name', $wooCatName, 'product_cat');
						$term_ids[] = $term_id;
					}
					if( !empty( $term_ids ) )
					{
						wp_set_object_terms($productId, $term_ids, 'product_cat');
					}
				}
			}
			return ;
		}

		public function ced_g2a_createAttributes( $productId = "", $attributeLabel = "", $attributeValue = "" ){
		// 	var_dump($attributeLabel);
		// var_dump($attributeValue);
		if($attributeValue == "All" || $attributeValue == "All products" || $attributeValue == "Default Category" || $attributeValue == "Sort List")
		{
			return;
		}
		wc_create_attribute( array(
			'name' => "$attributeLabel",
		) );
		$slug = preg_replace( '/^pa\_/', '', wc_sanitize_taxonomy_name( $attributeLabel ) );
		$term_taxonomy_ids = wp_set_object_terms( $productId, $attributeValue, "pa_".$slug, true );
		
			$thedata = Array("pa_".$slug=>Array(
				'name'=>"pa_".$slug,
				'value'=>$attributeValue,
				'is_visible' => '1',
				'is_taxonomy' => '1'
			));
			$_product_attributes = get_post_meta($productId, '_product_attributes', true);
			$_product_attributes = !empty($_product_attributes)? $_product_attributes : array();
			update_post_meta( $productId,'_product_attributes',array_merge($_product_attributes,$thedata)); 
		
	}
		// public function ced_g2a_ImportProductImages( $productId, $image ){

		// 	if( $productId == "" || $image == "" )
		// 		return false;

		// 		// var_dump($image);
		// 	$image_id = $this->ced_g2a_InsertProductImage( $productId, $image );
		// 	// var_dump($image_id);
		// 	if( $image_id != "" )
		// 		set_post_thumbnail( $productId, $image_id );

		// 	return true;
		// }

		public function ced_g2a_InsertProductImage( $productId, $image_url ){

			if( $productId == "" || empty( $image_url ) )
				return false;
			
			// var_dump($image_url);
			$image_name = basename( $image_url ).".jpg";
			$upload_dir       = wp_upload_dir(); // Set upload folder

			$arrContextOptions=array(
			    "ssl"=>array(
			        "verify_peer"=>false,
			        "verify_peer_name"=>false,
			    ),
			);  

			$image_data       = file_get_contents($image_url, false, stream_context_create($arrContextOptions)); // Get image data
			// var_dump($image_data);
			if( $image_data == "" || $image_data == null )
			{
				$connection = curl_init();
				curl_setopt($connection, CURLOPT_URL, $image_url);

				curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
				$image_data = curl_exec($connection);	
				curl_close($connection);
			}
			// var_dump($image_data);
			$unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
			$filename         = basename( $unique_file_name ); // Create image file name
			if( wp_mkdir_p( $upload_dir['path'] ) ) {
			    $file = $upload_dir['path'] . '/' . $filename;
			} else {
			    $file = $upload_dir['basedir'] . '/' . $filename;
			}
			// var_dump($file);
			file_put_contents( $file, $image_data );

			$wp_filetype = wp_check_filetype( $filename, null );
			// Set attachment data
			$attachment = array(
			    'post_mime_type' => $wp_filetype['type'],
			    'post_title'     => sanitize_file_name( $filename ),
			    'post_content'   => '',
			    'post_status'    => 'inherit'
			);

			$attach_id = wp_insert_attachment( $attachment, $file, $productId );
			require_once(ABSPATH . 'wp-admin/includes/image.php');
			$attach_data = wp_generate_attachment_metadata( $attach_id, $file );

			// var_dump($attach_data);

			wp_update_attachment_metadata( $attach_id, $attach_data );
			return $attach_id;
		}

		public function update_g2a_item( $itemDetails = array() ){

			if( empty($itemDetails) )
				return ;

			$endpointUrl = "https://api.gunbroker.com/v1/Items/".$itemDetails['gunBrokerId'];
			$itemUpdateArray = array( 'FixedPrice' => $itemDetails['price'], 'Quantity' => $itemDetails['qty'] );
			// print_r($itemUpdateArray);
			$AccessToken = $this->regenerate_access_token();
			// print_r($AccessToken);
			if( isset( $AccessToken['accessToken'] ) && $AccessToken['accessToken'] != "" )
			{
				update_option( 'ced_g2a_access_token', $AccessToken['accessToken'] );
				$accessToken = $AccessToken['accessToken'];
				$ced_g2a_g2a_details = get_option( 'ced_g2a_config_details', array() );
				$response = $this->sendHttpPutRequest($ced_g2a_g2a_details, $endpointUrl, $accessToken, $itemUpdateArray);
				// print_r( $response );
				// die;
			}
		}

		public function regenerate_access_token(){

			$ced_g2a_g2a_details = get_option( 'ced_g2a_config_details', array() );

			$endpointUrl = "https://api.gunbroker.com/v1/Users/AccessToken";
			$fileName = CED_G2A_PATH."admin/lib/ced-g2a-authorize.php";
			if( file_exists($fileName) )
			{
				require_once $fileName;
				$Ced_G2A_Authorize = new Ced_G2A_Authorize;
				$Ced_G2A_Authorize_instance = $Ced_G2A_Authorize->get_instance();

				$response = $Ced_G2A_Authorize_instance->sendHttpRequest($ced_g2a_g2a_details, $endpointUrl);
				return $response;
			}
		}
	}
}