/*
 * Hi, you're looking at the frontend JavaScript file that does all the
 * fancy "live search" stuff when you type in the search....
 *
 * We're basically going to do the following:
 *    - Cache things and elements on the page we need for the rest of the code
 *    - Make a popup on the page so we can throw results in it
 *    - Listen for someone to type in the search input and send what they typed to PHP via AJAX
 *    - Take the results from PHP (posts) and put them on the screen.
 */

/*
 * This is black magic, or called an ifee, which is a simple way to call a function without going function().
 *
 * Oh and that $, if you look at the bottom of this file, you'll see we're just renaming jQuery to $ so we can do less typing.
 */
( function( $ ) {

	/*
	 * Things we cache for use throughout all function.
	 */
	var currentAjaxRequest = false; // This is just going to be a place where we can store the active AJAX request.
	var resultsLocation = null; // We'll cache a jQuery object for the results location (dropdown) we create later.
	var searchFormTextbox = null; // Same, we'll cache the input that we use to type our search term in here...later.
	var searchForm = null; // Another place where we'll store the search form element later.

	// This uses jQuery to detect when the page is loaded and we can start modifying the webpage, when it is we run domReady().
	$( document ).ready( domReady );

	/**
	 * When the DOM/Document is ready.
	 *
	 * When jQuery says we can start modifying the web page, lets do it!
	 */
	function domReady() {
		findElements(); // Find elements on the page so we can work with them.

		// Make sure we can do everything and that fndElements found what it needed.
		if ( ! checkRequirements() ) {

			// We must not have the right stuff on the screen like a search input, let's not do anything then.
			return;
		}

		// We have what we need, lets......
		setupResultsPopup(); // Create a results location that we can put our results in from AJAX.
		turnOffAutocomplete(); // Turn off that dropdown and fills in things you've typed before (it looks like our dropdown!).
		listenForTypingAndSearchViaAjax(); // When someone types in the search input, do the AJAX request.
	}

	/**
	 * This makes sure we have workable elements on the page to work with.
	 *
	 * @return {Boolean} True if we have those elements.
	 */
	function checkRequirements() {

		/*
		 * .length is JavaScripts way to making sure jQuery found the element we e.g. assigned for form in findElements().
		 *
		 * If any of them come back as 0 that means jQuery couldn't find the form or input, etc.
		 */
		return 0 !== searchForm.length && 0 !== searchFormTextbox.length;
	}

	/**
	 * Cache stuff for use later across the code.
	 *
	 * Here's where we assign values to all those variables we created earlier.
	 */
	function findElements() {
		searchForm  = $( 'form.search-form' ); // Now let's cache the form element (we'll use it later).

		// Notice how I do a fancy thing jQuery lets me do, which is get .search-field in the form we found above.
		searchFormTextbox = $( '.search-field', searchForm ); // First let's find the search form and it's input field and save that for later.
	}

	/**
	 * Setup an area where we can place our search results (yeah, the popup!).
	 */
	function setupResultsPopup() {

		// Use jQuery to make a new <ul> that we'll place <li>'s in.
		resultsLocation = $( '<ul id="ajax-results">' );

		// Add our UL to the already existing search form using jQuery (now it's just sitting there ready for us).
		form.append( resultsLocation );
	}

	/**
	 * Turn off autocomplete.
	 *
	 * Normally when you type into an input, it will drop down with recent search terms you typed previously.
	 * We don't want that as it will look like our popup, so let's disable that.
	 */
	function turnOffAutocomplete() {
		searchFormTextbox.attr( 'autocomplete', 'off' );
	}

	/**
	 * Get Search Results.
	 *
	 * This listens for you to type something in the search input field and does the
	 * AJAX request, sends what you typed, and does something with the results.
	 */
	function listenForTypingAndSearchViaAjax() {

		// When someone adds/removes a character from the input...
		searchFormTextbox.on( 'keyup', function() {

			/*
			 * This is nifty because we are listening for someome to press down on a key, and lift up.
			 * When you're typing "awesome post" that's 12 times! We don't want to perform 12 AJAX requests,
			 * for example as you type "aw" and then "e". As you type, if we fired an AJAX request for what you typed,
			 * if the last one didn't finish yet (because you're so fast) then cancel it and make a new one.
			 */
			if ( false !== currentAjaxRequest ) {

				// Cancel any older requests so we can start a new one.s
				currentAjaxRequest.abort();
			}

			/*
			 * Do an AJAX request and store it in currentAjaxRequest so we can cancel it later
			 * if you type something else again e.g. the "e" in awesome...
			 */
			currentAjaxRequest = $.ajax( {

				/*
				 * AJAX uses normal POST and GET methods to send data without reloading the page,
				 * kind of like a <form> but better.
				 */
				method: 'post',

				/*
				 * Remember in the PHP file where we did that wp_localize_script, well the ajaxUrl we saved
				 * there is now being used here to tell this AJAX request where to send the thing you typed.
				 */
				url: ajaxSearchl10n.ajaxUrl,

				// Send this data.
				data: {

					/*
					 * Remember, every AJAX request gets a name and WP response to the name of that "action", this is
					 * what we've named this one.
					 */
					action: 'ajax_search',

					// This is that cool security secret we setup in the PHP file, that helps us make sure you are the one firing this AJAX request.
					nonce: ajaxSearchl10n.nonce,

					// This is where we pass what you typed over and name it "s" so we can get it in PHP.
					s: $( this ).val(), // $( this ) just means the search input we're listening for `keyup` on.
				},

				// When the PHP file responds, this is what we do about it.
				success: function( response, status, jqXHR ) {
					currentAjaxRequest = false;
					placeResults( response.data );
				}
			} );
		} );
	}

	/**
	 * Place results.
	 *
	 * This actually places the search results in the popup/dropdown on the page.
	 *
	 * @param  {Object} ajaxSearchResults Ajax Search Results from PHP.
	 */
	function placeResults( ajaxSearchResults ) {

		// First we want to make sure that we clear out any previous search results by just emptying the <ul>.
		resultsLocation.html( '' );

		// Now the AJAX request above passed us the results from the PHP file responding, let's loop through those, we know they're data about posts.
		for ( var i in ajaxSearchResults ) {

			// This is how for loops work in JavaScript, let's just assign the data to a variable that makes sense.
			var post = ajaxSearchResults[ i ];

			// Create a list element to add to the UL we added earlier in setupResultsPopup().
			var li = $( '<li>' );

			// Create an anchor element (the link to the post).
			var a = $( '<a>' );

			a.text( post.post_title ); // <a>The post title</a>
			a.attr( 'href', post.post_permalink ); // <a href="http://thepostlink.com">The post title</a>

			li.append( a ); // <li><a href="http://thepostlink.com">The post title</a></li>

			// Now add an the above code we just made in JavaScript (yeah you can do that) and inject it into the UL where it shows our search results.
			resultsLocation.append( li );

			/*
			 * So we might have something like:
			 *
			 * <ul id="ajax-results">
			 *    <li><a href="http://thepostlink.com">The post title</a></li>
			 * </ul>
			 *
			 * ... in the HTML for the popup right now.
			 */
		}
	}

} ( jQuery ) )
