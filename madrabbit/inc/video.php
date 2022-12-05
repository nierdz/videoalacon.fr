<?php
/**
 * Download video and thumbnail and setup associated custom post fields
 *
 * @package madrabbit
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Set timestamp custom post field.
 *
 * @param int     $post_ID Post ID.
 * @param WP_Post $post    Post object.
 * @param bool    $update  Whether this is an existing post being updated.
 */
function set_timestamp( $post_ID, $post, $update ) {
	if ( ! metadata_exists( 'post', $post_ID, 'timestamp' ) ) {
		$post_timestamp = time();
		add_post_meta( $post_ID, 'timestamp', $post_timestamp, true );
	}
}
add_action( 'save_post', 'set_timestamp', 10, 3 );

/**
 * Import medias and setup in post.
 *
 * @param int     $post_ID Post ID.
 * @param WP_Post $post    Post object.
 */
function media_importer( $post_ID, $post ) {
	$poster    = get_post_meta( $post_ID, 'poster', true );
	$url       = get_post_meta( $post_ID, 'url', true );
	$timestamp = get_post_meta( $post_ID, 'timestamp', true );
	$title     = get_the_title( $post );
	if ( metadata_exists( 'post', $post_ID, 'url' ) && ! get_attached_media( 'video/mp4', $post ) ) {
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.system_calls_exec
		exec( "TYPE=\"video\" POST_ID=\"$post_ID\" URL=\"$url\" TIMESTAMP=\"$timestamp\" TITLE=\"$title\" /usr/bin/media_importer" );
	}
	if ( metadata_exists( 'post', $post_ID, 'url' ) && ! get_attached_media( 'image', $post ) ) {
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.system_calls_exec
		exec( "TYPE=\"image\" POSTER=\"$poster\" POST_ID=\"$post_ID\" URL=\"$url\" TIMESTAMP=\"$timestamp\" TITLE=\"$title\" /usr/bin/media_importer" );
	}
}
add_action( 'edit_post', 'media_importer', 10, 2 );
