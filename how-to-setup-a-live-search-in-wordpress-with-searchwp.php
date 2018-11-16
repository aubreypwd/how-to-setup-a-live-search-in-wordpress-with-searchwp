<?php
/**
 * Plugin Name: How to setup a "live" search in WordPress with SearchWP
 * Version:     1.0.0
 * Author:      Aubrey Portwood at WebDevStudios
 * Author URI:  http://webdevstudios.com
 * Text Domain: how-to-setup-a-live-search-in-wordpress-with-searchwp
 * License:     GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @since       1.0.0
 * @package     WebDevStudios\how-to-setup-a-live-search-in-wordpress-with-searchwp
 */

require_once 'cmb2/init.php';

add_action( 'init', function() {
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

add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'ajax-search', plugins_url( 'ajax-search.css', __FILE__ ), array(), time() );
	wp_enqueue_script( 'ajax-search', plugins_url( 'ajax-search.js', __FILE__ ), array( 'jquery' ), time(), true );
	wp_localize_script( 'ajax-search', 'ajaxSearchl10n', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'ajax_search' ),
	) );
} );

function live_ajax_get_post_data_from_ids( $post_ids ) {
	$posts = [];

	if ( ! is_array( $post_ids ) ) {
		return [];
	}

	foreach ( $post_ids as $post_id ) {
		$posts[ $post_id ] = [
			'post_title'     => html_entity_decode( get_the_title( $post_id ) ),
			'post_permalink' => get_the_permalink( $post_id ),
		];
	}

	return $posts;
}

function live_ajax_search() {

	// Make sure that the nonce passes.
	check_admin_referer( 'ajax_search', 'nonce' );

	// Get our search term sent from the input.
	$s = sanitize_text_field( $_POST['s'] );

	// If no SearchWP use this...
	if ( ! class_exists( 'SWP_Query' ) ) {

		// Use default WP_Query search.
		$query = new WP_Query( array(
			's'      => $s,
			'fields' => 'ids',
		) );

		// Send back the results for WP_Query search.
		wp_send_json_success( live_ajax_get_post_data_from_ids( $query->get_posts() ) );
	}

	$query = new SWP_Query( array(
		's'      => $s,
		'fields' => 'ids',
	) );

	wp_send_json_success( live_ajax_get_post_data_from_ids( $query->get_posts() ) );
}
add_action( 'wp_ajax_nopriv_ajax_search', 'live_ajax_search' );
add_action( 'wp_ajax_ajax_search', 'live_ajax_search' );
