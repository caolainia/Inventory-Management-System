<?php /*Template Name: Event Registration Result Page */


get_header(); ?>

<div class="zj-result-container">
    <?php
    if (isset($_POST["eventAddress"]) && isset($_POST["companyName"]) && isset($_POST["dateInput"]) && isset($_POST["timeInput"])):
        $address = $_POST["eventAddress"];
        $agent = $_POST["companyName"];
        $date = $_POST["dateInput"];
        $time = $_POST["timeInput"];
        $note = $_POST["eventNote"];
        $contactNumbersArray = array();
        $addtional_links_array = array();

        //get contact numbers
        if(isset($_POST['contactNumber1']) && $_POST['contactNumber1'] != ""){
            $contactNumbersArray['#1'] = $_POST['contactNumber1'];
        }

        if(isset($_POST['contactNumber2']) && $_POST['contactNumber2'] != ""){
            $contactNumbersArray['#2'] = $_POST['contactNumber2'];
        }

        if(isset($_POST['contactNumber3']) && $_POST['contactNumber3'] != ""){
            $contactNumbersArray['#3'] = $_POST['contactNumber3'];
        }

        if(isset($_POST['contactNumber4']) && $_POST['contactNumber4'] != ""){
            $contactNumbersArray['#4'] = $_POST['contactNumber4'];
        }

        //get additional links
        if(isset($_POST['info-link1']) && $_POST['info-link1'] != ""){
            $addtional_links_array['#1'] = $_POST['info-link1'];
        }

        if(isset($_POST['info-link2']) && $_POST['info-link2'] != ""){
            $addtional_links_array['#2'] = $_POST['info-link2'];
        }

        if(isset($_POST['info-link3']) && $_POST['info-link3'] != ""){
            $addtional_links_array['#3'] = $_POST['info-link3'];
        }

        $contactNumberStr = encode_array_to_string($contactNumbersArray);
        $addtional_links_str = encode_array_to_string($addtional_links_array);
        $datetime = $date. " " . $time;
        $user_id = get_current_user_id();

        date_default_timezone_set('Australia/Adelaide');
        $current_date = date("Y-m-d");
        $current_time = date("h:i");

        if ($current_time > $time && $current_date == $date):
            // invalid time, ahead of current time 
        ?>
            <h3 class="mt-5">Invalid Time! Please rewrite the form.</h3>

        <?php else:
            $array = array('Address' => $address, 'Agent' => $agent, "Datetime" => $datetime, "comment" => $note, "User_id" => $user_id, "contact_numbers" => $contactNumberStr, "additional_links" => $addtional_links_str);

            //add data to db
            $status = add_event_to_db($array);
        ?>

            <?php if(is_user_logged_in()) :?>

                <?php if($status == false) : ?>
                    <h3 class="mt-5 text-center">Unable to register the event because the event has been registered already!</h3>
                <?php else : ?>
                    <?php 
                        //generate url like: www.myrefii.com/client-registration?event_id=1
                        $url = home_url().'/auction-operation?event_id='.$status ?>
                    <div class="zhijie-detail-card">
                        <div class="card bg-light mt-5 shadow-sm text-center">
                            <h5 class="mt-3">Successfully registered!</h5>
                            <h6>
                                You can view all your events from the Event List page<br>
                            </h6>
                            <hr>
                            <div class="mb-2 px-4 text-left">
                                <h6>
                                    <span class="zj-einfo-title">Venue:</span>
                                    <span class="zj-einfo-value"><?php echo $address;?></span>
                                </h6>
                                
                                <h6>
                                    <span class="zj-einfo-title">Time:</span>
                                    <span class="zj-einfo-value"><?php echo $datetime;?></span>
                                </h6>
                                <h6>
                                    <span class="zj-einfo-title">Host Agent:</span>
                                    <span class="zj-einfo-value"><?php echo $agent;?></span>
                                </h6>
                                <?php if ($note): ?>
                                    <h6>
                                        <span class="zj-einfo-title">Note:</span>
                                        <span class="zj-einfo-value"><?php echo $note; ?></span>
                                    </h6>
                                <?php endif; ?>
                                <?php 
                                    $count = 1;
                                    foreach($contactNumbersArray as $number) : 
                                    if ($number && is_numeric($number)): 
                                    ?>
                                        <h6>
                                            <span class="zj-einfo-title">Contact Number <?php echo $count;?>:</span>
                                            <span class="zj-einfo-value"><?php echo $number; ?></span>
                                        </h6>
                                        <?php $count += 1; ?>
                                    <?php endif; ?>
                                <?php endforeach ; ?>

                                <?php 
                                    $count = 1;
                                    foreach($addtional_links_array as $link) : 
                                    ?>
                                        <h6>
                                            <span class="zj-einfo-title">Link <?php echo $count;?>:</span>
                                            <span class="zj-einfo-value"><a href="<?php echo $link; ?>"><?php echo $link; ?></a></span>
                                        </h6>
                                        <?php $count += 1; ?>
                                <?php endforeach ; ?>
                            </div>
                        </div>
                    </div>

                    <div class="zj-post-section mt-4">
                        <h6>You can write a post to describe the event.</h6><hr>
                        <?php echo do_shortcode( '[fep_submission_form event_id=' . $status . ']' ); ?>
                    </div>
                <?php endif ; ?>

            <?php else : ?>
                <?php echo '<script>window.location = "'.home_url().'/login'.'" </script>'; ?>
            <?php endif ; ?>
        <?php endif ; ?>
    <?php else : ?>
        <div class="zhijie-page-alert">Cannot register the event, as there is no info provided.</div>
    <?php endif ; ?>

</div>

<?php get_footer();

function encode_array_to_string($array){
    $count = 1;
    //concat numbers
    $opt = '';
    $c = 0;
    foreach($array as $element){
        // if ($c = 0) {
        //     $contactNumberStr = $contactNumberStr . $number;
        // } else {
        //     $contactNumberStr = $contactNumberStr. ', ' . $number;
        // }
        $opt = $opt. ', ' . $element;
        //$c += 1;
    }

    return $opt;
}