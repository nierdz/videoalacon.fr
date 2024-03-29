<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package valc
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

add_filter( 'body_class', 'understrap_body_classes' );

if ( ! function_exists( 'understrap_body_classes' ) ) {
	/**
	 * Adds custom classes to the array of body classes.
	 *
	 * @param array $classes Classes for the body element.
	 *
	 * @return array
	 */
	function understrap_body_classes( $classes ) {
		// Adds a class of group-blog to blogs with more than 1 published author.
		if ( is_multi_author() ) {
			$classes[] = 'group-blog';
		}
		// Adds a class of hfeed to non-singular pages.
		if ( ! is_singular() ) {
			$classes[] = 'hfeed';
		}

		return $classes;
	}
}

// Removes tag class from the body_class array to avoid Bootstrap markup styling issues.
add_filter( 'body_class', 'understrap_adjust_body_class' );

if ( ! function_exists( 'understrap_adjust_body_class' ) ) {
	/**
	 * Setup body classes.
	 *
	 * @param string $classes CSS classes.
	 *
	 * @return mixed
	 */
	function understrap_adjust_body_class( $classes ) {

		foreach ( $classes as $key => $value ) {
			if ( 'tag' === $value ) {
				unset( $classes[ $key ] );
			}
		}

		return $classes;

	}
}

// Filter custom logo with correct classes.
add_filter( 'get_custom_logo', 'understrap_change_logo_class' );

if ( ! function_exists( 'understrap_change_logo_class' ) ) {
	/**
	 * Replaces logo CSS class.
	 *
	 * @param string $html Markup.
	 *
	 * @return string
	 */
	function understrap_change_logo_class( $html ) {

		$html = str_replace( 'class="custom-logo"', 'class="img-fluid"', $html );
		$html = str_replace( 'class="custom-logo-link"', 'class="navbar-brand custom-logo-link"', $html );
		$html = str_replace( 'alt=""', 'title="Home" alt="logo"', $html );

		return $html;
	}
}

if ( ! function_exists( 'understrap_pingback' ) ) {
	/**
	 * Add a pingback url auto-discovery header for single posts of any post type.
	 */
	function understrap_pingback() {
		if ( is_singular() && pings_open() ) {
			echo '<link rel="pingback" href="' . esc_url( get_bloginfo( 'pingback_url' ) ) . '">' . "\n";
		}
	}
}
add_action( 'wp_head', 'understrap_pingback' );

if ( ! function_exists( 'understrap_mobile_web_app_meta' ) ) {
	/**
	 * Add mobile-web-app meta.
	 */
	function understrap_mobile_web_app_meta() {
		echo '<meta name="mobile-web-app-capable" content="yes">' . "\n";
		echo '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
		echo '<meta name="apple-mobile-web-app-title" content="' . esc_attr( get_bloginfo( 'name' ) ) . ' - ' . esc_attr( get_bloginfo( 'description' ) ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'understrap_mobile_web_app_meta' );

// Escapes all occurances of 'the_archive_description'.
add_filter( 'get_the_archive_description', 'understrap_escape_the_archive_description' );

if ( ! function_exists( 'understrap_escape_the_archive_description' ) ) {
	/**
	 * Escapes the description for an author or post type archive.
	 *
	 * @param string $description Archive description.
	 * @return string Maybe escaped $description.
	 */
	function understrap_escape_the_archive_description( $description ) {
		if ( is_author() || is_post_type_archive() ) {
			return wp_kses_post( $description );
		} else {
			/*
			* All other descriptions are retrieved via term_description() which returns
			* a sanitized description.
			*/
			return $description;
		}
	}
} // End of if function_exists( 'understrap_escape_the_archive_description' ).

// Escapes all occurances of 'the_title()' and 'get_the_title()'.
add_filter( 'the_title', 'understrap_kses_title' );

// Escapes all occurances of 'the_archive_title' and 'get_the_archive_title()'.
add_filter( 'get_the_archive_title', 'understrap_kses_title' );

if ( ! function_exists( 'understrap_kses_title' ) ) {
	/**
	 * Sanitizes data for allowed HTML tags for post title.
	 *
	 * @param string $data Post title to filter.
	 * @return string Filtered post title with allowed HTML tags and attributes intact.
	 */
	function understrap_kses_title( $data ) {
		// Tags not supported in HTML5 are not allowed.
		$allowed_tags = array(
			'abbr'             => array(),
			'aria-describedby' => true,
			'aria-details'     => true,
			'aria-label'       => true,
			'aria-labelledby'  => true,
			'aria-hidden'      => true,
			'b'                => array(),
			'bdo'              => array(
				'dir' => true,
			),
			'blockquote'       => array(
				'cite'     => true,
				'lang'     => true,
				'xml:lang' => true,
			),
			'cite'             => array(
				'dir'  => true,
				'lang' => true,
			),
			'dfn'              => array(),
			'em'               => array(),
			'i'                => array(
				'aria-describedby' => true,
				'aria-details'     => true,
				'aria-label'       => true,
				'aria-labelledby'  => true,
				'aria-hidden'      => true,
				'class'            => true,
			),
			'code'             => array(),
			'del'              => array(
				'datetime' => true,
			),
			'ins'              => array(
				'datetime' => true,
				'cite'     => true,
			),
			'kbd'              => array(),
			'mark'             => array(),
			'pre'              => array(
				'width' => true,
			),
			'q'                => array(
				'cite' => true,
			),
			's'                => array(),
			'samp'             => array(),
			'span'             => array(
				'dir'      => true,
				'align'    => true,
				'lang'     => true,
				'xml:lang' => true,
			),
			'small'            => array(),
			'strong'           => array(),
			'sub'              => array(),
			'sup'              => array(),
			'u'                => array(),
			'var'              => array(),
		);
		$allowed_tags = apply_filters( 'understrap_kses_title', $allowed_tags );

		return wp_kses( $data, $allowed_tags );
	}
} // End of if function_exists( 'understrap_kses_title' ).

/**
 * Remove width and height attributes from thumbnail.
 *
 * @param string $html generated by the_post_thumbnail().
 *
 * @return string
 */
function remove_img_attr( $html ) {
	return preg_replace( '/(width|height)="\d+"\s/', '', $html );
}
add_filter( 'post_thumbnail_html', 'remove_img_attr' );


/**
 * Print quote shortcode.
 * Ugly shit but don't want to spend time to learn to use JS in block editor
 * https://developer.wordpress.org/block-editor/how-to-guides/javascript/
 *
 * @param array  $attr {
 * Attributes of the shortcode.
 *
 *  @type string $author Quote author.
 * }
 * @param string $content Shortcode content.
 * @return string|void HTML content to display video.
 */
function pretty_bootstrap_quote( $attr, $content = null ) {
	$a       = shortcode_atts(
		array(
			'author' => '',
		),
		$attr
	);
	$output  = '<figure class="p-4">';
	$output .= '<blockquote class="blockquote">';
	$output .= '<p class="pb-2">';
	$output .= '"' . $content . '"';
	$output .= '</p>';
	$output .= '</blockquote>';
	$output .= '<figcaption class="blockquote-footer mb-0">';
	$output .= '' . $a['author'];
	$output .= '</figcaption>';
	$output .= '</figure>';
	return $output;
}
add_shortcode( 'quote', 'pretty_bootstrap_quote' );
