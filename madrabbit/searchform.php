<?php
/**
 * The template for displaying search forms
 *
 * @package madrabbit
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<form class="form-inline" method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">
	<label class="sr-only" for="s">search</label>
	<input class="form-control mr-sm-2" id="s" name="s" type="text"
	value="<?php the_search_query(); ?>">
	<input class="btn btn-outline-light my-2" id="searchsubmit" name="submit" type="submit"
	value="Recherche">
</form>
