<?php /*Template Name: Auction Form Page */

session_start();
get_header();

$current_date = date("Y-m-d");
date_default_timezone_set('Australia/Adelaide');
$current_time = date("H:i");

if(isset($_GET["event_id"])):
    $event_id = $_GET["event_id"];
    // get the event detail
    global $wpdb;
    $table_name = $wpdb->prefix.'zhijie_events';
    $user_id = get_current_user_id();
    $prepared_statement = $wpdb->prepare("SELECT * FROM {$table_name} WHERE ID = %s;", $event_id);
    $values = $wpdb->get_results( $prepared_statement );
    if (sizeof($values) != 0):
        $row = array_pop(array_reverse($values));
        $address = $row->Address;
        $datetime = $row->Datetime;
        $agent = $row->Agent; ?>

        <div id="generator-container" class="container">
            <div class="row">
                <div class="col-12 mt-4">
                    <h3>Registering for the Event at <?php echo $address;?></h3>
                    <p>You must be registered to bid at a public auction of residential property in South Australia.</p>
                </div>
                <div class="col-12 container">
                    <form id="registerClient" name="form" action="<?php echo home_url() . "/auction-client?event_id=" . $event_id;?>" method="post" class='bg-light mt-2 p-3'>
                        <div class="row mb-3">
                            <label class="col-sm-12 col-form-label">Full Name (of person who is actually wishing to buy the property):</label>
                            <div class="col-sm-12">
                                <input name="clientName" type="text" class="form-control" id="client-name" placeholder='(the "Prospective Purchaser")' required>
                            </div>
                        </div>

                        <div class="zhijie-checkbox-oneline zhijie-information-card zhijie-grey px-1">
                            <label class="col-form-label">Proxy bidder being used?</label>
                            <div class="form-check ml-3">
                              <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
                              <label class="form-check-label" for="flexRadioDefault1">
                                Yes
                              </label>
                            </div>
                            <div class="form-check ml-3">
                              <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2" checked>
                              <label class="form-check-label" for="flexRadioDefault2">
                                No
                              </label>
                            </div>
                        </div>
                        <div class="row mb-3 px-1">
                            <label class="col-sm-12 col-form-label">If Yes, the Full Name of the person who is bidding on behalf of the prospective purchaser wanting to buy the property?</label>
                            <div class="col-sm-12">
                                <input name="clientName" type="text" class="form-control" id="client-name" placeholder='Full Name' required>
                            </div>
                        </div>
                        <div class="mt-3 zhijie-grey zhijie-information-card zhijie-small-font px-1 py-2">
                            <span>The Proxy Bidder agrees, if successful at auction, to sign / execute the contract of sale IN THEIR OWN NAME, unless:</span>
                            <br>
                            <span>1. The named person being the Prospective Purchaser is physically present at the auction and willing and able to sign/execute the contract of sale (and notified the Agent of same during the Bidder Registration process), OR</span>
                            <br>
                            <span>2. The Prospective Purchaser is NOT physically present and, agrees and executes the contract of sale within 5 minutes of the residential contract of sale being sent to him/her electronically and signed electronically, at the email address notified to the Agent during the Bidder Registration process,</span>
                            <br>
                            <span>OR</span>
                            <br>
                            <span>3. An original or certified copy of the duly executed Power of Attorney was sighted and a copy provided to the Agent, or the Agents representative, upon Bidder Registration.</span>
                            <br>
                            <span><b>WARNING NOTICE: </b> <i>If you intend to bid on behalf of another person or entity as the Prospective Purchaser's Proxy Bidder, then you must also complete a Proof of Prospective Purchaser's Identity AND Proxy Bidders Written Authority Form. If the person you are bidding for is not at the auction, then you as the bidder will be responsive for signing the contract and paying the agreed deposit amount.</i></span>
                        </div>

                        <div class="row mt-3">
                            <label class="col-sm-3 col-form-label">Address of prospective purchaser: </label>
                            <div class="col-sm-8">
                                <input name="clientAddress"  type="text" class="form-control" id="client-address" required>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <label class="col-sm-3 col-form-label">Telephone: (W)</label>
                            <div class="col-sm-8">
                                <input name="clientPhoneWork" type="text" class="form-control" id="client-mobile" required>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <label class="col-sm-3 col-form-label">Email</label>
                            <div class="col-sm-8">
                                <input name="clientEmail"  type="text" class="form-control" id="client-email" required>
                            </div>
                        </div>      

                        <div class="mt-3 zhijie-information-card zhijie-small-font py-3 px-1">
                            <div class="">
                                <b>INTENDING BIDDERS ACKNOWLEDGEMENT OF RECEIPT OF FORM R3, FORM R4, FORM R5, FORM R6 AND FORM R7 --</b>
                                <br>
                                <span>I, the abovenamed Prospective Purchaser of Proxy Bidder expressly acknowledge and agree that I have received a copy Form R3, Form R4, Form R5, Form R6 AND Form R7 (only applicable for residential land).</span>
                                <br>
                                <span>I seek (if applicable) the Vendor(s) consent to the above variations to the auction terms and/or conditions of sale and I will be advised of acceptance, or otherwise, prior to commencement of bidding.</span>
                            </div>
                            <label class="col-form-label">Signature of Intending Bidder (being either the Prospective Purchaser or nominated Proxy): </label>
                            <canvas id="signature" class="mx-3" height="60"></canvas>
                        </div>

                        <div>
                            <input type="hidden" name="signature" />
                        </div>

                        <div id="generate-btn-container" style="text-align: center;" class="mt-5">
                            <input form="registerClient" value="Submit" id="register-btn" name="submit" type="submit" class="btn btn-primary">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="zhijie-page-alert">Unable to find an event.</div>
    <?php endif; ?>
<?php else: ?>
    <div class="zhijie-page-alert">Unable to find an event.</div>
<?php endif; ?>
<?php get_footer();?>