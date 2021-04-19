<?php /*Template Name: Auction Operation Page */
get_header();

$event_id = -1;
$operation = 0;

if(is_user_logged_in()):
    $user_id = get_current_user_id();
    $auction_list = get_user_meta($user_id, 'interested_auction'); ?>

    <div id="zhijie-auction-operation-body" class="text-center">
    <?php if(isset($_GET["event_id"]) && $_GET["event_id"]):
        $event_id = $_GET["event_id"];
        global $wpdb;
        $table_name = $wpdb->prefix.'zhijie_events';
        $prepared_statement = $wpdb->prepare( "SELECT * FROM {$table_name} WHERE  ID = %s", $event_id);
        $values = $wpdb->get_results( $prepared_statement );
        if (sizeof($values) != 0):
            $row = array_pop(array_reverse($values));
            $registrar_id = $row->User_id;
            if ($registrar_id == $user_id):
                $id = $row->ID;
                $address = $row->Address;
                $datetime = $row->Datetime;
                $agent = $row->Agent;
                $event_status = $row->status; ?>
                
                <?php if ($event_status == "Active"): ?>
                    <div class="mt-3">
                        <h5>You are editing for the event at <br><?php echo $address; ?></h5><hr>
                    </div>

                    <div class="zhijie-auction-edit">
                        <div class="zhijie-bidding-form container">
                        <?php 
                        $auction_table = $wpdb->prefix.'zhijie_auction_new';
                        $auction_statement = $wpdb->prepare( "SELECT * FROM {$auction_table} WHERE  event_id = %s", $event_id);
                        $auction_values = $wpdb->get_results( $auction_statement );
                        // info to show
                        $price_to_show = "There is no bidding yet.";
                        $going = "N/A";

                        if (sizeof($auction_values) > 0) {
                            $row = array_pop(array_reverse($auction_values));
                            $price = intval($row->price);
                            $price_to_show = "$" . strval(number_format($price));
                            $auctioneer_call = $row->auctioneer_call;
                            $going = "Once";
                            if ($auctioneer_call == 2) {
                                $going = "Twice";
                            } else if ($auctioneer_call == 3) {
                                $going = "Gone";
                            }
                            $date_time = $row->date_time; 
                        } ?>
                            <div class="zhijie-bidding-info pl-4" style="font-size: 1.2rem;">
                                <div class="row mb-3">
                                    <label class="col-xs-3 col-form-label">Current Bidding Price: </label>
                                    <div class="col-xs-9 col-form-label ml-1 font-weight-bold">
                                        <span id="nowPrice"><?php echo $price_to_show; ?></span>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-xs-3 col-form-label" >Going:</label>
                                    <div class="col-xs-9 col-form-label ml-1 font-weight-bold" id="nowGoing">
                                        <?php echo $going; ?>
                                    </div>
                                </div>
                            </div>
                        
                            <hr>
                            <div class="row mt-3">
                                <label class="col-sm-2 col-form-label text-left"><b>New Bidding<span style="color:red">*</span>:</b></label>
                                <div class="col-sm-10">
                                    <input name="bidPrice" type="text" class="form-control" id="bid-price" value="<?php if (isset($price)) {echo $price;} ?>" required>
                                    <div id="bidding-price-button-list" class="mt-1">
                                        <button id="minus5000" class="zhijie-price-edit-button edit-left">+1k</button>
                                        <button id="minus2000" class="zhijie-price-edit-button edit-left">+2k</button>
                                        <button id="minus1000" class="zhijie-price-edit-button edit-left">+5k</button>
                                        <button id="add1000" class="zhijie-price-edit-button edit-right">+10k</button>
                                        <button id="add2000" class="zhijie-price-edit-button edit-right">+20k</button>
                                        <button id="add5000" class="zhijie-price-edit-button edit-right">+50k</button>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <label class="col-sm-2 col-form-label text-left"><b>Going<span style="color:red">*</span>: </b></label>
                                <div class="col-sm-10 zhijie-going-button-list" role="group">
                                    <div class="form-check form-check-inline">
                                        <input type="radio" name="inlineRadioOptions" id="zhijie-inline-radio1" data-id="<?php echo $event_id;?>" value="1" class="zhijie-going-button-1"checked>
                                        <label class="form-check-label ml-1" for="zhijie-inline-radio1">Once</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" name="inlineRadioOptions" id="zhijie-inline-radio2" data-id="<?php echo $event_id;?>" value="2" class="zhijie-going-button-2">
                                        <label class="form-check-label ml-1" for="zhijie-inline-radio2">Twice</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" name="inlineRadioOptions" id="zhijie-inline-radio3" data-id="<?php echo $event_id;?>" value="3" class="zhijie-going-button-3">
                                        <label class="form-check-label ml-1" for="zhijie-inline-radio3">Gone</label>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>
                    <div class="zj-confirm-group text-center">
                        <button id="zhijie-confirm-bid" data-id="<?php echo $event_id;?>" class="zhijie-register-button mt-5">
                            Confirm
                        </button>
                    </div>
                    <div class="zj-end-event-group">
                        <button id="zhijie-end-event-while-bidding" data-id="<?php echo $event_id;?>" class="zhijie-register-button mt-5">End the event</button>
                    </div>
                    <div class="zj-confirm-success fas fa-check"></div>
                <?php else: ?>
                    <div class="zhijie-page-alert">The event has not started yet.</div>
                <?php endif; ?>
            <?php else: ?>
                <div class="zhijie-page-alert">You cannot view other registrars' events.</div>
            <?php endif; ?>
        <?php else: ?>
            <div class="zhijie-page-alert">Unable to find the event.</div>
        <?php endif; ?>
    <?php else: ?>
        <div class="zhijie-page-alert">Unable to find an event.</div>
    <?php endif; ?>
    </div>
<?php else :
    // enter from qr code
    if(isset($_GET["operation"]) && $_GET["operation"]) {
        $operation = $_GET["operation"]; 
    }
    echo '<script>window.location = "'.home_url().'/login?redirect=' . $event_id . '&operation='. $operation .'" </script>';
endif ;?>
<?php get_footer();