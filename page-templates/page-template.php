<?php
/**
 * Template Name: Astra Child Page Template
 *
 * @package WordPress
 * @subpackage Arsta
 * @since 1.0
 */

get_header(); 

global $wp_query;

echo $query_vars;
if($wp_query->query_vars['user_profile']) {

	echo $wp_query;

}
?>


<?php if ( astra_page_layout() == 'left-sidebar' ) : ?>

	<?php get_sidebar(); ?>

<?php endif ?>

	<div id="primary" <?php astra_primary_class(); ?>>

		<?php astra_primary_content_top(); ?>

		<?php astra_content_page_loop(); ?>
	<h1>Text</h1>
	</div><!-- #primary -->

<?php if ( astra_page_layout() == 'right-sidebar' ) : ?>

	<?php get_sidebar(); ?>

<?php endif ?>
<?php get_footer(); ?>
