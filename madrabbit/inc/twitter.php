<?php
/**
 * Post article to twitter when published
 *
 * @package madrabbit
 */

use Abraham\TwitterOAuth\TwitterOAuth;
use function Env\env;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Set timestamp custom post field.
 *
 * @param int     $post_ID Post ID.
 * @param WP_Post $post    Post object.
 * @param string  $old_status The status the post is changing from.
 */
function post_twitter( $post_ID, $post, $old_status ) {
	if ( 'publish' !== $old_status && 'development' !== env( 'WP_ENV' ) ) {
		$connection = new TwitterOAuth(
			env( 'TWITTER_API_KEY' ),
			env( 'TWITTER_API_KEY_SECRET' ),
			env( 'TWITTER_ACCESS_TOKEN' ),
			env( 'TWITTER_ACCESS_TOKEN_SECRET' )
		);
		$connection->setApiVersion( '2' );

		// Fake the post is published to use get_permalink().
		$post->post_status = 'publish';
		$text              = get_permalink( $post );
		$response          = $connection->post( 'tweets', array( 'text' => $text ), true );
	}
}
add_action( 'publish_post', 'post_twitter', 10, 3 );
