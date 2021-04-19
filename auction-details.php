<?php /**
 *    Template Name: Auction Details
 */

 get_header();

 if(isset($_GET['event_id'])):
    $event_id = $_GET['event_id']; ?>

        <div class="container bg-white zhijie-event-inspector my-5" id="<?php echo "inspector-". $event_id?>" style="min-height:15rem">
            <?php 
                            
            $previous_info = find_event_old_info_by_id($event_id, 'fined');

            $new_info = find_event_new_info_by_id($event_id);

            $basic_info = find_event_info_in_zhijie_events($event_id);
            
            if($basic_info == false):?>
                <div class="zhijie-page-alert">Unable to find an event.</div>
            <?php elseif ($basic_info['status'] === 'Inactive'): ?>
                <div class="text-center pt-5">
                    <h4>This event has not started yet</h4>
                    <p class="font-italic">Please contact <b><?php echo $basic_info['sponsor']?></b> for starting time</p>
                </div>
            <?php else :
                $contactNumberStr = $basic_info['contactNumbers'];
                $contactNumbersArray = explode(",", $contactNumberStr);

                $links_str = $basic_info['additional_links'];
                $links_array = explode(",", $links_str);
                ?>

                <div class="inspector-wrapper">
                    <div class="inspector-inner">
                        <div class="text-center">
                            <h3>Welcome to the event</h3>
                        </div>
                        <div class="current-info-wrapper">
                            <div class="my-3 p-3 bg-body rounded current-info-box shadow-sm">
                                <h6 class="border-bottom pg-2 mb-0">Current Updates</h6>
                                <div class="d-flex text-muted pt-3">
                                    <p class='pb-3 mb-0 small lh-sm border-bottom'>
                                        <strong class="d-block text-gray-dark">Price</strong>
                                        <?php echo '$'.$new_info['price'].' (AUD)'?>
                                    </p>
                                </div>
                                <div class="d-flex text-muted pt-3">
                                    <p class='pb-3 mb-0 small lh-sm border-bottom called-time'>
                                        <strong class="d-block text-gray-dark">Last Called at</strong>
                                        <?php
                                            date_default_timezone_set('Australia/Adelaide');
                                            $current_time = new DateTime(date('Y-m-d H:i:s'));
                                            echo $new_info['date_time'];
                                            if($new_info['date_time'] != "N/A"){
                                                $called_time = new DateTime($new_info['date_time']);
                                                $time_diff = date_diff($current_time, $called_time);
                                                echo ' | '. $time_diff->format("%D Days %H:%I:%S"). " ago (Hours, Minutes, Seconds)";
                                            }
                                        ?>
                                    </p>
                                </div>
                                <div class="d-flex text-muted pt-3">
                                    <p class='pb-3 mb-0 small lh-sm border-bottom'>
                                        <strong class="d-block text-gray-dark">Called</strong>
                                        <?php echo $new_info['calls'].' times'?>
                                    </p>
                                </div>
                            </div>
                            

                            <hr>

                            <div class="basic-info-wrapper">
                                <div class="my-3 p-3 bg-grey basic-info-box rounded shadow-sm">
                                    <h6 class="border-bottom pg-2 mb-0">Event Details</h6>
                                    <div class="d-flex text-muted pt-3">
                                        <p class='pb-3 mb-0 small lh-sm border-bottom'>
                                            <strong class="d-block text-gray-dark">Address</strong>
                                            <?php echo $basic_info['address']?>
                                        </p>
                                    </div>

                                    <div class="d-flex text-muted pt-3">
                                        <p class='pb-3 mb-0 small lh-sm border-bottom'>
                                            <strong class="d-block text-gray-dark">Sponsor</strong>
                                            <?php echo $basic_info['sponsor']?>
                                        </p>
                                    </div>

                                    <div class="d-flex text-muted pt-3">
                                        <p class='pb-3 mb-0 small lh-sm border-bottom'>
                                            <strong class="d-block text-gray-dark">Started at</strong>
                                            <?php echo 'Event started at '.$basic_info['date_time']?>
                                        </p>
                                    </div>

                                    <div class="d-flex text-muted pt-3">
                                        <p class='pb-3 mb-0 small lh-sm border-bottom'>
                                            <strong class="d-block text-gray-dark">Contact Numbers</strong>
                                            <?php 
                                                foreach($contactNumbersArray as $number){
                                                    echo $number . ' ';
                                                }
                                            ?>
                                        </p>
                                    </div>

                                    <div class="d-flex text-muted pt-3">
                                        <p class='pb-3 mb-0 small lh-sm border-bottom'>
                                            <strong class="d-block text-gray-dark">About This Property</strong>
                                            <a href="<?php echo get_permalink($basic_info['post_id']); ?>">
                                                <?php echo get_permalink($basic_info['post_id']); ?>
                                            </a>
                                        </p>
                                    </div>
                                    <div class="d-flex text-muted pt-3">
                                        <p class='pb-3 mb-0 small lh-sm border-bottom'>
                                            <strong class="d-block text-gray-dark">Additional Information</strong>
                                            <?php 
                                                foreach($links_array as $link){
                                                    if($link != "None"){
                                                        echo '<a class="zhijie-link" href=' . $link .'>'.$link . '</a> <br>';
                                                    }
                                                }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                    <div class="row">
                        <div class="col">
                            <h3>Event Log</h3>
                        </div>

                        <div class="col offset-md-4">
                            <div class="btn-group dropdown">
                                <?php if(is_user_logged_in()) : ?>
                                    <button type="button" class="zhijie-link-sheet-button btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        Link to Google Sheets
                                    </button>
                                <?php else : ?>
                                    <button type="button" class="zhijie-link-sheet-button btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                                        Sign in to enable sheets
                                    </button>
                                <?php endif ;?>
                                <ul class="dropdown-menu" id="ul-link-sheet">
                                    <li><a class="dropdown-item" onclick="openPopupWindow()">Link to a new sheet</a></li>
                                    <li><a class="dropdown-item" id="li-current-sheet">Use Current</a></li>
                                </ul>
                            </div>
                        </div>
                        
                    </div>
                    <?php if($previous_info) : 
                          $temp = $previous_info;
                          $all_info = array_push($previous_info, $new_info);
                        ?>

                        <div class="div-scroll">
                            <table class="table mt-3 table-hover event-details-table">
                                <thead>
                                    <tr>
                                        <th scope="col">Price</th>
                                        <th scope="col">Called</th>
                                        <th scope="col">Called at</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach($previous_info as $info) : ?>
                                        
                                    <tr class="zhijie-tr">
                                        <td><?php echo "$". $info['price'] . "(AUD)"?></td>
                                        <td><?php echo $info['calls']?></td>
                                        <td><?php echo $info['date_time']?></td>
                                    </tr>
                                <?php endforeach ; ?>
                            </tbody>
                            </table>

                        </div>

                    <?php else : ?>
                        <p class="fw-light fst-italic">Not yet Recorded</p>
                    <?php endif ;?>
            <?php endif ; ?>

    </div>

<?php else: ?>
    <div class="inspector-wrapper">
        <div class="zhijie-page-alert">Unable to find the event.</div>
    </div>
<?php endif; ?>

<!--
    <section class="jumbotron text-center bg-white">
        <div class="text-center">
            <h3>You must log in first. Redirecting...</h3>
        </div>
    </section>
            -->
    <?php 
        // sleep(3);
        // echo '<script>window.location = "'.home_url().'/login'.'" </script>';
        ?>

<?php get_footer();?>