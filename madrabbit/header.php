<?php
/**
 * The header for our theme
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package madrabbit
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<?php // phpcs:ignore Squiz.PHP.EmbeddedPhp.ContentBeforeOpen ?>
<meta name="description" content="<?php
if ( is_single() ) {
	$content     = get_the_content();
	$description = wp_trim_words( trim( wp_strip_all_tags( strip_shortcodes( $content ) ) ), 55, '' );
	echo esc_textarea( $description );
} else {
	bloginfo( 'name' );
}
// phpcs:ignore Squiz.PHP.EmbeddedPhp.ContentAfterEnd
?>" />
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="profile" href="http://gmpg.org/xfn/11">
<?php wp_head(); ?>
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url( site_url() ); ?>/images/favicons/apple-touch-icon.png">
<link rel="icon" type="image/png" href="<?php echo esc_url( site_url() ); ?>/images/favicons/favicon-32x32.png" sizes="32x32">
<link rel="icon" type="image/png" href="<?php echo esc_url( site_url() ); ?>/images/favicons/favicon-16x16.png" sizes="16x16">
<link rel="mask-icon" href="<?php echo esc_url( site_url() ); ?>/images/favicons/safari-pinned-tab.svg" color="#333333">
<link rel="shortcut icon" href="<?php echo esc_url( site_url() ); ?>/images/favicons/favicon.ico">
<meta name="msapplication-config" content="<?php echo esc_url( site_url() ); ?>/images/favicons/browserconfig.xml">
</head>

<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebSite">
<div class="site" id="page">

	<div id="wrapper-navbar">

	<nav id="main-nav" class="navbar navbar-expand-md navbar-dark bg-dark">
		<div class="container-fluid">

		<a class="navbar-brand" rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" itemprop="url">
			<img src="/images/banner-72.png" width="50" height="50" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
		</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

		<?php
		$madrabbit_header_search_form  = '';
		$madrabbit_header_search_form .= '<form class="d-flex" method="get" id="searchform" action="' . esc_url( home_url( '/' ) ) . '" role="search">';
		$madrabbit_header_search_form .= '  <input class="form-control me-2" id="s" name="s" type="search" placeholder="Recherche" value="' . get_search_query() . '">';
		$madrabbit_header_search_form .= '  <button class="btn btn-outline-success" type="submit" id="searchsubmit" name="submit">Rechercher</button>';
		$madrabbit_header_search_form .= '</form>';
		wp_nav_menu(
			array(
				'theme_location'  => 'primary',
				'depth'           => 1,
				'container_class' => 'collapse navbar-collapse',
				'container_id'    => 'navbarSupportedContent',
				'menu_class'      => 'navbar-nav me-auto mb-2 mb-md-0',
				'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
				'walker'          => new WP_Bootstrap_Navwalker(),
				'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>' . $madrabbit_header_search_form,
			)
		);
		?>

		</div>
	</nav><!-- .navbar -->

	</div><!-- #wrapper-navbar end -->
