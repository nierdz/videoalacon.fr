<?php
/**
 * Download video and thumbnail and setup associated custom post fields
 *
 * @package valc
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

/**
 * Set duration custom post field.
 *
 * @param int     $post_ID Post ID.
 * @param WP_Post $post    Post object.
 */
function set_duration( $post_ID, $post ) {
	if ( metadata_exists( 'post', $post_ID, 'url' ) && ! metadata_exists( 'post', $post_ID, 'duration' ) && get_attached_media( 'video/mp4', $post ) && get_attached_media( 'image', $post ) ) {
		// We can't get path of mp4 directly so we use image instead and replace extension.
		$image_attached = get_attached_media( 'image', $post );
		$image_file     = wp_get_original_image_path( $image_attached[ array_key_first( $image_attached ) ]->ID );
		$image_pathinfo = pathinfo( $image_file );
		$video_file     = $image_pathinfo['dirname'] . '/' . $image_pathinfo['filename'] . '.mp4';
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.system_calls_shell_exec
		$duration = shell_exec( "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 -sexagesimal $video_file | awk -F: '{printf \"%02d:%02d\",$2,$3}'" );
		add_post_meta( $post_ID, 'duration', $duration, true );
	}
}
add_action( 'edit_post', 'set_duration', 11, 2 );
