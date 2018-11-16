/* globals console, jQuery */
if ( ! window.hasOwnProperty( 'ajaxSearch' ) ) {

	window.ajaxSearch = ( function( $, pub ) {

		var request = false;
		var resultsLocation = {};
		var input = {};

		function placeResults( data ) {

			// Clear the results.
			resultsLocation.html( '' );

			for ( var i in data ) {

				// Get the post data.
				var post = data[ i ];

				// Create a list element.
				var li = $( '<li>' );

				// Create an anchor element.
				var a = $( '<a>' );

				// Save the post title and link to the anchor.
				a.text( post.post_title );
				a.attr( 'href', post.post_permalink );

				// Add the anchor to the list element.
				li.append( a );

				// Add the list element to the results <ul>.
				resultsLocation.append( li );
			}
		}

		function initAjax() {

			// When someone adds/removes a character from the input...
			input.on( 'keyup', function() {
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
					success: function( response, status, jqXHR ) {
						request = false;
						placeResults( response.data );
					},

					// Failure.
					error: function( jqXHR, status, error ) {
					}
				} );
			} );
		}

		function initResultsLocation() {
			resultsLocation = $( '<ul id="ajax-results">' );
			var form = $( 'form.search-form' ).append( resultsLocation );
		}

		function initInput() {
			input.attr( 'autocomplete', 'off' );
		}

		function init() {

			// Store input for use later.
			input = $( 'form.search-form .search-field' );

			initInput();
			initResultsLocation();
			initAjax();

		}

		$( document ).ready( init );

		return pub; // Return public things.
	} ( jQuery, {} ) );

} // End if().
