/* globals console, jQuery */
if ( ! window.hasOwnProperty( 'ajaxSearch' ) ) {

	window.ajaxSearch = ( function( $, pub ) {

		var request = false;

		function init() {

			// When someone adds/removes a character from the input...
			$( 'form.search-form .search-field' ).on( 'keyup', function() {
				if ( false !== request ) {

					// Cancel any older requests.
					request.abort();
				}

				request = $.ajax( {
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
						request = false;
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
