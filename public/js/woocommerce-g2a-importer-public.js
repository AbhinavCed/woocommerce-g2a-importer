
(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
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
	
	$( document ).on( 'click', '.ced_get_g2akey', function(){
		 //alert('fff');
		var modal = document.getElementById("myModal");
		var span = document.getElementsByClassName("close")[0];
		var orderid=$( this ).attr( 'data-orderid' );
		 modal.style.display = "block";
		 //alert(val);


		 

		 // Get the button that opens the modal
		 //var btn = document.getElementById("myBtn");
		 
		 // Get the <span> element that closes the modal
		 



		// var url = window.location.href;
		// var itemId = [];
		// $( '#ced-g2a-loader' ).show();
		// jQuery('.ced_g2a_select_product_for_import').each(function(key) {
		// 	if( jQuery( this ).is( ':checked' ) )
		// 	{
		// 		itemId[key] = jQuery( this ).val();
		// 	}
		// });

		$.ajax({
		   url : ced_g2a_handler.ajaxUrl,
		   type: 'post',
		   data:{
			   action : 'ced_display_g2a_key',
			   method : "bulk_import",
			   orderid : orderid,
			   check_nonce : ced_g2a_handler.ced_g2a_nonce
		   },
		   success: function(response){
			   $(document).find( '#ced-g2a-loader' ).hide();
			   $("#ced_modal_text").text(response);
		   },
		   fail : function( response ){
			   $(document).find( '#ced-g2a-loader' ).hide();
		   }
	   });
	} );



	$( document ).on( 'click', '.close', function(){
		var modal = document.getElementById("myModal");
		var span = document.getElementsByClassName("close")[0];
		modal.style.display = "none";
	  });
	  
	  // When the user clicks anywhere outside of the modal, close it
	  $( document ).on( 'click', '#myModal', function(e){
		var modal = document.getElementById("myModal");
		var span = document.getElementsByClassName("close")[0];
		if (e.target == modal) {
		  modal.style.display = "none";
		}
	  });
})( jQuery );
