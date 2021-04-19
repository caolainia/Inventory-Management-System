<?php /*Template Name: Client Registration Result Page */
get_header();
require_once __DIR__ . '/vendor/autoload.php';

if(isset($_GET["event_id"]) && validate_event($_GET["event_id"]) == 1):
    // event_id
    $event_id = $_GET["event_id"];
    if (empty($_POST["clientName"]) || empty($_POST["clientEmail"]) || empty($_POST["clientPhone"])): ?>
        <div class="zhijie-page-alert">Cannot register the client. Form is not completed.</div>
    <?php 
    else:
        // participants information
        $name = $_POST["clientName"];
        $email = $_POST["clientEmail"];
        $address = $_POST["clientAddress"];
        $mobile = $_POST["clientPhone"];
        $contactTime = 'N/A';

        $manager_id = find_event_manager($event_id);
        if(isset($_POST['contactTimeFrom']) && $_POST['contactTimeTo']){

            $contactTimeFrom = $_POST['contactTimeFrom'];
            $contactTimeTo = $_POST['contactTimeTo'];
            $contactTime = 'From '.$contactTimeFrom.' to '.$contactTimeTo;
        }

        $contactDaysStr = '';
        if(isset($_POST['contactDay'])){
            $contactDays = $_POST['contactDay'];
            foreach($contactDays as $value){
                $contactDaysStr = $contactDaysStr.$value.' ';
            }
        }
        else{
            $contactDaysStr = 'N/A';
        }

        $conjecture_bid = 0;
        if(isset($_POST['clientConjecture'])){
            $conjecture_bid = intval($_POST['clientConjecture']);
        }
        $bid_to_show = $conjecture_bid==0?"N/A":$conjecture_bid;
        // participants IP address
        $user_ip = $_SERVER['REMOTE_ADDR'];
        // event details
        global $wpdb;
        $table_name = $wpdb->prefix.'zhijie_events';
        $prepared_statement = $wpdb->prepare( "SELECT * FROM {$table_name} WHERE  ID = %s", $event_id);
        $value = $wpdb->get_row( $prepared_statement );
        $event_address = $value->Address;
        $event_time = $value->Datetime;
        $event_agent = $value->Agent;
        
        date_default_timezone_set('Australia/Adelaide');
        $submitted_date = date('Y-m-d H:i:s');
        
        // store to sheet first
        // $args_sheet = array(
        //     "client_name" => $name,
        //     "client_email" => $email,
        //     "client_address" => $address,
        //     "client_mobile" => $mobile,
        //     "event_address" => $event_address,
        //     "event_time" => $event_time,
        //     "event_agent" => $event_agent,
        //     "user_ip" => $user_ip,
        //     "submitted_date" => $submitted_date, 
        //     "best_contact_days" => $contactDaysStr,
        //     "best_contact_time" => $contactTime
        // );

        // $client = google_client_init();
        // $service = google_service_init($client);
        // $stored_sheet = add_data_to_sheet($args_sheet, $manager_id, $service); 

        // Then store to DB
        $args_db = array($name, $address, $email, $mobile, $event_id, $submitted_date, $user_ip, $contactDaysStr, $contactTime, "", $conjecture_bid);
        $status = register_client_to_info($args_db); ?>

        <div class="zhijie-thanks-container">
            <div class="zhijie-ticket-banner text-center">
                <img class="zhijie-green-check-round mt-5" src="<?php echo get_stylesheet_directory_uri() .'/img/green-check.jpg';?>">
                <div class="zhijie-banner-content">
                    <span><b><i><?php echo $status ? "Thank You!" : "Update Successfully!"; ?></i></b></span>
                        <br>
                    </div>
                </div>
                <div class="zhijie-ticket-detail mt-5 p-3 card ">
                    <div id="zhijie-register-detail-title" class="text-center">
                        <b>Your <?php echo $status ? "Registered" : "Updated"; ?> Details: </b><br>
                    </div>
                    <hr/>
                    <span>Name: <b><?php echo $name; ?> </b><br></span>
                    <span>Email: <b><?php echo $email; ?> </b><br></span>
                    <span>Phone: <b><?php echo $mobile; ?> </b><br></span>
                    <span>Address: <b><?php echo $address; ?> </b><br></span>
                    <hr>
                    <span>Best Contact Days: <br>
                        <b><?php echo $contactDaysStr; ?> </b><br>
                    </span>
                    <span>Best Contact Time: <br>
                        <b><?php echo $contactTime; ?> </b>
                    </span>
                    <span>Guessed Sold Price: <b><?php echo $bid_to_show; ?> </b><br></span>
                </div>
            </div>
        </div>

        
    <?php endif; ?>
<?php else: ?>
    <div class="zhijie-page-alert">Unable to find an event.</div>
<?php endif; ?>

<!-- <script async defer src="https://apis.google.com/js/api.js"
    onload="this.onload=function(){};handleClientLoad()"
    onreadystatechange="if (this.readyState === 'complete') this.onload()">
</script> -->
<?php get_footer();?>

<?php 
    // Register a client to the current event for inspection.
    // Store the values to wp_zhijie_client_info
    function register_client_to_info($array){
        global $wpdb;
        $table_name = $wpdb->prefix.'zhijie_client_info';
        $data = array(
            "name" => $array[0],
            "address" => $array[1],
            "email" => $array[2],
            "mobile" => $array[3],
            "event_id" => $array[4],
            "date" => $array[5],
            "ip" => $array[6],
            "contact_day" => $array[7],
            "contact_time" => $array[8],
            "stored_sheet_id" => $array[9],
            "conjecture_bid" => $array[10]
        );
        // If it is a new user for event
        if (!check_client_exist_in_event($array[6], $array[4])) {
            $format = array('%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%d');
            $wpdb->insert($table_name, $data, $format);
            return True;
        } else {
            // Get the existed row's id and update the value of the row
            $field_name = "id";
            $prepared_statement = $wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE ip = %s", $array[6], "AND event_id = %d", $array[4]);
            $values = $wpdb->get_col( $prepared_statement );
            $rowid = array_pop(array_reverse($values));
            $wpdb->update($table_name, $data, array('id'=>$rowid));

            return False;
        }
    }

    function validate_event($event_id){
        global $wpdb;
        $table_name = $wpdb->prefix.'zhijie_events';
        $field_name = 'Datetime';
        $prepared_statement = $wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE  ID = %d", $event_id);
        $values = $wpdb->get_col( $prepared_statement );
        // event does not exist
        if(count($values) == 0){
            return 0;
        }

        $current_time = date("Y-m-d h:i:s");
        $event_time = array_pop(array_reverse($values));
        // echo time()."\n";
        // echo strtotime($current_time)."\n";
        // echo strtotime(($event_time));
        if($current_time >= $event_time){
            return 1;
        }
        else{
            return 1;
        }
    }

    //Check if a user has already registered his info
    function check_client_exist_in_event($client_ip, $event_id){
        global $wpdb;
        $table_name = $wpdb->prefix.'zhijie_client_info';
        $field_name = 'ip';

        $prepared_statement = $wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE EventID = %s", $event_id);
        $IPs = $wpdb->get_col( $prepared_statement );
        
        return in_array($client_ip, $IPs) ? true : false;
    }
