<?php
/**
 * Theme basic setup
 *
 * @package valc
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Remove admin bar on front.
add_filter( 'show_admin_bar', '__return_false' );
remove_action( 'init', 'wp_admin_bar_init' );

add_action( 'after_setup_theme', 'valc_setup' );

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function valc_setup() {
	/*
	* Let WordPress manage the document title.
	* By adding theme support, we declare that this theme does not use a
	* hard-coded <title> tag in the document head, and expect WordPress to
	* provide it for us.
	*/
	add_theme_support( 'title-tag' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'primary' => 'Primary Menu',
		)
	);

	/*
	* Switch default core markup for search form, comment form, and comments
	* to output valid HTML5.
	*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'script',
			'style',
		)
	);

	/*
	* Adding Thumbnail basic support
	*/
	add_theme_support( 'post-thumbnails' );
	update_option( 'thumbnail_size_w', 312 );
	update_option( 'thumbnail_size_h', 245 );
	update_option( 'thumbnail_crop', 0 );
	update_option( 'medium_size_w', 135 );
	update_option( 'medium_size_h', 85 );
	update_option( 'large_size_w', 1024 );
	update_option( 'large_size_h', 1024 );

	/*
	* Remove comments
	*/
	add_filter( 'comments_open', '__return_false', 20, 2 );
	add_filter( 'pings_open', '__return_false', 20, 2 );

	/**
	 * Remove pages from sitemap
	 *
	 * @param WP_Post_Type[] $post_types Array of registered post type objects keyed by their name.
	 */
	function disable_post_sitemap( $post_types ) {
		unset( $post_types['page'] );
		return $post_types;
	}
	add_filter( 'wp_sitemaps_post_types', 'disable_post_sitemap', 10, 1 );

	/**
	 * Remove tags, formats and categories from sitemap
	 *
	 * @param WP_Taxonomy[] $taxonomies Array of registered taxonomy objects keyed by their name.
	 */
	function disable_tags_sitemap( $taxonomies ) {
		unset( $taxonomies['post_tag'] );
		unset( $taxonomies['post_format'] );
		unset( $taxonomies['category'] );
		return $taxonomies;
	}
	add_filter( 'wp_sitemaps_taxonomies', 'disable_tags_sitemap', 10, 1 );

	/**
	 * Remove users from sitemap
	 *
	 * @param WP_Sitemaps_Provider $provider Instance of a WP_Sitemaps_Provider.
	 * @param string               $name     Name of the sitemap provider.
	 */
	function disable_user_sitemap( $provider, $name ) {
		if ( 'users' === $name ) {
			return false;
		}
		return $provider;
	}
	add_filter( 'wp_sitemaps_add_provider', 'disable_user_sitemap', 10, 2 );

	/**
	 * Add 404 header when 404 page
	 */
	function send_404_header() {
		if ( is_page( '404 - PAGE INTROUVABLE' ) ) {
			status_header( 404 );
			nocache_headers();
		}
	}
	add_action( 'wp', 'send_404_header' );

}
