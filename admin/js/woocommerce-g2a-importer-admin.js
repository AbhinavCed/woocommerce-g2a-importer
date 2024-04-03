(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	 $( document ).on( 'click', '.ced_g2a_import_to_store', function(){
	 	var url = window.location.href;
	 	$( '#ced-g2a-loader' ).show();
	 	var itemId = $( this ).attr( 'data-itemid' );
	 	$.ajax({
			url : ced_g2a_handler.ajaxUrl,
			type: 'post',
			data:{
				action : 'ced_g2a_import_to_store',
				method : "single_import",
				itemId : itemId,
				check_nonce : ced_g2a_handler.ced_g2a_nonce
			},
			success: function(response){
				$(document).find( '#ced-g2a-loader' ).hide();
				window.location.href = url;
			},
			fail : function( response ){
				$(document).find( '#ced-g2a-loader' ).hide();
			}
		});
	 } );

	 $( document ).on( 'click', '.ced_g2a_bulk_import_submit', function(){
	 	
	 	var url = window.location.href;
	 	var itemId = [];
	 	$( '#ced-g2a-loader' ).show();
	 	jQuery('.ced_g2a_select_product_for_import').each(function(key) {
	 		if( jQuery( this ).is( ':checked' ) )
	 		{
	 			itemId[key] = jQuery( this ).val();
	 		}
	 	});

	 	$.ajax({
			url : ced_g2a_handler.ajaxUrl,
			type: 'post',
			data:{
				action : 'ced_g2a_import_to_store',
				method : "bulk_import",
				itemId : itemId,
				check_nonce : ced_g2a_handler.ced_g2a_nonce
			},
			success: function(response){
				$(document).find( '#ced-g2a-loader' ).hide();
				window.location.href = url;
			},
			fail : function( response ){
				$(document).find( '#ced-g2a-loader' ).hide();
			}
		});
	 } );

	 $( document ).on( 'click', '#ced_g2a_place_order', function(){
	 	$( '#ced-g2a-loader' ).show();
	 	var orderId = $( this ).attr( 'data-orderId' );
	 	$.ajax({
	 		url : ced_g2a_handler.ajaxUrl,
	 		type: 'post',
	 		data:{
	 			action : 'ced_g2a_place_order_on_g2a',
	 			orderId : orderId,
	 			check_nonce : ced_g2a_handler.ced_g2a_nonce
	 		},
	 		success: function(response){
	 			$(document).find( '#ced-g2a-loader' ).hide();
	 			var response=jQuery.trim(response);
	 			if(response=="success")
	 			{	
	 				var message="";
	 				message+="<div class='notice notice-success'>Order Placed Succesfully</div>"
	 				$("#ced_g2a_place").append(message);
	 			}
	 			else
	 			{	
	 				var message="";
	 				message+="<div class='notice notice-error'>"+response+"</div>"
	 				$("#ced_g2a_place").append(message);
	 			}
	 		}
	 	});
	 } );

	  $( document ).on( 'click', '#ced_g2a_pay_for_order', function(){
	 	$( '#ced-g2a-loader' ).show();
	 	var orderId = $( this ).attr( 'data-orderId' );
	 	$.ajax({
	 		url : ced_g2a_handler.ajaxUrl,
	 		type: 'post',
	 		data:{
	 			action : 'pay_for_g2a_order',
	 			orderId : orderId,
	 			check_nonce : ced_g2a_handler.ced_g2a_nonce
	 		},
	 		success: function(response){
	 			$(document).find( '#ced-g2a-loader' ).hide();
	 			var response=jQuery.trim(response);
	 			if(response=="success")
	 			{	
	 				var message="";
	 				message+="<div class='notice notice-success'>Paid Succesfully</div>"
	 				$("#ced_g2a_pay_order").append(message);
	 			}
	 			else
	 			{	
	 				var message="";
	 				message+="<div class='notice notice-error'>"+response+"</div>"
	 				$("#ced_g2a_pay_order").append(message);
	 			}
	 		}
	 	});
	 } );

	   $( document ).on( 'click', '#ced_g2a_get_order_key', function(){
	 	$( '#ced-g2a-loader' ).show();
	 	var orderId = $( this ).attr( 'data-orderId' );
	 	$.ajax({
	 		url : ced_g2a_handler.ajaxUrl,
	 		type: 'post',
	 		data:{
	 			action : 'ced_g2a_get_order_key',
	 			orderId : orderId,
	 			check_nonce : ced_g2a_handler.ced_g2a_nonce
	 		},
	 		success: function(response){
	 			$(document).find( '#ced-g2a-loader' ).hide();
	 			var response=jQuery.trim(response);
	 			if(response=="success")
	 			{	
	 				var message="";
	 				message+="<div class='notice notice-success'>Key Retrieved Succesfully</div>"
	 				$("#ced_g2a_get_key").append(message);
	 			}
	 			else
	 			{	
	 				var message="";
	 				message+="<div class='notice notice-error'>"+response+"</div>"
	 				$("#ced_g2a_get_key").append(message);
	 			}
	 		}
	 	});
	 } );

    $( document ).on( 'click', '.ced_g2a_email_key_button', function(){
 	$( '#ced-g2a-loader' ).show();
 	var orderId = $( this ).attr( 'data-orderId' );
 	$.ajax({
 		url : ced_g2a_handler.ajaxUrl,
 		type: 'post',
 		data:{
 			action : 'ced_g2a_send_email_key',
 			orderId : orderId,
 			check_nonce : ced_g2a_handler.ced_g2a_nonce
 		},
 		success: function(response){
 			$(document).find( '#ced-g2a-loader' ).hide();
 			var response=jQuery.trim(response);
 			if(response=="success")
 			{	
 				var message="";
 				message+="<div class='notice notice-success'>Email sent Succesfully</div>"
 				$(".ced_g2a_email_key_button").append(message);
 			}
 			else
 			{	
 				var message="";
 				//message+="<div class='notice notice-error'>"+response+"</div>"
 				//$("#ced_g2a_get_key").append(message);
 			}
 		}
 	});
 });

jQuery( document ).on( 'click', '.ced_include_more_category', function(){
		var repeatable = '<tr><td class = "include_category"><input type="text" class= "ced_include_category"></td></tr>';
		jQuery( repeatable ).insertBefore( jQuery(this).parent().parent());

	});


jQuery( document ).on( 'click', '.ced_save_include_category', function(){
	 	jQuery('#ced_g2a_marketplace_loader').show();
		var arr = [];
		jQuery('.ced_include_category').each(function(){

			var val = jQuery(this).val();
			if(val!="")			
			arr.push(val);

		});
		jQuery.post(
				ced_g2a_handler.ajaxUrl,
				{
					'action': 'ced_g2a_save_included_category',
					'included' : arr
				},
				function(response)
				{	
					jQuery('#ced_g2a_marketplace_loader').hide();
					location.reload(true);
				}
		);
	// console.log(arr);
	})
})( jQuery );
