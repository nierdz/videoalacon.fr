<?php
/**
 * Enqueue CSS and JS
 *
 * @package madrabbit
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Enqueue CSS and JS.
 */
function madrabbit_enqueue() {
	$the_theme     = wp_get_theme();
	$theme_version = $the_theme->get( 'Version' );

	wp_enqueue_style( 'understrap-styles', get_template_directory_uri() . '/css/theme.min.css', array(), $theme_version );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'understrap-scripts', get_template_directory_uri() . '/js/theme.min.js', array(), $theme_version, true );
}

add_action( 'wp_enqueue_scripts', 'madrabbit_enqueue' );
