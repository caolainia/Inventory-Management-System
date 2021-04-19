<?php /*Template Name: Event Detail Page */
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
                    $event_status = $row->status;
                    $contactNumberStr = $row->contact_numbers;
                    $contactNumbersArray = explode(",", $contactNumberStr, 4);
                    $note = $row->comment;
                    $post_id = $row->post_id;
                    $event_type = $row->event_type;
                    $url = home_url().'/auction-details?event_id='.$event_id ."&operation=1";
                    date_default_timezone_set('Australia/Adelaide');
                    $current_time = new DateTime(date('Y-m-d H:i:s'));
                    if ($event_type == "Inspection"): ?>
                        <div class="mb-2 text-left card-header bg-inactive">
                            <h4 class="text-white text-center">Invalid Event!</h4>
                        </div>
                        
                    <?php else: ?>
                    
                        <div class="zhijie-detail-card card bg-light mt-5 mb-5 shadow-sm card-md text-center">
                            
                            <div id="zhijie-activate-event" class="mb-4">

                                <?php if(time() < strtotime($datetime) + 86400) : ?>
                                    <?php if ($event_status != "Active"): ?>
                                        <div class="mb-2 text-left card-header bg-inactive">
                                            <h4 class="text-white">The auction is not alive yet</h4>
                                        </div>
                                            
                                        <div class="align-items-center">
                                            <button id="zhijie-event-button" data-id="<?php echo $event_id;?>" class="zhijie-register-button zhijie-start-event mt-3">Start the event</button>
                                            <div id="zj-start-event-caption" style="font-size: 0.8rem;">Activate the auction and start bidding</div>
                                        </div>
                                        <div id="zhijie-event-price-info" style="display: none">
                                            
                                            <?php echo do_shortcode( '[kaya_qrcode title_align="alignnone" content="'.$url.'" ecclevel="L" align="alignnone"]' ); ?>
                                            <div>
                                                Save the QR code for bidding <br>
                                                Or click the button to bid.
                                            </div>
                                            <a href="<?php echo get_home_url() . "/auction-operation?event_id=" . $event_id ?>">
                                                <button class="zhijie-register-button">Bid</button>
                                            </a>
                                        </div>
                                        
                                    <?php else: ?>
                                        <div id="zhijie-event-price-info">
                                            <div class="mb-2 text-left card-header bg-active">
                                                <h4 class="text-white">The auction is alive!</h4>
                                            </div>
                                            <?php echo do_shortcode( '[kaya_qrcode title_align="alignnone" content="'.$url.'" ecclevel="L" align="alignnone"]' ); ?>
                                            <div>
                                                Scan the QR code to view the bidding.<br>
                                                Click the button to bid.
                                            </div>
                                            <a href="<?php echo get_home_url() . "/auction-operation?event_id=" . $event_id ?>">
                                                <button class="zhijie-register-button">Bid</button>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                <?php else : ?>
                                    <div class="mb-2 text-left card-header bg-expired"><h4 class="text-white">The auction is expired</h4></div>
                                <?php endif ; ?>
                            </div>
                            
                            <hr>
                            <div class="px-4 py-3">
                                <div class="my-2 text-left">
                                        <h6 class="zj-einfo-title"><i>Venue:</i></h6>
                                        <div class="text-right"><b><?php echo $address;?></b></div>
                                </div>
                                <div class="my-2 text-left">
                                        <h6 class="zj-einfo-title"><i>Time:</i></h6>
                                        <div class="text-right"><b><?php echo $datetime;?></b></div>
                                </div>
                                <div class="my-2 text-left">
                                
                                        <h6 class="zj-einfo-title"><i>Host Agent:</i></h6>
                                        <div class="text-right"><b><?php echo $agent;?></b></div>
                                </div>
                                <hr>
                                <?php if ($post_id != 0): ?>
                                    <div class="my-2 text-left">
                                        <h6 class="zj-einfo-title">About This Property: </h6>
                                        <a class="zhijie-link" style="color: #0073e6;" href="<?php echo get_permalink($post_id);?>"><u><?php echo get_permalink($post_id);?></u></a>
                                    </div>
                                <?php endif; ?>


                                <?php if ($note): ?>
                                    <div class="my-2 text-left">
                                        <h6 class="zj-einfo-title">Note:</h6>
                                        <div class="text-right"><b><?php echo $note; ?></b></div>
                                    </div>
                                <?php endif; ?>

                                <?php 
                                $count = 1;
                                foreach($contactNumbersArray as $number) :
                                    if ($number && is_numeric($number)): ?>
                                        <div class="my-2 text-left">
                                            <h6 class="zj-einfo-title">Contact Number <?php echo $count;?>: </h6>
                                            <div class="text-right"><b><?php echo $number; ?></b></div>
                                        </div>
                                    <?php endif; ?>
                                    <?php $count += 1;?>
                                <?php endforeach ; ?>
                            </div>
                    
                        </div>  <!-- <div class="zhijie-detail-card"></div> -->

                        <?php if($post_id == 0) : ?>
                            <div class="zj-post-section bg-white">
                                <div class="zhijie-post-wrapper border border-success p-3 my-3">
                                    <h5 class="p-2"><i>Write a post for the event here</i></h5>
                                    <hr>                          
                                    <?php echo do_shortcode( '[fep_submission_form event_id=' . $event_id . ']' );?>
                                </div>
                            </div>
                        <?php endif; ?>

                    <?php endif; ?>

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