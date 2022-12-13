<?php
/**
 * Custom function to print related posts
 *
 * @package madrabbit
 */

/**
 * Get related posts
 *
 * @param int $post_id id of the post.
 */
function print_related_posts( $post_id ) {
	global $wpdb;

	$max_related_posts = 30;

	$tags       = wp_list_pluck( get_the_terms( $post_id, 'post_tag' ), 'term_id' );
	$categories = wp_list_pluck( get_the_terms( $post_id, 'category' ), 'term_id' );

	$related_args = array(
		'post_type'      => 'post',
		'posts_per_page' => $max_related_posts,
		'post_status'    => 'publish',
		'post__not_in'   => array( $post_id ),
		'orderby'        => 'rand',
		'tax_query'      => array( // phpcs:ignore
			'relation' => 'OR',
			array(
				'taxonomy' => 'post_tag',
				'field'    => 'id',
				'terms'    => $tags,
			),
			array(
				'taxonomy' => 'category',
				'field'    => 'id',
				'terms'    => $categories,
			),
		),
	);

	$related_query = new WP_Query( $related_args );
	while ( $related_query->have_posts() ) {
		$related_query->the_post();
		?>

		<div class="card mb-3">
			<div class="row g-0">
				<div class="col-md-6">
					<a href="<?php the_permalink(); ?>">
													<?php
													the_post_thumbnail(
														'large',
														array(
															'class' => 'rounded-start related-image-cards',
															'alt' => get_the_title(),
														)
													);
													?>
								</a>
				</div>
				<div class="col-md-6">
					<div class="card-header p-2">
						<?php the_title( sprintf( '<h6 class="card-title"><a href="%s" rel="bookmark" class="text-decoration-none">', esc_url( get_permalink() ) ), '</a></h6>' ); ?>
					</div>
					<div class="card-body p-2">
						<div class="card-text"><i class="bi-calendar"></i> <small class="text-muted"><?php echo get_the_date( 'j F Y' ); ?></small></div>
						<div class="card-text"><i class="bi-bookmark-star"></i> <small class="text-muted">
							<?php
							$categories = get_the_category();
							if ( ! empty( $categories ) ) {
								echo '<a class="text-decoration-none text-muted" href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">' . esc_html( $categories[0]->name ) . '</a>';
							}
							?>
						</small></div>
						<div class="card-text"><i class="bi-clock"></i> <small class="text-muted"><?php echo esc_html( get_post_meta( get_the_ID(), 'video_duration', true ) ); ?></small></div>
					</div>
				</div>
			</div>
		</div>

		<?php
	}
	wp_reset_postdata();

}
?>
