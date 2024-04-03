<?php
if(!class_exists('Ced_G2A_Authorize')){

	class Ced_G2A_Authorize{

		private static $_instance;
		/**
		 * get_instance Instance.
		 *
		 * Ensures only one instance of Ced_G2A_Authorize is loaded or can be loaded.
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

		public function authorize_account($ced_g2a_g2a_details = array()){

			if( empty( $ced_g2a_g2a_details ) )
				return array( 'status'=>"201", 'message'=>"Please fill in the details" );

			$endpointUrl = "https://api.gunbroker.com/v1/Users/AccessToken";

			$response = $this->sendHttpRequest( $ced_g2a_g2a_details, $endpointUrl );
			return $response;
		}

		public function sendHttpRequest($ced_g2a_g2a_details, $endpointUrl){

			$username = isset($ced_g2a_g2a_details['ced_g2a_hashKey']) ? $ced_g2a_g2a_details['ced_g2a_hashKey'] : "";
			$password = isset($ced_g2a_g2a_details['ced_g2a_secretKey']) ? $ced_g2a_g2a_details['ced_g2a_secretKey'] : "";
			$ced_g2a_g2a_devkey = isset($ced_g2a_g2a_details['ced_g2a_g2a_devkey']) ? $ced_g2a_g2a_details['ced_g2a_g2a_devkey'] : "";
			$requestBody = array( 'Username' => $username, 'Password'=>$password );

			$header = $this->prepareHeader($ced_g2a_g2a_devkey);
			$connection = curl_init();

			// $endpointUrl = $endpointUrl."ordermgt/transports/order_api/v1/obtain_auth_token/";
			curl_setopt($connection, CURLOPT_URL, $endpointUrl);
			curl_setopt($connection, CURLOPT_HTTPHEADER, $header);
			//stop CURL from verifying the peer's certificate
			curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
			
			//set method as POST
			curl_setopt($connection, CURLOPT_POST, 1);
			
			curl_setopt($connection, CURLOPT_POSTFIELDS, json_encode($requestBody));
			curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
	        
			$response = curl_exec($connection);
			curl_close($connection);
			return $this->ParseResponse($response);
		}

		public function prepareHeader($ced_g2a_g2a_devkey)
	 	{
	 		$header = array(
	 			'Content-Type: application/json',
	 			'X-DevKey: '.$ced_g2a_g2a_devkey
			);
			return $header;
	 	}

		public function ParseResponse($response){

			if( !empty( $response ) )
				return json_decode( $response, true );
		}
	}
}