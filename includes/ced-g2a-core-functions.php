<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function ced_g2a_getEndpointUrl( $ced_g2a_details = array() )
{
	if( empty( $ced_g2a_details ) )
		return ; 

	$endpointUrl = "";
	if( isset( $ced_g2a_details['ced_g2a_mode'] ) )
	{
		if( $ced_g2a_details['ced_g2a_mode'] == "sandbox" )
		{
			$endpointUrl = "https://sandboxapi.g2a.com/v1/";
		}
		else
		{
			$endpointUrl = "https://products-export-api.g2a.com/v1/";
		}

		return $endpointUrl;
	}
	return $endpointUrl;
}