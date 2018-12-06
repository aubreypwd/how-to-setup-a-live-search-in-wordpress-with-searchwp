<?php
/**
 * Plugin Name: How to setup a "live" search in WordPress with SearchWP
 * Author:      Aubrey Portwood at WebDevStudios
 * Author URI:  http://webdevstudios.com
 * Text Domain: how-to-setup-a-live-search-in-wordpress-with-searchwp
 * License:     GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

/*
 * Before you code steps (also see video):
 *
 *   Note, there is a cool video I have with this that goes through these steps more visually.
 *
 * =========================================
 *
 * 1) Download and install SearchWP (or SearchWP Pro) from http://searchwp.com by logging into your account and activate it.
 *
 *     You will have to install SearchWP Zip you download via Dashboard > Plugins > Add New > Upload.
 *
 * 2) Goto Dashboard > Settings > SearcWP and click "Save Engines" after you setup your search criteria.
 *
 *     You will have to allow the Indexer to do the initial index. Will likely not take long on a small site.
 *
 * 3) That's it, SearchWP is ready, now let's start coding...
 */

/*
 * Hi!
 *
 * Let's start here.....
 *
 * A few things to note:
 *
 * 1) This code is not WDS Coding Standard Compliant, and I did not do any fancy coding practices, on purpose.
 *    I wanted this to be easy for the beginner and the advanced person to look at as a way to get started
 *    building live searches using SearchWP. I could have easily made this OOP or even wrapped it in a Class wrapper,
 *    or even a function....but I didn't, to keep it really simple for everyone.
 *
 * 2) There is SO much more you can do here! But, the goal of this is to get YOU STARTED. You're going to end up with a cool
 *    popup that shows up when you search for something as you type....but there's a lot farther you can go from there...
 *
 * 3) This does not modify the search results page. Though we show you about 20 search results in the "live" popup, if
 *    you hit enter you're going to see the search results page. It uses the "default" engine in SearchWP by default, so the
 *    results should be the same (given we alpha-sort our results, they won't be on the search results landing page).
 *
 * 4) Email me if you found this useful! aubrey@webdevstudios.com
 *
 * First off, this file is the actual plugin file, when you activate this plugin, this files loads first,
 * so ... start here.... READ ON!
 */

/*
 * So, right off the bat, I'm going to cheat and use CMB2 https://github.com/CMB2/CMB2
 * to create some post-meta fields that we can use to test SearchWP's ability to search
 * postmeta!
 *
 * So yeah, let's include that library, so we don't have to write too much code for adding a metabox.
 */
require_once 'cmb2/init.php';

/*
 * I want to make sure and be able to show you that SearchWP can also search multiple post types too!
 *
 * This is going to create a "Books" Custom Post type on the left side of the Dashboard. And the CMB2
 * Library we loaded will help us add some meta fields too. This is JUST so I can show you that SearchWP can
 * search postmeta EVEN IN custom post types!
 *
 * To make sure your Search Engine is setup for this, first go to the Dashboard > Books section and add a Book, some content
 * and make sure and set an author (that's what CMB2 is doing).
 *
 * Then, goto Dashboard > Settings > SearchWP and click "Add Post Type" button. Make sure Books is in the list.
 *
 * To make sure our meta fields for this work in search expand the "Books" Post Type you just added, and click "Add Attribute".
 * Click "Custom Field" and add _author (that will ensure we can search the author custom field).
 *
 * NOTE search results will include Posts too, we're just adding this CPT JUST to show you you can search for CPT's and custom fields too!
 */
add_action( 'init', function() {

	// This is a default WordPress function that creates the post type.
	register_post_type( 'books', array(
		'labels'              => array(
			'name'               => __( 'Books', 'how-to-setup-a-live-search-in-wordpress-with-searchwp' ),
			'singular_name'      => __( 'Book', 'how-to-setup-a-live-search-in-wordpress-with-searchwp' ),
			'add_new'            => _x( 'Add New Book', 'how-to-setup-a-live-search-in-wordpress-with-searchwp', 'how-to-setup-a-live-search-in-wordpress-with-searchwp' ),
			'add_new_item'       => __( 'Add New Book', 'how-to-setup-a-live-search-in-wordpress-with-searchwp' ),
			'edit_item'          => __( 'Edit Book', 'how-to-setup-a-live-search-in-wordpress-with-searchwp' ),
			'new_item'           => __( 'New Book', 'how-to-setup-a-live-search-in-wordpress-with-searchwp' ),
			'view_item'          => __( 'View Book', 'how-to-setup-a-live-search-in-wordpress-with-searchwp' ),
			'search_items'       => __( 'Search Books', 'how-to-setup-a-live-search-in-wordpress-with-searchwp' ),
			'not_found'          => __( 'No Books found', 'how-to-setup-a-live-search-in-wordpress-with-searchwp' ),
			'not_found_in_trash' => __( 'No Books found in Trash', 'how-to-setup-a-live-search-in-wordpress-with-searchwp' ),
			'parent_item_colon'  => __( 'Parent Book:', 'how-to-setup-a-live-search-in-wordpress-with-searchwp' ),
			'menu_name'          => __( 'Books', 'how-to-setup-a-live-search-in-wordpress-with-searchwp' ),
		),
		'hierarchical'        => false,
		'description'         => __( 'Just a custom post type to show that we can add them to Search WP', 'how-to-setup-a-live-search-in-wordpress-with-searchwp' ),
		'taxonomies'          => array(),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => null,
		'menu_icon'           => null,
		'show_in_nav_menus'   => true,
		'publicly_queryable'  => true,
		'exclude_from_search' => false,
		'has_archive'         => true,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => true,
		'capability_type'     => 'post',
		'supports'            => array(),
	) );
} );

// This will add that _author metabox we were talking about.
add_action( 'cmb2_admin_init', function() {
	$author_box = new_cmb2_box( array(
		'id'           => '_book_info',
		'title'        => esc_html__( 'Info', 'how-to-setup-a-live-search-in-wordpress-with-searchwp' ),
		'object_types' => array( 'books' ),
	) );

	$author_box->add_field( array(
		'name'   => esc_html__( 'Author', 'how-to-setup-a-live-search-in-wordpress-with-searchwp' ),
		'id'     => '_author',
		'type'   => 'text',
		'column' => true,
	) );
} );

/*
 * Now, we're going to need to load a couple scripts on the frontend, and this is how we do it.
 */
add_action( 'wp_enqueue_scripts', function() {

	// First let's load any CSS we might need.
	wp_enqueue_style( 'ajax-search', plugins_url( 'ajax-search.css', __FILE__ ), array(), time() );

	// Now, let's load our JavaScript that will do the "Live Search" part.
	wp_enqueue_script( 'ajax-search', plugins_url( 'ajax-search.js', __FILE__ ), array( 'jquery' ), time(), true );

	// This just stores some information for us to use in our JavaScript later...
	wp_localize_script( 'ajax-search', 'ajaxSearchl10n', array(

		/*
		 * This will use "AJAX" to do the live search without having to reload the page. To do this we need to tell
		 * JavaScript where to send the data (a URL).
		 */
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),

		/*
		 * This is a security feature. It's way of WordPress knowing that it's you using the live search by creating
		 * a little secret code that will last for 24 hours.
		 */
		'nonce' => wp_create_nonce( 'ajax_search' ),
	) );
} );

/**
 * Get live search data back to the frontend.
 *
 * This will get more information that our search results need. In the function
 * live_ajax_search() below all that gives us is a list of posts and their ID in the database.
 *
 * We need more than that, so this will go get a little bit more, e.g. the title of the post, and the
 * URL so we can click it.
 *
 * @param  array $post_ids  A list of Posts identified by their ID in the database.
 * @return array            More data about those posts, e.g. post title and their URL.
 */
function get_live_ajax_get_post_data_from_ids( $post_ids ) {
	$posts = [];

	if ( ! is_array( $post_ids ) ) {

		// This is just making sure that the $post_ids are an array so we can loop over them below, if they are not, we don't pass back any data.
		return [];
	}

	foreach ( $post_ids as $post_id ) {
		$posts[ $post_id ] = [

			/*
			 * html_entity_decode
			 *
			 * If you don't want post titles like "You're Awesome Post" to show on the frontend
			 * as "You\'re Awesome Post" (escaped), you have to use html_entity_decode to make sure
			 * we remove those escaping slashes that happen as a result of sending back the data over JSON.
			 */
			'post_title'     => html_entity_decode( get_the_title( $post_id ) ),

			// And of course, we need the URL so we can click it.
			'post_permalink' => get_the_permalink( $post_id ),
		];
	}

	return $posts;
}

/**
 * Pass search result data back to frontend.
 *
 * When the AJAX request is performed from the frontend (via ajax-search.js/JavaScript),
 * this will answer the call and send some useful data back.
 */
function pass_search_result_data_back_to_frontend() {

	// Make sure our secret security measure checks out (see ajaxSearchl10n above).
	check_admin_referer( 'ajax_search', 'nonce' );

	/*
	 * sanitize_text_field
	 *
	 * When our AJAX request comes over -over JavaScript we save the search term, e.g.
	 * "You're Awesome Post" in a variable called "s". Here we just grab that value and make
	 * sure it's not passing any bad data that could corrupt the system using sanitize_text_field().
	 */
	$s = sanitize_text_field( $_REQUEST['s'] );

	// This checks to see if SearchWP's Querying engine is available, if it's not we default to normal WP_Query which WordPress uses (and is less powerful).
	if ( ! class_exists( 'SWP_Query' ) ) {

		// Use default WP_Query search (just as a fallback, because we don't want things to just not work because someone deactivated SearchWP).
		$query = new WP_Query( array(
			's'              => $s, // The search term sent over via AJAX.
			'fields'         => 'ids', // We just want ID's here so we can only get what we need later.
			'posts_per_page' => 20, // More results, we may want to do something more advanced here (if you comment this out it will follow Dashboard > General > Reading settings).
		) );

		// Send back the results for WP_Query (generic search results). Note, the script will die() here and not continue.
		wp_send_json_success( get_live_ajax_get_post_data_from_ids( $query->get_posts() ) );
	}

	/*
	 * SWP_Query
	 *
	 * So we must have SWP_Query available, so let's use that because it's going to search through WordPress
	 * using SearchWP!
	 *
	 * See https://searchwp.com/docs/swp_query/ for more documentation.
	 *
	 * This works a lot like WP_Query but is more powerful.
	 */
	$query = new SWP_Query( array(
		's'              => $s, // The search term sent over via AJAX.
		'fields'         => 'ids', // We just want ID's here so we can only get what we need later.
		'posts_per_page' => 20, // More results, we may want to do something more advanced here (if you comment this out it will follow Dashboard > General > Reading settings).
	) );

	// And....send data back to the frontend to be processed in JavaScript.
	wp_send_json_success( get_live_ajax_get_post_data_from_ids( $query->get_posts() ) );
}

/*
 * AJAX Hooks.
 *
 * When our JavaScript (as you will see) does an AJAX request it's named something,
 * in this case "ajax_search" so we tell WordPress to answer to that name and go get the data
 * we need.
 *
 *     E.g.: "wp_ajax_nopriv_{ajax_search}" and "wp_ajax_{ajax_search}".
 *
 * wp_ajax_nopriv (what is that)?
 *
 *     You need two here because one allows users who are logged in to use it, and users who are not
 *     logged in to use it (wp_ajax_nopriv).
 */
add_action( 'wp_ajax_nopriv_ajax_search', 'pass_search_result_data_back_to_frontend' );
add_action( 'wp_ajax_ajax_search', 'pass_search_result_data_back_to_frontend' );

/*
 * Now go checkout ajax-search.js....
 */
