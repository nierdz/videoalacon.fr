<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package madrabbit
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>

<div class="wrapper" id="wrapper-footer">

	<div class="container">

	<div class="row">

		<div class="col-md-12">

		<footer class="site-footer" id="colophon">

			<div class="site-info">

			Ceci est un site parodique !
			<span class="sep"> | </span>
			<a href="https://github.com/nierdz/mad-rabbit.com/issues">Open an issue</a>

			</div><!-- .site-info -->

		</footer><!-- #colophon -->

		</div><!--col end -->

	</div><!-- row end -->

	</div><!-- container end -->

</div><!-- wrapper end -->

</div><!-- #page we need this extra closing tag here -->

<?php wp_footer(); ?>

</body>

</html>
