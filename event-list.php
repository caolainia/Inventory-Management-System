<?php /*Template Name: Event List Page */
  get_header();
?>

<?php if(is_user_logged_in()): 
  $user = wp_get_current_user();
  $roles = ( array ) $user->roles;
  if ($roles[0] != "subscriber"): ?>
    
  <div id="zhijie-event-list-parent" class="text-center">
      <div class="w-100 zhijie-banner-section">
	      <span class="mask bg-gradient-default opacity-8"></span>
        <div class="zhijie-swiper-caption">
          <b>MyRefic is Terrific. </b><br>
          <span class="event-list-desc"><i>It helps you track and manage all auction events.</i></span><br>
          <span class="event-list-desc"><i>Share the real-time bidding with participants. Start by clicking the button below to create an event.</i></span>
        </div>

        <div class="zhijie-swiper-button-section container">
          <div class="row">
            <div class="col-sm-4 col-xs-6 zhijie-register-button-container">
              <a href="<?php echo home_url().'/event-registration'?>">
                <button class="zhijie-register-button">Register an event</button>
              </a>
            </div>

          </div>
        </div>
      </div>

      <div id="zhijie-event-list" class="mb-5 event-list-container">
        <?php 
        // based on the current user id, get all the events from the event table
        global $wpdb;
        $table_name = $wpdb->prefix.'zhijie_events';
        $user_id = get_current_user_id();
        $prepared_statement = $wpdb->prepare("SELECT * FROM {$table_name} WHERE User_id = %s;", $user_id);
        $values = $wpdb->get_results( $prepared_statement );
        if (sizeof($values) == 0): ?>
            <div id="zhijie-no-event">
              <h2>
                There is no event registered. <br>
                Please go to the Event Registration page and create one!
              </h2>
            </div>
            <?php else:
              $count = 1;
              ?>
            <div class="album shadow bg-white zhijie-album border m-3">
              <div class="container mt-5" id="list-container">
                <h2 class="text-left zhijie-list-header">My Events</h2>
                <div class="row justify-content-end">
                    <div class="col-5">
                      <div class="align-right btn-group" role="group" aria-label="Basic outlined example">
                        <button type="button" class="btn active zhijie-button-outline btn-outline-auctions">Auctions</button>
                        <button type="button" class="btn zhijie-button-outline zhijie-button-outline-active">Inspections</button>
                        <button type="button" class="btn zhijie-button-outline btn-outline-all">Show All</button>
                      </div>
                    </div>

                </div>

                <hr>
                
                <div class="row p-2" id="list-row">
                <?php
                  foreach( $values as $key => $row) :?>
                  
                    <?php
                        // each column in your row will be accessible like this
                        $ID = $row->ID;
                        $Address = $row->Address;
                        $Datetime = $row->Datetime;
                        $Agent = $row->Agent;
                        $note = $row->comment;
                        $post_id = $row->post_id;
                        $thumbnail = get_the_post_thumbnail_url($post_id);
                        $event_type = $row->event_type;
                        ?>

                        <div class="col-md-4 mb-3">
                          <div id="event-card-<?php echo $ID; ?>" data-value="<?php echo get_home_url();?>" data-id="<?php echo $ID; ?>" data-type="<?php echo $event_type; ?>" class=" card zhijie-event-card zhijie-shadow img-responsive">
                            <?php if($event_type === "Inspection"): ?>
                              <div class="zj-inspection-card-banner zhijie-card-header">Inspection</div>
                            <?php else: ?>
                              <div class="zj-auction-card-banner zhijie-card-header">Auction</div>
                            <?php endif; ?>
                              <?php if($thumbnail) :?>
                                <img src="<?php echo $thumbnail?>" class="card-img-top" alt="<?php echo $Address ?>"/>
                              <?php else : ?>
                                <?php if($event_type == "Auction") : ?>
                                    <img src="<?php echo get_stylesheet_directory_uri()."/img/house.jpg"?>" class="card-img-top" alt="<?php echo $Address ?>"/>
                                <?php else : ?>
                                  <img src="<?php echo get_stylesheet_directory_uri()."/img/sam-moqadam-b3kL6kBOEWs-unsplash.jpg"?>" class="card-img-top" alt="<?php echo $Address ?>"/>
                                <?php endif ; ?>

                              <?php endif ; ?>
                              
                              <div class="card-body">
                                <div class="card-title"><b class="zhijie-text"><?php echo $Address; ?></b></div>
                              </div>
                              <ul class="list-group list-group-flush">
                                  <?php if($event_type != "Inspection"): ?>
                                    <li class="list-group-item">Time: <i><?php echo $Datetime; ?></i></li>
                                  <?php else : ?>
                                    <li class="list-group-item">Time: <i>Register your available time for inspection</i></li>
                                  <?php endif ; ?>

                                  <?php if($note): ?>
                                    <li class="list-group-item"><i><?php echo $note; ?></i></li>
                                  <?php else : ?>
                                    <li class="list-group-item"><i>No Comment</i></li>
                                  <?php endif ; ?>
                              </ul>

                            </div>
                        </div>
                            <!-- <div class="zhijie-event-card-right">
                              <?php if ($note): ?>
                                <div class="p-1"><b>Note:</b> <?php// echo $note; ?></div>
                              <?php endif; ?>
                            </div> -->
                  
                    <?php $count++ ?>
                <?php endforeach ; ?>
                </div>
              </div>
            </div>
            
        <?php endif; ?>
      </div>
    </div>
  <?php else : ?>
      <div class="zhijie-page-alert">You have to be a Real Estate Agent account to view this page.</div>
  <?php endif ;?>
<?php else : ?>
    <?php echo '<script>window.location = "'.home_url().'/login'.'" </script>'; ?>
<?php endif ;?>

<?php get_footer();?>