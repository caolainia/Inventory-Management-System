<?php /*Template Name: Client Registration Page */
get_header(); 

if(isset($_GET["event_id"])):
	$event_id = $_GET["event_id"];
    // get event detail
    global $wpdb;
    $table_name = $wpdb->prefix.'zhijie_events';
    $user_id = get_current_user_id();
    $prepared_statement = $wpdb->prepare("SELECT * FROM {$table_name} WHERE ID = %s;", $event_id);
    $values = $wpdb->get_results( $prepared_statement );
    if (sizeof($values) != 0):
        $row = array_pop(array_reverse($values));
        $address = $row->Address;
        $datetime = $row->Datetime;
        $agent = $row->Agent; 
        $event_type = $row->event_type; 
        if ($event_type === "Inspection"): ?>

        	<div id="generator-container" class="container">
                <div class="row">

                    <div class="col-12 mt-4">
                        <h3>Welcome! You are registering for the House Inspection at <?php echo $address;?></h3>
                    </div>
                    <div class="col-12 container">
                        <form id="registerClient" name="form" action="<?php echo home_url() . "/client-registration-result?event_id=" . $event_id;?>" method="post" class='bg-light mt-2 p-3'>
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Full Name<span style="color:red">*</span>: </label>
                                <div class="col-sm-9">
                                    <input name="clientName" type="text" class="form-control" id="client-name" placeholder='Firstname Surname ' required>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <label class="col-sm-3 col-form-label">Telephone<span style="color:red">*</span>: </label>
                                <div class="col-sm-9">
                                    <input name="clientPhone" placeholder='0424123456' type="text" class="form-control" id="client-mobile" required>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <label class="col-sm-3 col-form-label">Email<span style="color:red">*</span>: </label>
                                <div class="col-sm-9">
                                    <input name="clientEmail"  placeholder='example.name@example.com' type="text" class="form-control" id="client-email" required>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <label class="col-sm-3 col-form-label">Address: </label>
                                <div class="col-sm-9">
                                    <input name="clientAddress" placeholder='96 Barnett Street, Mawson Lakes, SA 5095' type="text" class="form-control" id="client-address">
                                </div>
                            </div>

                            <div class="row mt-3">
                                <label class="col-sm-3 col-form-label">Best Contact Day: </label>
                                <div class="form-check ml-3">
                                    <input name="contactDay[]"  type="checkbox" class="form-check-input" id="monday" value="Monday">
                                    <label class="form-check-label" for="monday">Monday</label>
                                </div>
                                
                                <div class="form-check ml-3">
                                    <input name="contactDay[]"  type="checkbox" class="form-check-input" id="tuesday" value="Tuesday">
                                    <label class="form-check-label" for="tuesday">Tuesday</label>
                                </div>

                                <div class="form-check ml-3">
                                    <input name="contactDay[]"  type="checkbox" class="form-check-input" id="wednesday" value="Wednesday">
                                    <label class="form-check-label" for="wednesday">Wednesday</label>
                                </div>

                                <div class="form-check ml-3">
                                    <input name="contactDay[]"  type="checkbox" class="form-check-input" id="thursday" value="Thursday">
                                    <label class="form-check-label" for="thursday">Thursday</label>
                                </div>

                                <div class="form-check ml-3">
                                    <input name="contactDay[]"  type="checkbox" class="form-check-input" id="friday" value="Friday">
                                    <label class="form-check-label" for="friday">Friday</label>
                                </div>

                                <div class="form-check ml-3">
                                    <input name="contactDay[]"  type="checkbox" class="form-check-input" id="saturday" value="Saturday">
                                    <label class="form-check-label" for="saturday">Saturday</label>
                                </div>

                                <div class="form-check ml-3">
                                    <input name="contactDay[]"  type="checkbox" class="form-check-input" id="sunday" value="Sunday">
                                    <label class="form-check-label" for="sunday">Sunday</label>
                                </div>
                                
                            </div>  
                            <div class="row mt-3">
                                <label class="col-sm-3 col-form-label">Best Contact Time:</label>
                                <div class="col-sm-8">
                                    <label class="col-form-label" for="contactTimeFrom">From: </label>
                                    <input type="time" id="contactTimeFrom" name="contactTimeFrom">

                                    <label class="col-form-label ml-3" for="contactTimeTo">To: </label>
                                    <input type="time" id="contactTimeTo" name="contactTimeTo">
                                </div>
                            </div>

                            <div class="row mt-3">
                                <label class="col-sm-3 col-form-label">Guess Sold Price (AUD$): </label>
                                <div class="col-sm-9">
                                    <input name="clientConjecture"  placeholder="Take a guess on the final price and win a prize after the auction" type="text" class="form-control" id="client-conjecture">
                                </div>
                            </div>

                            <div id="generate-btn-container" style="text-align: center;" class="mt-5">
                                <input form="registerClient" value="Submit" id="register-btn" name="submit" type="submit" class="btn btn-primary">
                            </div>    
                            
                        </form>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="zhijie-page-alert mt-5">Invalid event type. This page is for House inspection registration</div>
        <?php endif; ?>
    <?php else: ?>
        <div class="zhijie-page-alert mt-5">Unable to find an event.</div>
    <?php endif; ?>
<?php else: ?>
    <div class="zhijie-page-alert mt-5">Unable to find an event.</div>
<?php endif; ?>
<?php get_footer();?>