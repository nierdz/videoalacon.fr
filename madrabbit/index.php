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

		<div class="row row-cols-1 row-cols-sm-2 row-cols-md-4">

		<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				?>

			<div class="col">
			<div class="card mb-3">
				<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'large', array( 'class' => 'index-image-cards' ) ); ?></a>
				<div class="card-body text-truncate">
				<?php the_title( sprintf( '<h6 class="card-title"><a href="%s" class="text-decoration-none" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h6>' ); ?>
				<div class="card-text"><i class="bi-calendar"></i> <small class="text-muted"><?php echo get_the_date( 'j F Y' ); ?></small></div>
				<div class="card-text"><i class="bi-clock"></i> <small class="text-muted"><?php echo esc_html( get_post_meta( get_the_ID(), 'video_duration', true ) ); ?></small></div>
				</div>
			</div>
			</div>

				<?php
			}
		}
		?>

		</div>

		</main><!-- #main -->

		<!-- The pagination component -->
		<?php understrap_pagination(); ?>

	</div><!-- .row -->

	</div><!-- #content -->

</div><!-- #index-wrapper -->

<?php
get_footer();
