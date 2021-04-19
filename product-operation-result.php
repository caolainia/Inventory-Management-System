<?php /*Template Name: Product Operation Result Page */

get_header(); ?>

<div class="zj-result-container">
    <?php
    if (isset($_POST["productName"]) 
     && isset($_POST["productDuration"]) 
     && isset($_POST["productSupplierSelect"]) 
     && isset($_POST["productCategorySelect"])):
        $name = $_POST["productName"];
        $duration = $_POST["productDuration"];
        $supplier = $_POST["productSupplierSelect"];
        $category = $_POST["productCategorySelect"];

        date_default_timezone_set('Australia/Adelaide');
        $current_date = date("Y-m-d");
        $current_time = date("h:i");

        $array = array('Address' => $address, 'Agent' => $agent, "Datetime" => $datetime, "comment" => $note, "User_id" => $user_id, "contact_numbers" => $contactNumberStr, "additional_links" => $addtional_links_str);

        //add data to db
        $status = add_event_to_db($array);
        ?>

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
            <?php endif ; ?>
        <?php endif ; ?>
    <?php else : ?>
        <div class="zhijie-page-alert">Cannot add product, as there is no info provided.</div>
    <?php endif ; ?>

</div>

<?php get_footer();