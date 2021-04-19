<?php /*Template Name: Open Registration Result Page */


get_header(); ?>

<div class="zj-result-container">
    <?php
    if (isset($_POST["openAddress"]) && isset($_POST["agentName"])):
        $address = $_POST["openAddress"];
        $agent = $_POST["agentName"];
        $date = $_POST["openDateInput"];
        $time = $_POST["openTimeInput"];
        $datetime = "N/A";
        if (isset($date) && isset($time) && $date != "" && $time != "") {
            $datetime = $date. " " . $time;
        }
        $note = $_POST["openNote"];

        $user_id = get_current_user_id();

        $array = array(
            'Address'=>$address, 
            'Agent'=>$agent, 
            "Datetime"=>$datetime, 
            "comment"=>$note, 
            "User_id"=>$user_id, 
            "event_type"=>"Inspection"
        );

        //add data to db
        $status = add_inspection_to_db($array);
        ?>

        <?php if(is_user_logged_in()) :?>
            <?php if($status == false) : ?>
                <h3 class="mt-5 text-center">Unable to create the inspection because the address has been recorded already!</h3>
            <?php else : 
                $url = home_url().'/client-registration?event_id='.$status ?>
                <div class="zhijie-detail-card">
                    <div class="card bg-light mt-5 shadow-sm text-center">
                        <h5 class="mt-3">Successfully created!</h5>
                        <h6>
                            View all inspections from the Event List page<br>
                        </h6>
                        <hr>
                        <div class="mb-2 px-4 text-left">
                            <h6>
                                <span class="zj-einfo-title">Address:</span>
                                <span class="zj-einfo-value"><?php echo $address;?></span>
                            </h6>

                            <?php if ($datetime && $datetime != ""): ?>
                                <h6>
                                    <span class="zj-einfo-title">Time:</span>
                                    <span class="zj-einfo-value"><?php echo $datetime;?></span>
                                </h6>
                            <?php endif; ?>

                            <h6>
                                <span class="zj-einfo-title">Agent Name:</span>
                                <span class="zj-einfo-value"><?php echo $agent;?></span>
                            </h6>
                            <?php if ($note): ?>
                                <h6>
                                    <span class="zj-einfo-title">Note:</span>
                                    <span class="zj-einfo-value"><?php echo $note; ?></span>
                                </h6>
                            <?php endif; ?>
                            <hr>
                            <div class="zj-qr-section mt-3">
                                <h6 class="text-center">
                                    Save the QR code <br>
                                    <span style="font-size:1rem; font-weight: normal;">Inspectors can register for inspections by scanning the QR code.</span>
                                </h6>
                                <?php echo do_shortcode( '[kaya_qrcode title_align="alignnone" content="'.$url.'" ecclevel="L" align="alignnone"]' ); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="zj-post-section mt-3">
                    <div class="zhijie-post-wrapper border border-success p-3 my-3">
                        <h5 class="p-2"><i>Write a post for the event here</i></h5>
                        <hr>                          
                        <?php echo do_shortcode( '[fep_submission_form event_id=' . $status . ']' );?>
                    </div>
                </div>
                
            <?php endif ; ?>

        <?php else : ?>
            <?php echo '<script>window.location = "'.home_url().'/login'.'" </script>'; ?>
        <?php endif ; ?>

    <?php else : ?>
        <div class="zhijie-page-alert">Cannot create the Inspection, as there is no info provided.</div>
    <?php endif ; ?>

</div>

<?php get_footer();

function encode_array_to_string($array){
    $count = 1;
    //concat numbers
    $opt = '';
    $c = 0;
    foreach($array as $element){
        $opt = $opt. ', ' . $element;
    }

    return $opt;
}