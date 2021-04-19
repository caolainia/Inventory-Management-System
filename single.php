<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header(); 
?>

<?php 
		$post_id = get_the_ID();
		$post_author_id = get_post_field( 'post_author', $post_id );
		$author_info = get_userdata($post_author_id);
	?>
	<div id="primary" <?php astra_primary_class();?>>

		<?php astra_primary_content_top(); ?>
        <article class="bg-white p-5">
			
			<div class="row">
				<div class="col-md-8 blog-main">
					<div class="ast-post-format- single-layout-1">
						<h3><?php echo the_title(); ?></h3>
						<p class="text-pink"> Posted on <?php echo get_the_date();?> | <?php echo get_comments_number(); ?> Comments | <?php if(get_current_user_id() == $post_author_id) { edit_post_link($text = 'Edit'," ", " ", $id = $post_id, $class = "zhijie-link") ;} ?></p>
					</div>

                    <?php  $args = array(
							'post_parent'    => $post_id,
							'post_type'      => 'attachment',
							'numberposts'    => -1, // show all
							'post_status'    => 'any',
							'post_mime_type' => 'image',
							'orderby'        => 'menu_order',
							'order'           => 'ASC'
								);

						$attachments = get_posts($args);
						$urls = array();
						foreach($attachments as $att_id => $attachment){
							$url = wp_get_attachment_url($attachment->ID);
							//Get rid of the low quality ones
							$pattern = "/150x150/";
							if(preg_match($pattern, $url)){
								continue;
							}
							array_push($urls, $url);
						}
						?>
						<?php $img_count = 0;
						?>
						<?php if ($urls): ?>
                            <div class="zhijie-carousel-wrapper">
                                <div id="carouselExampleIndicators" class="carousel slide max-size-control" data-ride="carousel">
                                    <ol class="carousel-indicators">
                                        <?php foreach($urls as $url): ?>
                                            <?php if($img_count == 0): ?>
                                                <li data-target="#carouselExampleIndicators" data-slide-to="0"></li>
                                            <?php else :?>
                                                <li data-target="#carouselExampleIndicators" data-slide-to="<?php echo $count?>"></li>
                                            <?php endif;?>

                                            <?php 
                                                $img_count++;
                                            ?>
                                        <?php endforeach; ?>
                                    </ol>
                                    <div class="carousel-inner">
                                        
                                    <?php $img_count = 0;
                                                ?>
                                            <?php foreach($urls as $url): ?>

                                                <?php if($img_count == 0): ?>
                                                    <div class="carousel-item active">
                                                        <img class="d-block w-100" src="<?php echo $url; ?>" alt="Slide">
                                                    </div>
                                                <?php else :?>
                                                    <div class="carousel-item">
                                                        <img class="d-block w-100" src="<?php echo $url; ?>" alt="Slide">
                                                    </div>
                                                <?php endif;?>
                                                

                                                <?php 
                                                    $img_count++;
                                                ?>
                                            <?php endforeach; ?>
                                    </div>
                                    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </div>
                            </div>

						<?php endif; ?>
                        <div class="mt-5">
							<h3 class="pb-4 mb-4 font-italic border-bottom">Post Content</h3>
							<?php echo get_the_content(); ?>
							
						</div>
                </div>

                <aside class="col-md-4 blog-sidebar">
					<div class="card card-profile shadow">
                        <div class="card p-2">
                            <h4 class="font-italic header-author">Author</h4>
							<div class="row justify-content-center">
								<div class="p-3 mb-3 rounded text-center">
									
									<div class="card-profile-image">
										<img class="rounded-circle" src="<?php echo get_avatar_url($post_author_id);?>" />
									</div>
									<h5><?php echo $author_info->user_login; ?></h5>
								</div>
							</div>

                        </div>
                    </div>
                </aside>
            </div>
        </article>

        <?php 
			$args = array('post_id'=>$post_id);
			$comments = get_comments($args); 
		?>
			<?php foreach($comments as $comment) : ?>
				<div class="mt-5 bg-light col-md-8">
					<?php $comment_info = get_comment($comment); 
						$emai = $comment_info->comment_author_email;
					?>
					<div class="row">
						<div class="col-5 mt-4">
							<h5 class="text-pink"><?php echo $comment->comment_author;?></h5>
						</div>
						<div class="ml-auto mt-2">
						
							<img class="rounded-circle comment-photo" src="<?php echo get_avatar_url($emai);?>"/>
						</div>
					</div>
					<p class="text-break"><?php echo $comment->comment_content;?></p>
					<p class="font-italic">At <?php echo $comment->comment_date;?></p>
					<?php
						$post_id = get_the_ID();
						$comment_id =get_comment_ID();
						
						//get the setting configured in the admin panel under settings discussions "Enable threaded (nested) comments  levels deep"  
						$max_depth = get_option('thread_comments_depth');
						//add max_depth to the array and give it the value from above and set the depth to 1
						$default = array(
							'add_below'  => 'comment',
							'respond_id' => 'respond',
							'reply_text' => __('Reply'),
							'login_text' => __('Log in to Reply'),
							'depth'      => 1,
							'before'     => '',
							'after'      => '',
							'max_depth'  => $max_depth
							);

					?>
					<p><?php comment_reply_link($default,$comment_id,$post_id);?>|<?php edit_comment_link('Edit');?></p>
					<hr/>
				</div>
		<?php endforeach?>
		
		<div class="mt-5">
			<?php echo comment_form(); ?>
		</div>
    </div>

<?php get_footer();?>
