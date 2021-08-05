<?php
/**
 * The main template file
 *
 * @package madrabbit
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="wrapper" id="index-wrapper">

  <div class="container" id="content" tabindex="-1">

    <div class="row">

      <main class="site-main" id="main">

        <?php
        if ( have_posts() ) {
          while ( have_posts() ) {
            the_post();
        ?>

        <div class="card mb-3">
          <div class="row no-gutters">
            <div class="col-md-4">
              <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large'); ?></a>
            </div>
            <div class="col-md-8">
              <div class="card-body">
                <?php the_title( sprintf( '<h2 class="card-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
                <p class="card-text"><?php the_excerpt(); ?></p>
                <p class="card-text"><small class="text-muted">Publié le <?php echo get_the_date('F j, Y') ?></small></p>
              </div>
            </div>
          </div>
        </div>

        <?php
          }
        }
        ?>

      </main><!-- #main -->

      <!-- The pagination component -->
      <?php understrap_pagination(); ?>

    </div><!-- .row -->

  </div><!-- #content -->

</div><!-- #index-wrapper -->

<?php
get_footer();
