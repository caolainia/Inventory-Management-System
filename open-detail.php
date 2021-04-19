<?php /*Template Name: Open Detail Page */
get_header(); ?>

<?php if(is_user_logged_in()):

    $user = wp_get_current_user();
    $roles = ( array ) $user->roles;
    $user_id = get_current_user_id();
    if ($roles[0] != "subscriber"):
        if(isset($_GET["event_id"]) && $_GET["event_id"]):
            $event_id = $_GET["event_id"];

            global $wpdb;
            $table_name = $wpdb->prefix.'zhijie_events';
            $prepared_statement = $wpdb->prepare( "SELECT * FROM {$table_name} WHERE  ID = %s", $event_id);
            $values = $wpdb->get_results( $prepared_statement );
            $count = 1;
            if (sizeof($values) != 0):
                $row = array_pop(array_reverse($values));
                $registrar_id = $row->User_id;
                if ($registrar_id == $user_id):
                    $address = $row->Address;
                    $datetime = $row->Datetime;
                    $agent = $row->Agent;
                    $note = $row->comment;
                    $post_id = $row->post_id;
                    $event_type = $row->event_type;
                    $url = home_url().'/client-registration?event_id='.$event_id;
                    if ($event_type != "Inspection"): ?>
                        <div class="mb-2 text-left card-header bg-inactive">
                            <h4 class="text-white text-center">Invalid Event Type!</h4>
                        </div>
                    <?php else: ?>
                        <div class="zhijie-detail-card card bg-light mt-5 mb-5 shadow-sm card-md text-center">
                            <div id="zhijie-activate-event" class="mb-4">
                                <div class="mb-2 text-left card-header bg-inactive">
                                    <h4 class="text-white">Here's the info for the house inspection!</h4>
                                </div>
                                <div id="zhijie-event-price-info">
                                    <?php echo do_shortcode( '[kaya_qrcode title_align="alignnone" content="'.$url.'" ecclevel="L" align="alignnone"]' ); ?>
                                    <div>
                                        Save the QR code for others to request an inspection.
                                    </div>

                                    <hr>
                                    <div id="zj-inspection-info-content" class="px-4 py-3">
                                        <div class="my-2 text-left">
                                                <h6 class="zj-einfo-title"><i>Address:</i></h6>
                                                <div class="text-right"><b><?php echo $address;?></b></div>
                                        </div>
                                                                                
                                        <div class="my-2 text-left">
                                                <h6 class="zj-einfo-title"><i>Agent Name:</i></h6>
                                                <div class="text-right"><b><?php echo $agent;?></b></div>
                                        </div>

                                        <div class="my-2 text-left">
                                            <h6 class="zj-einfo-title"><i>Inspection Time:</i></h6>
                                            <div class="text-right"><b><?php echo $datetime;?></b></div>
                                        </div>
                                        <hr>

                                        <?php if (isset($note) && $note) : ?>
                                            <div class="my-2 text-left">
                                                <h6 class="zj-einfo-title">Note:</h6>
                                                <div class="text-right"><b><?php echo $note; ?></b></div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($post_id != 0): ?>
                                            <div class="my-2 text-left">
                                                <h6 class="zj-einfo-title">About This Property: </h6>
                                                <a class="zhijie-link" style="color: #0073e6;" href="<?php echo get_permalink($post_id);?>"><u><?php echo get_permalink($post_id);?></u></a>
                                            </div>
                                        <?php endif; ?>
                                        
                                    </div> <!-- zj-inspection-info-content -->
                                    
                                </div> <!-- zhijie-event-price-info -->                       
                            </div> <!-- zhijie-activate-event -->
                            
                        </div> <!-- zhijie-detail-card -->

                        <?php if($post_id == 0) : ?>
                            <div class="zj-post-section">
                                <div class="zhijie-post-wrapper border border-success p-3 my-3">
                                    <h5 class="p-2"><i>Write a post for the event here</i></h5>
                                    <hr>                          
                                    <?php echo do_shortcode( '[fep_submission_form event_id=' . $event_id . ']' );?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div id="zhijie-clients-list" class="container">
                            <h3>Here is the list of registered clients</h3>
                            <?php 
                            // based on the current user id, get all the events from the event table
                                global $wpdb;
                                $table_name = $wpdb->prefix.'zhijie_client_info';
                                $prepared_statement = $wpdb->prepare("SELECT * FROM {$table_name} WHERE event_id = %s;", $event_id);
                                $values = $wpdb->get_results( $prepared_statement );
                                if (sizeof($values) == 0): ?>
                                    <div id="zhijie-no-client">There is no client registered.</div>
                                <?php else:
                                foreach( $values as $key => $row):
                                    // each column in your row will be accessible like this
                                    $Name = $row->name;
                                    $Address = $row->address;
                                    $Email = $row->email;
                                    $Mobile = $row->mobile;
                                    $Date = $row->date;
                                    $contactDays = $row->contact_day;
                                    $contactTime = $row->contact_time;
                                    ?>
                                    <div id="client-info-card-<?php echo $ID; ?>" data-value="<?php echo get_home_url();?>" data-id="<?php echo $ID; ?>" class="zhijie-client-info-card mb-3 px-3">
                                        <div class="zhijie-client-card-left">
                                            <div class="p-1">Name: <b><?php echo $Name; ?></b></div>
                                            <div class="p-1">Address: <b><?php echo $Address; ?></b></div>
                                            <div class="p-1">Email: <b><?php echo $Email; ?></b></div>
                                            <div class="p-1">Phone: <b><?php echo $Mobile; ?></b></div>
                                        </div>
                                        <div class="zhijie-client-card-right">
                                            <div class="p-1">Best Contact Days: <br>
                                                <b><?php echo $contactDays; ?></b>
                                            </div>
                                            <div class="p-1">Best Contact Time: <br>
                                                <b><?php echo $contactTime; ?></b>
                                            </div>
                                            <div class="p-1">Registered Date: <b><?php echo $Date; ?></b></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                    <?php endif; ?> <!-- Invalid Event -->

                <?php else: ?>
                    <div class="zhijie-page-alert">You cannot view other registrars' events.</div>
                <?php endif; ?>

            <?php else: ?>
                <div class="zhijie-page-alert">Unable to find an event.</div>
            <?php endif; ?>

        <?php else: ?>
            <div class="zhijie-page-alert">Unable to find an event.</div>
        <?php endif; ?>
    <?php else: ?>
        <div class="zhijie-page-alert">You have to be a Real Estate Agent account to view this page.</div>
    <?php endif; ?>

<?php endif; ?>

<?php get_footer();?>