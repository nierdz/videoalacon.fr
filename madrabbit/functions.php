<?php
/**
 * madrabbit functions and definitions
 *
 * @package madrabbit
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$understrap_includes = array(
  '/setup.php',                           // Theme setup and custom theme supports.
  '/enqueue.php',                         // Enqueue scripts and styles.
  '/template-tags.php',                   // Custom template tags for this theme.
  '/pagination.php',                      // Custom pagination for this theme.
  '/extras.php',                          // Custom functions that act independently of the theme templates.
  '/custom-comments.php',                 // Custom Comments file.
  '/jetpack.php',                         // Load Jetpack compatibility file.
  '/class-wp-bootstrap-navwalker.php',    // Load custom WordPress nav walker. Trying to get deeper navigation? Check out: https://github.com/understrap/understrap/issues/567.
  '/editor.php',                          // Load Editor functions.
);

foreach ( $understrap_includes as $file ) {
  require_once get_template_directory() . '/inc' . $file;
}
