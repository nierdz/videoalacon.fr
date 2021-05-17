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
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="profile" href="http://gmpg.org/xfn/11">
  <?php wp_head(); ?>
  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo site_url(); ?>/images/favicons/apple-touch-icon.png">
  <link rel="icon" type="image/png" href="<?php echo site_url(); ?>/images/favicons/favicon-32x32.png" sizes="32x32">
  <link rel="icon" type="image/png" href="<?php echo site_url(); ?>/images/favicons/favicon-16x16.png" sizes="16x16">
  <link rel="mask-icon" href="<?php echo site_url(); ?>/images/favicons/safari-pinned-tab.svg" color="#333333">
  <link rel="shortcut icon" href="<?php echo site_url(); ?>/images/favicons/favicon.ico>
  <meta name="msapplication-config" content="<?php echo site_url(); ?>/images/favicons/browserconfig.xml">
  <?php if(is_single()) {
  $video_timestamp = get_post_meta( $post->ID, 'video_timestamp', true ); ?>
  <meta property="og:video" content="https://media.mad-rabbit.com/videos/<?php echo $video_timestamp; ?>.mp4" />
  <meta property="og:video:type" content="video/mp4" />
  <meta property="og:video:width" content="640" />
  <meta property="og:video:height" content="480" />
  <?php } ?>
</head>

<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebSite">
<div class="site" id="page">

  <!-- ******************* The Navbar Area ******************* -->
  <div id="wrapper-navbar">

    <a class="skip-link sr-only sr-only-focusable" href="#content">Voir la vidéo</a>

    <nav id="main-nav" class="navbar navbar-expand-md navbar-dark bg-primary" aria-labelledby="main-nav-label">

      <h2 id="main-nav-label" class="sr-only">Main Navigation</h2>

      <div class="container">

          <!-- Your site title as branding in the menu -->

            <?php if ( is_front_page() && is_home() ) : ?>

              <h1 class="navbar-brand mb-0"><a rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" itemprop="url"><?php bloginfo( 'name' ); ?></a></h1>

            <?php else : ?>

              <a class="navbar-brand" rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" itemprop="url"><?php bloginfo( 'name' ); ?></a>

            <?php endif; ?>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle navigation', 'understrap' ); ?>">
          <span class="navbar-toggler-icon"></span>
        </button>

        <!-- The WordPress Menu goes here -->
        <?php
        wp_nav_menu(
          array(
            'menu'            => 'Top Menu',
            'theme_location'  => 'primary',
            'container_class' => 'collapse navbar-collapse',
            'container_id'    => 'navbarNavDropdown',
            'menu_class'      => 'navbar-nav ml-auto',
            'menu_id'         => 'main-menu',
            'depth'           => 1,
            'walker'          => new Understrap_WP_Bootstrap_Navwalker()
          )
        );
        ?>
      </div><!-- .container -->

     <?php get_search_form(); ?>

    </nav><!-- .site-navigation -->

  </div><!-- #wrapper-navbar end -->
