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
  '/class-wp-bootstrap-navwalker.php',    // Load custom WordPress nav walker.
);

foreach ( $understrap_includes as $file ) {
  require_once get_template_directory() . '/inc' . $file;
}
