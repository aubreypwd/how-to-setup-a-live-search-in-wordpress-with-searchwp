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
