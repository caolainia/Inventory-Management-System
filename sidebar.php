<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$sidebar = apply_filters( 'astra_get_sidebar', 'sidebar-1' );

echo '<div ';
	echo astra_attr(
		'sidebar',
		array(
			'id'    => 'secondary',
			'class' => join( ' ', astra_get_secondary_class() ),
			'role'  => 'complementary',
		)
	);
	echo '>';
	?>


	<div class="zhijie-sidebar-body mr-4">

		<div class="card zhijie-sidebar-post-section px-2">
			<span class="pt-3 zhijie-sidebar-post-section-title">All Posts</span>
			<hr>
			<?php 
				$post_ids = get_posts(array(
					"numberposts" => 20,
					'fields' => 'ids'
				));
		
				foreach ($post_ids as $post_id ) {
					$title = get_post_field( 'post_title', $post_id ); 
					$link = get_post_permalink($post_id)
					?>
					<a href="<?php echo $link; ?>" class="zhijie-post-title">
						<?php echo $title; ?>
					</a>

				<?php } 
			?>
		</div>

	</div>
</div><!-- #secondary -->
