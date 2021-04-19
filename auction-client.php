<?php /*Template Name: Auction Client Page */
get_header();
require_once __DIR__ . '/vendor/autoload.php';

if(isset($_GET["event_id"]) && validate_event($_GET["event_id"]) == 1):
    // event_id
    $event_id = $_GET["event_id"];
    if (empty($_POST["clientName"]) || empty($_POST["clientEmail"]) 
        || empty($_POST["clientAddress"]) || empty($_POST["clientMobile"])): ?>
        <div class="zhijie-page-alert">Cannot register the client. No information is provided.</div>
    <?php 
    else:
        // participants information
        $name = $_POST["clientName"];
        $email = $_POST["clientEmail"];
        $address = $_POST["clientAddress"];
        $mobile = $_POST["clientPhoneWork"];
        // participants signature
        $img = $_POST['signature'];

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

        // Form the img string to JSON
        $img_json = json_encode(array("name"=>"Signature", "value"=>$img, "type"=>"text"));
        date_default_timezone_set('Australia/Adelaide');
        // participant ticket number
        $args_db = array($event_id, $user_ip, $name, $email, $address, $mobile, $img_json, date('Y-m-d H:i:s'));
        $ticket_no =  register_client_to_event($args_db);

        $args_sheet = array(
            "event_id" => $event_id,
            "client_name" => $name,
            "client_email" => $email,
            "client_address" => $address,
            "client_mobile" => $mobile,
            "ticket_no" => $ticket_no,
            "event_address" => $event_address,
            "event_time" => $event_time,
            "event_agent" => $event_agent,
        );
        add_data_to_sheet($args_sheet); ?>

        <div class="zhijie-ticket-container">
            <div class="zhijie-ticket-banner text-center">
                <div class="zhijie-banner-content">
                    <b><i><?php echo $event_agent; ?></i></b>
                </div>
            </div>
            <div class="zhijie-ticket-no"><b><?php echo $ticket_no; ?></b></div>
            <div class="zhijie-ticket-detail text-center">
                <?php echo $name; ?> <br>
                <?php echo $email; ?> <br>
                <?php echo $address; ?> <br>
                <?php echo $mobile; ?> <br>       
            </div>
        </div>
    <?php endif; ?>

<?php else: ?>
    <div class="zhijie-page-alert">Unable to find an event.</div>
<?php endif; ?>

<script async defer src="https://apis.google.com/js/api.js"
    onload="this.onload=function(){};handleClientLoad()"
    onreadystatechange="if (this.readyState === 'complete') this.onload()">
</script>
<?php get_footer();?>

<?php 
    //Register a client to the current event
    //Check if the user exists first, if not add the user to db with new ticketNo
    //Else, find the ticketNo by user ip address
    function register_client_to_event($array){
        global $wpdb;
        $table_name = $wpdb->prefix.'zhijie_tickets';

        //if user is not exist, insert a row and increase count for one
        if(!validate_ip_address($array[1], $array[0])){


            $current_order = find_biggest_ticketNo($array[0]);
            $newest_order = $current_order + 1;
            $data = array("EventID" => $array[0], "TicketNO" => $newest_order, "IP" => $array[1], "Name" => $array[2], "Email" => $array[3], "Address" => $array[4], "Mobile" => $array[5], "fields" => $array[6], "date_submitted" => $array[7]);
            $format = array('%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s');
            $wpdb->insert($table_name, $data, $format);
            

            return $newest_order;
        }

        else{

            $field_name = "TicketNO";
            $prepared_statement = $wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE  IP = %s", $array[1], "AND EventID = %d", $array[0]);
            $values = $wpdb->get_col( $prepared_statement );

            $ticketNo = array_pop(array_reverse($values));

            return $ticketNo;
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

    //Find the biggest ticketNo of the current event
    function find_biggest_ticketNo($event_id){
        global $wpdb;
        $table_name = $wpdb->prefix.'zhijie_tickets';
        $field_name = 'TicketNO';
        $prepared_statement = $wpdb->prepare( "SELECT MAX({$field_name}) FROM {$table_name} WHERE  EventID = %s", $event_id);
        $values = $wpdb->get_col( $prepared_statement );
        $biggest = array_pop(array_reverse($values));

        return $biggest;
    }

    //Check if user ip is already exists in db
    function validate_ip_address($client_ip, $event_id){

        global $wpdb;
        $table_name = $wpdb->prefix.'zhijie_tickets';
        $field_name = 'IP';

        $prepared_statement = $wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE EventID = %s", $event_id);
        $IPs = $wpdb->get_col( $prepared_statement );

        if(in_array($client_ip, $IPs)){

            return true;

        }

        else{

            return false;
        }
    }

    function add_data_to_sheet($array) {
        $client = new Google_Client();
        $client->setApplicationName("Goolge Sheets");
        $client->setDeveloperKey("AIzaSyCQNIOLvUAuGfjQ5z_FpHXLBD1AH64sa2M");
        $service_credentials_url = __DIR__."/service_account_credentials.json";
        putenv('GOOGLE_APPLICATION_CREDENTIALS='.$service_credentials_url);
        $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');
        $client->useApplicationDefaultCredentials();
        $service = new Google_Service_Sheets($client);
        $spreadsheetId = "1EswzNOxPBhKwWKRFIWUVu_Q6WFJpmCfLqEBZIqYOALc";

        $range = "A1";
        //Read
        // $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        // $values = $response->getValues();

        // if(empty($values)){

        //     print "No data found. \n";

        // }  else{
        //     $mask = "%10s %-10s %s\n";
        //     foreach($values as $row){
        //         echo sprintf($mask, $row[2], $row[1], $row[0]);
        //     }
        // }

        //Create
        $insert_values = [
            [$array["event_id"],$array["event_address"],$array["event_time"],$array["event_agent"],
            $array["ticket_no"],$array["client_name"],$array["client_email"],$array["client_address"],
            $array["client_mobile"]]
        ];

        $body = new Google_Service_Sheets_ValueRange([
            'values' => $insert_values
        ]);

        $params = [
            'valueInputOption' => 'RAW'
        ];

        $insert = [
            "insertDataOption" => "INSERT_ROWS"
        ];

        $result = $service->spreadsheets_values->append(
            $spreadsheetId,
            $range,
            $body,
            $params
        );
    }