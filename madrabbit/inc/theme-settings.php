<?php
/**
 * Check and setup theme's default settings
 *
 * @package madrabbit
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Store default theme settings in database.
 */
function madrabbit_setup_theme_default_settings() {
  $defaults = madrabbit_get_theme_default_settings();
  $settings = get_theme_mods();
  foreach ( $defaults as $setting_id => $default_value ) {
    // Check if setting is set, if not set it to its default value.
    if ( ! isset( $settings[ $setting_id ] ) ) {
      set_theme_mod( $setting_id, $default_value );
    }
  }
}

/**
 * Retrieve default theme settings.
 *
 * @return array
 */
function madrabbit_get_theme_default_settings() {
  $defaults = array(
    'understrap_posts_index_style' => 'default',   // Latest blog posts style.
    'understrap_sidebar_position'  => 'right',     // Sidebar position.
  );

  /**
   * Filters the default theme settings.
   *
   * @param array $defaults Array of default theme settings.
   */
  return apply_filters( 'understrap_theme_default_settings', $defaults );
}
