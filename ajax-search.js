/* globals console, jQuery */
if ( ! window.hasOwnProperty( 'ajaxSearch' ) ) {

	window.ajaxSearch = ( function( $, pub ) {



		function init() {

			$( 'form.search-form .search-field' ).on( 'keyup', function() {
				$.ajax( {
					method: 'post',
					url: ajaxSearchl10n.ajaxUrl,

					// Send this data.
					data: {
						action: 'ajax_search',
						nonce: ajaxSearchl10n.nonce,
						s: $( this ).val(),
					},

					// Success.
					success: function( data, status, jqXHR ) {
					},

					// Failure.
					error: function( jqXHR, status, error ) {
					}
				} );
			} );

		}

		$( document ).ready( init );

		return pub; // Return public things.
	} ( jQuery, {} ) );

} // End if().
