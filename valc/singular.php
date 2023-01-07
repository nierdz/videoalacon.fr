<?php
/**
 * The template for displaying all single posts
 *
 * @package valc
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="wrapper" id="single-wrapper">

	<div class="containeri-fluid" id="content" tabindex="-1">

	<main class="site-main" id="main">

		<div class="row">

		<?php
		while ( have_posts() ) {
			the_post();
			?>

		<div class="col-md-9 pe-0">
			<div class="mx-3">
			<?php $image = get_attached_media( 'image' ); ?>
			<video
				class="video-js"
				controls preload="auto"
				poster="<?php echo esc_url( array_shift( $image )->guid ); ?>"
				data-setup='{"aspectRatio": "16:9", "responsive": true}'
			>
			<?php $video = get_attached_media( 'video/mp4' ); ?>
			<source src="<?php echo esc_url( array_shift( $video )->guid ); ?>" type="video/mp4">
			</video>
			<h1 class="text-center"><?php the_title(); ?></h1>
			<?php the_content(); ?>
			</div>
		</div>

		<div class="col-md-3 ps-0">
			<div class="mx-3">
			<?php print_related_posts( get_the_ID() ); ?>
			</div>
		</div>

			<?php
		}
		?>

		</div><!-- .row -->

	</main><!-- #main -->

	</div><!-- #content -->

</div><!-- #single-wrapper -->

<?php
get_footer();
