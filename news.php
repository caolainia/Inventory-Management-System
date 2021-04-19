<?php /*Template Name: Zhijie News Page */
get_header();

?>

<div class="zhijie-home-body text-center">

	<div class="w-100 zhijie-banner-section">
	  <span class="mask bg-gradient-default opacity-8"></span>

	  <div class="zhijie-swiper-caption">
	    <b>MyRefic is Terrific. </b><br>
	    <span><i>It helps you track and manage all auction events on as well as to store valuable records to Google Sheets.</i></span>
	  </div>

	  <div class="zhijie-swiper-button-section container">
	    <div class="row">
	      <div class="col-sm-4 col-xs-6 zhijie-register-button-container">
	        <a href="<?php echo home_url().'/event-list'?>">
	          <button class="zhijie-register-button">Learn More</button>
	        </a>
	      </div>
	    </div>
	  </div>
	</div>

	<div id="zhijie-news-section">
	    <div class="card px-2 my-2 py-1 pb-3">
	     <span class="pt-3 zhijie-sidebar-post-section-title">Recent Posts</span>
	     <hr>
		 <?php 
			$post_ids = get_posts(array(
				"numberposts" => 8,
				'fields' => 'ids'
			)); 
			if (sizeof($post_ids) != 0):
			?>
		    <div class="container">
		        <div class="row">
		            <?php            
		              foreach ($post_ids as $post_id ):
		                $content_post = get_post($post_id);
		                $title = get_post_field( 'post_title', $post_id ); 
		                $link = get_post_permalink($post_id);
						$content = get_the_excerpt($post_id);
						$content = substr($content, 0, 200) . "...";
						//$content = substr($content, 0, 300);
						$thumbnail = get_the_post_thumbnail_url($post_id, 'thumbnail');
		                // $content = apply_filters('the_content', $content);
		                // $content = str_replace(']]>', ']]&gt;', $content);
		                ?>
						<div class="mb-3 col-md-4 col-sm-8">
							<div class="card zhijie-shadow zhijie-event-card">
								<?php if($thumbnail) :?>
									<img src="<?php echo $thumbnail?>" class="card-img-top zhijie-img-fluid thumbnail" alt="<?php echo $title;?>">
								<?php else : ?>
									<img src="<?php echo get_stylesheet_directory_uri()."/img/house.jpg"?>" class="card-img-top zhijie-img-fluid thumbnail" alt="<?php echo $title;?>">
								<?php endif ; ?>
								
								<div class="card-body px-2">
										<h5 class="card-title">
											<a class="zhijie-link" href="<?php echo get_permalink($post_id)?>" title="<?php echo $title; ?>"><?php echo $title; ?></a>
										</h5>
										<p class="card-text"><?php echo $content; ?></p>

								</div>
								<div class="card-footer bg-white">
									<a href="<?php echo $link; ?>">
										<button id="zhijie-continue-reading-button">Continue Reading</button>
									</a>
								</div>
							</div>
						</div>

		              <?php endforeach; ?>
		        </div>
		     </div>

	 
			<?php else: ?>
				<div class="">There is no post recently. Please come back later.</div>
			<?php endif; ?>

	 	</div>
	 </div>
	
</div>

<?php get_footer(); ?>