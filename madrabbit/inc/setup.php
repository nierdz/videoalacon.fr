<?php
/**
 * Theme basic setup
 *
 * @package madrabbit
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Remove admin bar on front
add_filter('show_admin_bar', '__return_false');
remove_action('init', 'wp_admin_bar_init');

add_action( 'after_setup_theme', 'madrabbit_setup' );

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function madrabbit_setup() {

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

}
