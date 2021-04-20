
<?php

    //arr to store address and number
    global $address_number_pairs;
    $address_number_pairs = array();

    //client number for the auction
    global $client_number;
    $client_number = 1;

    require_once __DIR__ . '/vendor/autoload.php';
  add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
  function my_theme_enqueue_styles() {
    $the_theme = wp_get_theme();
    //wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style', get_stylesheet_uri());
     
     wp_register_script( "ajax_script", get_stylesheet_directory_uri() .'/js/ajax.js', array('jquery') );
     wp_localize_script( 'ajax_script', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));

     wp_enqueue_script( 'ajax_script' );   
     wp_enqueue_script( 'google-login-api', "https://apis.google.com/js/platform.js");
     wp_enqueue_script('google-login', get_stylesheet_directory_uri() . '/js/google-login.js');
     wp_enqueue_script('google-forms', get_stylesheet_directory_uri() . '/js/forms.js');

     wp_enqueue_script('jquery', "https://code.jquery.com/jquery-3.5.1.slim.min.js");
     wp_enqueue_script( 'bootstrap-scripts', get_stylesheet_directory_uri() . '/js/bootstrap.bundle.js', array("jquery"));
     wp_enqueue_style('bootstrap', get_stylesheet_directory_uri().'/css/bootstrap.css');
  }
  // add font awesome. It has myriad of icons
  function enqueue_load_fa() {
    wp_enqueue_style( 'load-fa', 'https://use.fontawesome.com/releases/v5.5.0/css/all.css' );
  }
  add_action( 'wp_enqueue_scripts', 'enqueue_load_fa');
    
  //set menu by user login status
  function register_my_menus() {

    $term = get_term_by('name', 'main', 'nav_menu');
    $menu_id = $term->term_id;
    $menu_name = $term->name;

    $menu_exists = wp_get_nav_menu_object($menu_id);
    $run_once = get_option($menu_name);
  
    //$event_registration_btn = 0;
    if($menu_exists && !$run_once){
       $array = wp_get_nav_menu_items($menu_id);
      // foreach($array as $key){
      //   if($key['title'] === 'Event Registration'){
      //     $event_registration_btn = $key;
      //   }
      // }
      
      foreach($array as $key){
        if($key->title == "Event Registration"){
            $event_registration_btn = $key;
        }
      }

      //echo $event_registration_btn;
      $last_item = end($array);
      if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        wp_update_nav_menu_item($menu_id, $last_item -> ID, array(
          'menu-item-title' => __('Sign Out'),
          'menu-item-classes' => 'zj-signout-btn',
          'menu-item-status' => 'publish',
          'menu-item-url' => wp_logout_url(home_url() . "/login") 
        ));
      } else {
        wp_update_nav_menu_item($menu_id, $last_item -> ID, array(
          'menu-item-title' =>  __('Sign In'),
          'menu-item-classes' => 'zj-signout-btn',
          'menu-item-url' => home_url()."/login", 
          'menu-item-status' => 'publish'
        ));
      }
    }
  }
  add_action( 'init', 'register_my_menus' );

    //ajax post id to page
    add_action('wp_ajax_post_id', 'post_id');
    function post_id(){

      $url = __DIR__."/auction-details.php";
      $result['event_id'] = $_POST['event_id'];
      // build the urlencoded data
      $postvars = http_build_query($result);
      // open connection
      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
      // execute post
      $post = curl_exec($ch); 

      // close connection
       curl_close($ch);

      if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $result = json_encode($result);
        echo $result;
      }else {
         header("Location: ".$_SERVER["HTTP_REFERER"]);
      }
      die();
    }
    //ajax send sheet url to db
    add_action( 'wp_ajax_send_sheet_url', 'send_sheet_url' );
    function send_sheet_url(){
      $url = $_POST['url'];
      $delimiter = '/d/';
      if(strstr($url, $delimiter)){
        ///d/1vV_uDcvmIG2VSs8MSICubnVczda8bqfIta7qTNEi-_U/edit#gid=0
        $url = strstr($url, $delimiter);

        ///d/1vV_uDcvmIG2VSs8MSICubnVczda8bqfIta7qTNEi-_U
        $url = strstr($url, '/edit', true);
        
        //1vV_uDcvmIG2VSs8MSICubnVczda8bqfIta7qTNEi-_U
        $url = str_replace($delimiter, '', $url);
      }
   
      $user_id = get_current_user_id();
      //validate the url
      $client = google_client_init();
      $service = google_service_init($client);

      $sheet_data = validate_sheet_url($url, $service,false);
      $id = add_user_meta($user_id, 'spreadsheetId', $url, true);

      $current_url = get_user_meta($user_id, 'spreadsheetId');
      if($current_url && $current_url != $url){
        $id = update_user_meta($user_id, 'spreadsheetId', $url);
      }
      elseif($current_url != $url){
        $id = add_user_meta($user_id, 'spreadsheetId', $url, true);
      }
      
      //Determine if the data has been added to the sheet yet. If it`s not, add the header
      //Default false
      // $url_status = 0;
      // $meta_array = get_user_meta($user_id, 'url_status');
      // if($meta_array){
      //   $status = update_user_meta($user_id, 'url_status', $url_status);
      // }
      // else{
      //   $status = add_user_meta($user_id, 'url_status', $url_status, true);
      // }
      //Debug
      // $result['url'] = $url;
      // $result['id'] = $id;
      // $result['after'] = $sheet_url;
      // $result['user_id'] = $user_id;
      // $result['table name'] =$table_name;
      //$result['sheet_data'] = $sheet_data;
      //$result['status'] = $status;
      
      if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $result = json_encode($result);
        echo $result;
      }else {
         header("Location: ".$_SERVER["HTTP_REFERER"]);
      }
      die();
    }
    //Add event to the interested event list
    add_action( 'wp_ajax_add_event_to_list', 'add_event_to_list' );
    add_action( 'wp_ajax_nopriv_add_event_to_list', 'add_event_to_list' );
    function add_event_to_list(){
      $event_id = $_POST['id'];
      $user_id = get_current_user_id();
      $is_logged_in = is_user_logged_in();
      if($is_logged_in){
        $result['meta_id'] = add_auction_to_usermeta($user_id, $event_id);
        $result['is_logged_in'] = true;
      }
      else{
        $result['is_logged_in'] = false;
      }
      global $wpdb;
      //find user in zhijie_users
      $table_name  = $wpdb->prefix."zhijie_events";
      //$address = $wpdb->esc_like($address);
      $field_name = '*';
      //$prepared_statement = $wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE Address LIKE %s",  '11\%');
      
      // $values = $wpdb-> get_results($wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE Address LIKE %s", $address));
      // $result['table name'] =$table_name;
      // $result['values'] = $values;
      if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $result = json_encode($result);
        echo $result;
      }else {
         header("Location: ".$_SERVER["HTTP_REFERER"]);
      }
      die();
    }

    //Search events in db by the text that entered by client
    add_action( 'wp_ajax_send_search_text', 'send_search_text' );
    add_action( 'wp_ajax_nopriv_send_search_text', 'send_search_text' );
    function send_search_text(){
      $address = $_POST['text'];
      global $wpdb;
      //find user in zhijie_users
      $table_name  = $wpdb->prefix."zhijie_events";
      $address = "%".$wpdb->esc_like($address)."%";
      $result['address'] = $address;
      //$address = $wpdb->esc_like($address);
      $field_name = '*';
      //$prepared_statement = $wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE Address LIKE %s",  '11\%');
      
      $values = $wpdb-> get_results($wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE Address LIKE %s", $address));
      $result['table name'] =$table_name;
      $result['values'] = $values;
      $result['address_esc'] = $address;
      if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $result = json_encode($result);
        echo $result;
      }else {
         header("Location: ".$_SERVER["HTTP_REFERER"]);
      }
      die();
    }

    //Sychronize data with spreadsheet
    add_action( 'wp_ajax_send_data_to_sheet', 'send_data_to_sheet' );
    function send_data_to_sheet(){
      $user_id = get_current_user_id();

      $sheet_url = get_user_meta($user_id, 'spreadsheetId', true);
      $event_id = $_POST['event_id'];
      $previous_info = find_event_old_info_by_id($event_id, 'fined');
      $new_info = find_event_new_info_by_id($event_id);
      $basic_info = find_event_info_in_zhijie_events($event_id);
      $new_info['address'] = $basic_info['address'];

      array_push($previous_info, $new_info);
      $all_info = $previous_info;

      $count = 0;
      while( $count < count($all_info)){
        $all_info[$count]['address'] = $basic_info['address'];
        $count++;
      }
      //Debug
      // $result['all_info'] = $all_info;
      // $result['add'] = $basic_info['address'];
      // $result['url'] = $sheet_url;
      $insert_result = add_data_to_sheet($all_info, $sheet_url);
      $result['insert_result'] = $insert_result;
      if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $result = json_encode($result);
        echo $result;
      }else {
         header("Location: ".$_SERVER["HTTP_REFERER"]);
      }
      die();
    }

    //Delete an event from client`s list
    add_action( 'wp_ajax_remove_event_from_list', 'remove_event_from_list');
    add_action( 'wp_ajax_nopriv_remove_event_from_list', 'remove_event_from_list' );
    function remove_event_from_list(){
      $id = $_POST['id'];
      $result['id'] = $id;
      if(is_user_logged_in()){

        $result['result'] = delete_event_from_client_list($id);
      }
      else{
        $result['is_logged_in'] = false;
      }

      if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
         $result = json_encode($result);
         echo $result;
      }else {
          header("Location: ".$_SERVER["HTTP_REFERER"]);
      }
       
      die();
    }

    add_action( 'wp_ajax_start_event_show_prices', 'start_event_show_prices');
    function start_event_show_prices() {
      $event_id = $_POST['id'];
      global $wpdb;
      $table_name = $wpdb->prefix.'zhijie_events';
      $update_status = $wpdb->update( $table_name, array( 'status' => "Active"),array('ID'=>$event_id));
      $result['update_status'] = $update_status;
      if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
         $result = json_encode($result);
         echo $result;
      }else {
          header("Location: ".$_SERVER["HTTP_REFERER"]);
      }
      die();
    }

    // Set the event to inactive and push the price from auction_new to auction_old
    add_action( 'wp_ajax_store_new_bid', 'store_new_bid');
    function store_new_bid() {
      $event_id = $_POST['id'];
      // pop and push to old table
      $result['new_to_old_status'] = push_price_new_to_old($event_id);
      $price = $_POST['price'];
      $auctioneer_call = $_POST['going'];
      date_default_timezone_set('Australia/Adelaide');
      $date_time = date('Y-m-d H:i:s');
      // insert into new table
      global $wpdb;
      $table_name = $wpdb->prefix.'zhijie_auction_new';
      $data = array("event_id" => $event_id, "price" => $price, "auctioneer_call" => $auctioneer_call, "client_num" => 0, "date_time" => $date_time);
      $format = array('%d', '%d', '%d', '%d', '%s');
      $insert_status = $wpdb->insert($table_name, $data, $format);
      $result['insert_status'] = $insert_status;
      if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $result = json_encode($result);
        echo $result;
      }else {
        header("Location: ".$_SERVER["HTTP_REFERER"]);
      }
      die();
    }

    // Set the event to inactive and push the price from auction_new to auction_old
    add_action( 'wp_ajax_end_event_unshow_prices', 'end_event_unshow_prices');
    function end_event_unshow_prices() {
      $event_id = $_POST['id'];
      global $wpdb;
      $table_name = $wpdb->prefix.'zhijie_events';
      $update_status = $wpdb->update( $table_name, array( 'status' => "Inactive"),array('ID'=>$event_id));
      $result['update_status'] = $update_status;
      $result['new_to_old_status'] = push_price_new_to_old($event_id);
      if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $result = json_encode($result);
        echo $result;
      }else {
        header("Location: ".$_SERVER["HTTP_REFERER"]);
      }
      die();
    }

    // push the price from auction_new to auction_old
    function push_price_new_to_old($event_id) {
      global $wpdb;
      $table_name = $wpdb->prefix.'zhijie_auction_new';
      $prepared_statement = $wpdb->prepare( "SELECT * FROM {$table_name} WHERE  event_id = %d", $event_id);
      $values = $wpdb->get_results( $prepared_statement );
      if (sizeof($values) > 0) {
        // get values from new table
        $row = array_pop(array_reverse($values));
        $price = $row->price;
        $auctioneer_call = $row->auctioneer_call;
        $client_num = $row->client_num;
        $date_time = $row->date_time;
        $id = $row->id;
        // remove the row
        $wpdb->delete( $table_name, array( 'id' => $id ) );
        // insert to old table
        $to_insert = $wpdb->prefix.'zhijie_auction_old';
        $data = array("event_id" => $event_id, "price" => $price, "auctioneer_call" => $auctioneer_call, "client_num" => $client_num, "date_time" => $date_time);
        $format = array('%d', '%d', '%d', '%d', '%s');
        $wpdb->insert($to_insert, $data, $format);
        return $data;
      }
      return -1;
    }

    //ajax show auctions on event-list
    add_action( 'wp_ajax_show_events_on_list', 'show_events_on_list' );
    function show_events_on_list(){

      $user_id = get_current_user_id();
      $type = $_POST['type'];
      $result = find_events_by_type($user_id, $type);
      foreach($result as $event){
        
        $post_id = $event->post_id;
        if($post_id != 0){
          $thumbnail = get_the_post_thumbnail_url($post_id);
        }
        else{
          $thumbnail = get_stylesheet_directory_uri()."/img/house.jpg";
        }

        $event->thumbnail = $thumbnail;
      }
      if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
          $result = json_encode($result);
          echo $result;
      } else {
        header("Location: ".$_SERVER["HTTP_REFERER"]);
      }
    
      die();

    }



    function cmp($a, $b) {
      preg_match("/[0-9]{2,4}/", $a, $ma);
      preg_match("/[0-9]{2,4}/", $b, $mb);
      $ia = intval($ma[0]);
      $ib = intval($mb[0]);
      if ($ia == $ib) {
          return 0;
      }
      return ($ia < $ib) ? -1 : 1;
    }

    // Read fdf
    // modify fdf
    // write to a new fdf
    // shell_exec "pdftk form1b.pdf fill_form 2.fdf output new.pdf"
    function ready_pdf_filling($pir, $cot) {
      $fdf_path = __DIR__.'/resources/b.fdf';
      $filesize = filesize($fdf_path);
      $fdf = fopen($fdf_path, "r");
      $content = fread($fdf, $filesize);
      fclose($fdf);

      $lined_content1 = preg_replace("/\)\n/", ") ", $content);
      $lined_content2 = preg_replace("/\<\<\n/", "<< ", $lined_content1);
      $lined_content3 = preg_replace("/Off\n/", "Off  ", $lined_content2);
      $lined_content4 = preg_replace("/Yes\n/", "Yes  ", $lined_content3);
      $lined_content5 = preg_replace("/\/\n/", "/ ", $lined_content4);
      $lines = preg_split("/((\r?\n)|(\r\n?))/", $lined_content5);

      // all body list
      $textareaList = array();
      $checkboxList = array();
      $selectList = array();

      // fdf header lines
      $fdf_header_list = array();
      // fdf footer lines
      $fdf_footer_list = array();

      $strip_fdf_head = true;
      foreach($lines as $line){
          if (preg_match("/<<.*?textareaname.*?>>/", $line)) {
            $textareaList[] = $line;
            $strip_fdf_head = false;
          } else if (preg_match("/<<.*?checkboxname.*?>>/", $line)) {
            $checkboxList[] = $line;
            $strip_fdf_head = false;
          } else if (preg_match("/<<.*?select.*?>>/", $line)) {
            $selectList[] = $line;
            $strip_fdf_head = false;
          } else if ($strip_fdf_head){
            $fdf_header_list[] = $line;
          } else {
            $fdf_footer_list[] = $line;
          }
      }

      usort($textareaList, 'cmp');
      usort($checkboxList, 'cmp');
      usort($selectList, 'cmp');

      // For division 1 only
      $textareaHeader = array();
      $textareaList33 = array();
      $textareaFooter = array();
      $textareaCount33 = 0;
      // textarea 471 - 5498
      foreach($textareaList as $k => $line) {

        preg_match("/[0-9]{2,4}/", $line, $ma);
        $ia = intval($ma[0]);
        if ($ia < 471) {
          $textareaHeader[] = $line;
        } else if ($ia >= 471 && $ia <= 5498) {
          $textareaList33[$textareaCount33] = $line;
          $textareaCount33 += 1;
        } else {
          $textareaFooter[] = $line;
        }
      }


      $checkboxHeader = array();
      $checkboxList33 = array();
      $checkboxFooter = array();
      $checkboxCount33 = 0;
      // checkbox 405 - 5467
      foreach($checkboxList as $k => $line) {
        preg_match("/[0-9]{2,4}/", $line, $ma);
        $ia = intval($ma[0]);
        if ($ia < 405) {
          $checkboxHeader[] = $line;
        } else if ($ia >= 405 && $ia <= 5467) {
          $checkboxList33[$checkboxCount33] = $line;
          $checkboxCount33 += 1;
        } else {
          $checkboxFooter[] = $line;
        }
      }
      

      $selectHeader = array();
      $selectList33 = array();
      $selectFooter = array();
      $selectCount33 = 0;
      // select 409 - 5473
      foreach($selectList as $k => $line) {
        preg_match("/[0-9]{2,4}/", $line, $ma);
        $ia = intval($ma[0]);
        if ($ia < 409) {
          $selectHeader[] = $line;
        } else if ($ia >= 409 && $ia <= 5473) {
          $selectList33[$selectCount33] = $line;
          $selectCount33 += 1;
        } else {
          $selectFooter[] = $line;
        }
      }


      // Edit 33 questions body list

      // Question 1 Test: always tick checkbox
      // TO BE EDITED
      $checkboxList33[0] = tick_fdf_checkbox($checkboxList33[0]);


      // Concat fdf header footer and body above
      $final_no_footer = array_merge($fdf_header_list, $checkboxList33, $checkboxHeader, $checkboxFooter, $selectFooter, $selectHeader, $selectList33, $textareaList33, $textareaHeader, $textareaFooter);
      $result = "";
      foreach($final_no_footer as $line) {
        $line = preg_replace("/>>\]/", ">>",  $line);
        $result = $result . $line . "\n";
      }
      $result = $result . "]\n";
      // Concat fdf footer
      foreach($fdf_footer_list as $line) {
        $result = $result. $line . "\n";
      }
      // Write to file
      $new_fdf_path = addslashes(__DIR__.'/resources/w.fdf');
      file_put_contents($new_fdf_path, $result);

      $blank_pdf_path = addslashes(__DIR__ . "/resources/form1b.pdf");
      $new_pdf_path = addslashes(__DIR__ . "/resources/new.pdf");
      echo addslashes($new_pdf_path);
      echo "<br>";
      // Exec shell to write back to pdf
      $cmd = "pdftk ".$blank_pdf_path." fill_form ".$new_fdf_path." output ".$new_pdf_path.' 2>&1';
      echo $cmd;
      shell_exec($cmd);
    }





    function tick_fdf_checkbox($line) {
      $line = preg_replace("/\/V \/(Off)? \/T/", "/V /Yes /T", $line);
      return $line;
    }








    // Extract all zip file
    function extract_all_files($pir, $cot, $e, $tax, $water, $search) {
        $result["pir"] = $pir;
        $result["cot"] = $cot;
        $result["e"] = $e;
        $result["tax"] = $tax;
        $result["water"] = $water;
        $result["search"] = $search;

        $dir = dirname($pir);

        // Unzip all files
        $zip = new ZipArchive;
        if ($zip->open($pir)) {
            $zip->extractTo($dir);
            $filename = $zip->getNameIndex(0);
            $result["pirdir"] = dirname($filename);
            $zip->close();
            $result["pir_extract_status"] = True;
        } else {
            $result["pir_extract_status"] = False;
        }
        if ($zip->open($cot)) {
            $zip->extractTo($dir);
            $filename = $zip->getNameIndex(0);
            $result["cotdir"] = dirname($filename);
            $zip->close();
            $result["cot_extract_status"] = True;
        } else {
            $result["cot_extract_status"] = False;
        }
        if ($zip->open($e)) {
            $zip->extractTo($dir);
            $filename = $zip->getNameIndex(0);
            $result["edir"] = dirname($filename);
            $zip->close();
            $result["e_extract_status"] = True;
        } else {
            $result["e_extract_status"] = False;
        }
        if ($zip->open($tax)) {
            $zip->extractTo($dir);
            $filename = $zip->getNameIndex(0);
            $result["taxdir"] = dirname($filename);
            $zip->close();
            $result["tax_extract_status"] = True;
        } else {
            $result["tax_extract_status"] = False;
        }
        if ($zip->open($water)) {
            $zip->extractTo($dir);
            $filename = $zip->getNameIndex(0);
            $result["waterdir"] = dirname($filename);
            $zip->close();
            $result["water_extract_status"] = True;
        } else {
            $result["water_extract_status"] = False;
        }
        if ($zip->open($search)) {
            $zip->extractTo($dir);
            $filename = $zip->getNameIndex(0);
            $result["searchdir"] = dirname($filename);
            $zip->close();
            $result["search_extract_status"] = True;
        } else {
            $result["search_extract_status"] = False;
        }
        return $result;
        
    }











    //ajax google login
    add_action( 'wp_ajax_login_ajax_request', 'login_ajax_request' );
    add_action( 'wp_ajax_nopriv_login_ajax_request', 'login_ajax_request' );
    function login_ajax_request() {
        $user_name = $_POST['user_name'];
        $google_user_id = intval($_POST['user_id']);
        $img_url = $_POST['img_url'];
        $user_email = $_POST['user_email'];

        if(!$user_name) {
            $result['type'] = "error".$user_name;
            
        } else {

          //Debug
          $result['user_name'] = $user_name;
          $result['google_user_id'] = $google_user_id;
          $result['img_url'] = $img_url;
          $result['user_email'] = $user_email;

          // register user to wp_users table, and get the user_id
          $user_id = create_new_user_in_wp_users('cqmygysdss1994', $user_name, $user_email);
          // if user exists in wp_users
          if ($user_id == 0 || !is_numeric($user_id)) {
             $temp_user = get_user_by('email', $user_email);
             $user_id = $temp_user->ID;
          }
          $result['user_id'] = $user_id;

          // register user to wp_zhijie_users table
          $result['db_status'] = store_google_user_in_db($result);   
          $result['type'] = "success";
           

          //sign in user by info
          // $user = get_user_by('email', $user_email);

          // $user_info['user_login'] = $user->user_login;
          // $user_info['user_password'] = 'cqmygysdss1994';
          // $user_info['remember'] = "true";

          $user = get_user_by( 'id', $user_id ); 
          if( $user ) {
              wp_set_current_user( $user_id, $user->user_login );
              wp_set_auth_cookie( $user_id );
              do_action( 'wp_login', $user->user_login, $user );
              
              //$result['user'] = $user;
          }
          // $result['login status'] = sign_in_user($user_info, $user->ID);
      
        }

        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        } else {
           header("Location: ".$_SERVER["HTTP_REFERER"]);
        }
      
        die();
    }
 

    //ajax set event
    add_action( 'wp_ajax_set_event', 'set_event' );
    add_action( 'wp_ajax_nopriv_set_event', 'set_event' );

    function set_event() {
      //Debug
      $result['Address'] = $_POST['address'];
      
      $event_time = $_POST['date']." ".$_POST['time'];
      $result['Datetime'] = $event_time;

      $result['Agent'] = $_POST['company'];
      
      $user_id = get_current_user_id();
      $result['User_id'] = $user_id;

      //$set_result = add_event_to_db($result);

      //Send img url to js
      $result['Form_url'] = $_POST['url'];
      //$result["Insert row to db"] = $set_result;

      //Post form_url back to the page
      $result['QR_code'] = do_shortcode( '[kaya_qrcode title_align="alignnone" content="'.$url.'" ecclevel="L" align="alignnone"]' );

      if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $result = json_encode($result);
        echo $result;
      }else {
         header("Location: ".$_SERVER["HTTP_REFERER"]);
      }
    
      die();
    }

    function register_my_menu() {
      register_nav_menus(
        array(
          'header-menu' => __( 'Main Menu' )
        )
      );
    }
    add_action( 'init', 'register_my_menu' );
    
    //Store user data in zhijie_user table
    //Para: an array that contains user info including name, id, mail etc.
    //Return: array for debuging purpose
    function store_google_user_in_db($user_info){
      global $wpdb;
      $table_name  = $wpdb->prefix."zhijie_users";
      // construct a clean array to store in DB
      $data['user_name'] = $user_info['user_name'];
      $data['user_email'] = $user_info['user_email'];
      $data['user_imgurl'] = $user_info['img_url'];
      $data['user_id'] = $user_info['user_id'];
      $data['google_user_id'] = $user_info['google_user_id'];

      //verify if user alredy exists in db
      $field_name = 'user_email';

      $prepared_statement = $wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE user_email = %s", $data['user_email']);
      $values = $wpdb->get_col( $prepared_statement );
      if(count($values) > 0){

        $arr['error'] = "user already existed!";
      }

      else{
        // insert to wp_zhijie_users
        $format = array('%s','%s', '%s', '%d', '%d');
        $num = $wpdb->insert($table_name, $data, $format);
  
        $my_id = $wpdb->insert_id;
        $if_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
        $arr = array('data' => $data, 'format' => $format, 'table_name' => $table_name, 'my_id' => $my_id, 'num' => $num, 'if_exists' => $if_exists);
        
      }

      return $arr;
    }

    //Create a new user in wp_users table
    //Para: password, user name and email
    //return: id of new user if user hasn`t registered before else return error message
    function create_new_user_in_wp_users($password, $user_name, $email){

      $new_user_id = wp_create_user( $user_name, $password, $email); 

      if(is_wp_error($new_user_id)){
        $error = $new_user_id->get_error_message();

        return 'fail: '. $error;
      }else{
        return $new_user_id;
      }

    }

    //Sign in new user to wordpress core
    //Para: array that contains name, mail and int for user id
    //Return: Error message if unsuccessful
    function sign_in_user($user_info, $id){

        $user_verify = wp_signon( $user_info, false );   
   		  wp_set_current_user($id);
        if ( is_wp_error($user_verify) ) {  
          return 'Invalid login details'; 
        } else {  
         	return $user_verify;
        }  
    }

    /**
    * Add open event (inspection event) to zhijie_open_events table in db
    * @param array of address, agent name, user id
    * @return inserted result / false
    */
    function add_inspection_to_db($array){
      global $wpdb;
      $table_name = $wpdb->prefix. 'zhijie_events';
      //verify if recird alredy exists in db
      $field_name = 'ID';
      $validate_statement = $wpdb->prepare( 
        "SELECT {$field_name} FROM {$table_name} WHERE 
        Address = %s
        AND event_type = %s",
        $array['Address'],
        "Inspection"
      );
      $values = $wpdb->get_col( $validate_statement );
      if(count($values) == 0){
        $format = array('%s', '%s', '%s', '%s', '%d', '%s');
        $wpdb->insert($table_name, $array, $format);
        $validate_statement = $wpdb->prepare( 
          "SELECT {$field_name} FROM {$table_name} WHERE 
          Address = %s
          AND event_type = %s",
          $array['Address'],
          "Inspection"
        );
        $values = $wpdb->get_col( $validate_statement );
        $value = array_pop($values);
        return $value;
      }
      else{
        // already recorded
        return false;
      }
    }

    /**
    * Add event to zhijie_events table in db
    * @param array of address, company name, datetime, user id, form url
    * @return Insert result
    */
    function add_event_to_db($array){
      global $wpdb;
      $table_name = $wpdb->prefix. 'zhijie_events';
      //verify if recird alredy exists in db
      $field_name = 'ID';

      $validate_statement = $wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE 
        Address = %s 
        AND Datetime = %s",
        $array['Address'], 
        $array['Datetime']
      );

      $values = $wpdb->get_col( $validate_statement );
      if(count($values) == 0){

        $format = array('%s', '%s', '%s', '%s', '%d', '%s', '%s');
        //$array = array("Address" => "111", "Agent" => "2222", "datetime" => "2022-02-02 07:30:25");
        $wpdb->insert($table_name, $array, $format);

        $validate_statement = $wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE 
          Address = %s 
          AND Datetime = %s",
          $array['Address'], 
          $array['Datetime']
        );
        $values = $wpdb->get_col( $validate_statement );
        $value = array_pop($values);


        return $value;
      }

      else{

        return false;
      }
    }

    //Post form_url back to the page for generating the QR code

    function post_url_back_to_page($url, $page_url){
        // where are we posting to?
        

        // what post fields?
        $fields = array(
          'url' => $url
        );

        // build the urlencoded data
        $postvars = http_build_query($fields);

        // open connection
        $ch = curl_init();

        // set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $page_url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);

        // execute post
        $result = curl_exec($ch);

        // close connection
        curl_close($ch);
    }

    // Disable wp-admin bar for non-admin users
    add_filter('show_admin_bar', 'is_blog_admin');
//Submit form actions for future use
// add_action('admin_post_submit-form', '_handle_form_action'); // If the user is logged in
// add_action('admin_post_nopriv_submit-form', '_handle_form_action'); // If the user in not logged in
// function _handle_form_action(){

//     if (isset($_POST['submit']))
//     {   
//         echo '<script>window.location = "'.home_url().'/qr-code'.'" </script>';
//         $url = $_POST['inputUrl'];
        
//     }


// }

//Inspect an array
function display_array($your_array)
{
    foreach ($your_array as $key => $value)
    {
        if (is_array($value))
        {
            display_array($value);
        }
        else
        {
             echo "Key: $key; Value: $value<br />\n";
        }
        
    }
    
    echo "--------------<br />\n";
}


     //Initialize Google Spreadsheet Service
     function google_service_init($client){

      $client->setApplicationName("Goolge Sheets");
      $client->setDeveloperKey("AIzaSyCQNIOLvUAuGfjQ5z_FpHXLBD1AH64sa2M");
      $service_credentials_url = __DIR__."/service_account_credentials.json";
      putenv('GOOGLE_APPLICATION_CREDENTIALS='.$service_credentials_url);
      $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
      $client->setAccessType('offline');
      $client->useApplicationDefaultCredentials();
      $service = new Google_Service_Sheets($client);

      return $service;
     
    }
    
    //initialize Google Client
    function google_client_init(){

      $client = new Google_Client();

      return $client;
    }
    
/**
   * Find events by type on event list page
   * @param int $user_id
   * @param string $type
   * @return array $result on success
   */
  function find_events_by_type($user_id, $type){
    global $wpdb;
    $table_name  = $wpdb->prefix."zhijie_events";

    $field_name = '*';
    $result = array();
    if($type == "auctions"){
      $prepared_statement = $wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE  user_id = %d AND event_type = %s", $user_id, "Auction" );
      $result = $wpdb->get_results( $prepared_statement );

      return $result;
    }
    elseif($type == "inspections"){
      $prepared_statement = $wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE  user_id = %d AND event_type = %s", $user_id, "Inspection" );
      $result = $wpdb->get_results( $prepared_statement );

      return $result;
    }
    else{
      $prepared_statement = $wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE  user_id = %d", $user_id);
      $result = $wpdb->get_results( $prepared_statement );

      return $result;
    }
  }
    
/**
   * Validate the sheet url by try to read and add some data to it
   * @param int $spreadsheetId
   * @param int $service google service
   * @return array $result on success
   * @return exception $e on failure
   */
    function read_data_from_sheet($range, $spreadsheetId, $service, $bool){
      try{
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();
  
        if(empty($values)){
  
            return false;
  
        }  else{

            if($bool){

              $first_row = array_pop(array_reverse($values));
              return $first_row;
            }
            else{
              $result = array();
              array_push($result, $values);
              return $result;
            }
        }
      } catch(Exception $e){
        echo $e;
      }


  }

  /**
   * Validate the sheet url by try to read and add some data to it
   * @param int $spreadsheetId
   * @param int $service google service
   * @return array $result on success
   * @return exception $e on failure
   */
  function validate_sheet_url($spreadsheetId, $service){

    if(read_data_from_sheet('!A1:K', $spreadsheetId, $service, true)){
      $first_row = read_data_from_sheet('!A1:K', $spreadsheetId, $service, true);
      $insert_values = [$first_row];
      $body = new Google_Service_Sheets_ValueRange([
        'values' => $insert_values
        ]);

        $params = [
            'valueInputOption' => 'RAW'
        ];

        $insert = [
            "insertDataOption" => "INSERT_ROWS"
        ];

        $result = $service->spreadsheets_values->update(
            $spreadsheetId,
            '!A1:K1',
            $body,
            $params
        );
      
        return $result;
    }

    else{
      try{
        //
        // $insert_values = [
        //   ["client_name","client_email","client_address",
        //   "client_mobile","event_address","event_time",
        //   "event_agent", "user_ip", "submitted_date", 
        //   "best_contact_days", "best_contact_time"]
        // ];
    
        $insert_values = [
          ["Price", "Calls", "Called by Client", "Call at", "Address"]
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
    
        $result = $service->spreadsheets_values->update(
            $spreadsheetId,
            '!A1:K1',
            $body,
            $params
        );
      
        return $result;
        
      } catch(Exception $e){
        echo $e;
  
        return $e;
      }
    }
   
  }

  /**
   * Get sheet url by agent`s id
   * Deprecated
   * @param int $manager_id.
   * @return int $url 
   */
  function get_sheet_url($manager_id){
    global $wpdb;
    $table_name  = $wpdb->prefix."zhijie_users";

    $field_name = 'sheet_url';
    $prepared_statement = $wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE  user_id = %d", $manager_id );
    $url = array_pop($wpdb->get_col( $prepared_statement ));

    return $url;
  }

  /**
   * Find the agent that created the event
   *
   * @param int $event_id.
   * @return int $id 
   */
  function find_event_manager($event_id){
    global $wpdb;
    $table_name  = $wpdb->prefix."zhijie_events";

    $field_name = 'User_id';
    $prepared_statement = $wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE  ID = %d", $event_id );
    $id = array_pop($wpdb->get_col( $prepared_statement ));
    
    return $id;
  }

  /**
   * Add event id to usermeta table of an interested event
   *
   * @param int $event_id.
   * @param int current $user_id.
   * @return int $meta_id that indicates if the insertion was successful.
   */
  function add_auction_to_usermeta($user_id, $event_id){
    $existing_auctions = get_user_meta($user_id, 'interested_auction');
    if(in_array($event_id, $existing_auctions)){
      return 0;
    }
    else{
      $meta_id = add_user_meta($user_id, 'interested_auction', $event_id);
      return $meta_id;
    }
  }

  function find_event_info_in_zhijie_events($event_id){
    global $wpdb;
    $table_name  = $wpdb->prefix."zhijie_events";
    $field_name = '*';

    $prepared_statement = $wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE  ID = %d", $event_id );
    $info_from_zhijie_events = $wpdb->get_row( $prepared_statement, 'ARRAY_A');
    if($info_from_zhijie_events){
      $result['address'] = $info_from_zhijie_events['Address'];
      $result['sponsor'] = $info_from_zhijie_events['Agent'];
      $result['status'] = $info_from_zhijie_events['status'];
      $result['id'] = $event_id;
      $result['date_time'] = $info_from_zhijie_events['Datetime'];
      $result['post_id'] = $info_from_zhijie_events['post_id'];
      if($info_from_zhijie_events['contact_numbers']){
  
        $result['contactNumbers'] = $info_from_zhijie_events['contact_numbers'];
      }
      else{
        $result['contactNumbers'] = 'None';
      }
      
      if($info_from_zhijie_events['additional_links']){
  
        $result['additional_links'] = $info_from_zhijie_events['additional_links'];
      }
      else{
        $result['additional_links'] = 'None';
      }
      

      return $result;
    }
    else{
      return false;
    }
  }

  /**
   * Find the newest calls and price of an event
   *
   * @param int event_id.
   * @return array $result that contains price, calls, call by which client, and call at what time.
   */
  function find_event_new_info_by_id($event_id){
    global $wpdb;
    
    $table_name = $wpdb->prefix."zhijie_auction_new";
    $field_name = '*';
    $prepared_statement = $wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE  event_id = %d", $event_id );
    $info_from_zhijie_auction = $wpdb->get_row( $prepared_statement, 'ARRAY_A');

      if(isset($info_from_zhijie_auction['price']) && isset($info_from_zhijie_auction['auctioneer_call'])){

        $result['price'] = number_format($info_from_zhijie_auction['price']);
        $result['calls'] = $info_from_zhijie_auction['auctioneer_call'];
        //$result['client_num'] = $info_from_zhijie_auction['client_num'];
        $result['date_time'] = $info_from_zhijie_auction['date_time'];
      }
      else{
          
        $result['price'] = "N/A";
        $result['calls'] = "N/A";
        //$result['client_num'] = "N/A";
        $result['date_time'] = "N/A";
  
      }
      return $result;
  }

    /**
   * Delete an event from client`s auction list on Auctions page if user is logged in
   *
   * @param int event_id.
   * @return bool true on success false on failure.
   */
  function delete_event_from_client_list($event_id){
    $user_id = get_current_user_id();

    return delete_user_meta($user_id, 'interested_auction', $event_id);
  }

    /**
   * Delete an event from client`s auction list on Auctions page if user is logged in
   *
   * @param int event_id.spreadsheetId
   * @return bool true on success false on failure.
   */
  function find_event_old_info_by_id($event_id, $type){
    global $wpdb;
    $table_name = $wpdb->prefix."zhijie_auction_old";
    $field_name = "*";
    $prepared_statement = $wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE  event_id = %d", $event_id );
    $info_from_zhijie_auction_old = $wpdb->get_results($prepared_statement, 'ARRAY_A');

    if($type == "fined"){

      $fined_info = array();
      foreach($info_from_zhijie_auction_old as $info){
        $temp['price'] = number_format($info['price']);
        $temp['calls'] = $info['auctioneer_call'];
        $temp['client_num'] = $info['client_num'];
        $temp['date_time'] = $info['date_time'];

        array_push($fined_info, $temp);
      }

      return $fined_info;
    }

    else{

      return $info_from_zhijie_auction_old;
    }
  }
    /**
   * Varify if user is logged in or has the privileges to view a page
   * Redirect user to login page or show messages depends on bool $redirect
   * @param bool $redirect
   * @return void.
   */
  function varify_user_loggin_status($redirect){

    if(is_user_logged_in()){

      $user_id = get_current_user_id();
      $user_meta = get_userdata($user_id);
      $role = array_pop($user_meta->roles);
      if($role == 'subscriber'){
        if($redirect){
          echo "<h3>Your user group has no privileges to view this page. Redirecting...";

          header('Refresh: 3; URL='.home_url());
          exit();
        }
      }
      
    }
    else{
          echo "<h3>You have not logged in yet. Redirecting...";
          header('Refresh: 3; URL='.home_url().'/login');
          exit();
    }

  }

  /**
   * Add data to google spreadsheet. Validate the $spreadsheetId first, if is not valid return false.
   * If the sheet is currently empty, insert a header to it and call recursively
   * Find the differences of current data in the sheet and $array. Append differences
   * @param $array contains the data that needs to be appended
   * @param $spreadsheetId indicates which spreadsheet to add to
   * @return bool true or false to indicate if the insertion was successful.
   * @return array $result to indicate the result of appending data
   */
  function add_data_to_sheet($array, $spreadsheetId) {

    $client = google_client_init();
    $service = google_service_init($client);
    if ($spreadsheetId && $spreadsheetId != "") {
        $range = "A1";
        $current_data = read_data_from_sheet('A1:K', $spreadsheetId, $service, false);
        $header = array("Price", "Calls", "Called at", "Address");

        if($current_data){
          //  $current_data = check_diff_multi($current_data, $header);
          //  $differences = check_diff_multi($array, $current_data);
          //  $opt['current'] = $current_data;
          //  $opt['diff'] = $differences;
          // if(count($differences) == 0){
          //    $opt['current'] = $current_data;
          //    $opt['diff'] = $differences;
          //  return $opt;
          // }
          $result = array();
          foreach($array as $row){
            
            // Insert the data to the Client Info sheet in google
            $insert_values = [
              [$row["price"],$row["calls"],
              $row["date_time"], $row["address"]]
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

            $temp = $service->spreadsheets_values->append(
              $spreadsheetId,
              $range,
              $body,
              $params
            );

            array_push($result, $temp);
          }
          //  $opt['current'] = $current_data;
          //  $opt['diff'] = $differences;
          return $result;
        }
        else{
            insert_header_to_sheet($spreadsheetId, $service, $range);
            $insert_value = add_data_to_sheet($array, $spreadsheetId);

            return $insert_value;
        }

    } else {
        return false;
    }
    
}


  /**
   * Insert header to a sheet
   * @param $array contains the data that needs to be appended
   * @param $spreadsheetId indicates which spreadsheet to add to
   * @return bool true or false to indicate if the insertion was successful.
   */
function insert_header_to_sheet($spreadsheetId, $service, $range){
    $insert_values = [
      ["Price", "Calls", "Called at", "Address"]
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

function check_diff_multi($array1, $array2){
  $result = array();
  foreach($array1 as $key => $val) {
       if(isset($array2[$key])){
         if(is_array($val) && $array2[$key]){
             $result[$key] = check_diff_multi($val, $array2[$key]);
         }
     } else {
         $result[$key] = $val;
     }
  }

  return $result;
}

function endsWith( $haystack, $needle ) {
  $length = strlen( $needle );
  if( !$length ) {
      return true;
  }
  return substr( $haystack, -$length ) === $needle;
}

function add_product_to_db($a) {
  global $wpdb;
  $table_name = 'wp_jose_products';
  //verify if recird alredy exists in db
  $field_name = 'ID';

  $validate_statement = $wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE 
    product_name = %s 
    AND valid_duration = %s",
    $a['product_name'], 
    $a['valid_duration']
  );

  $values = $wpdb->get_col( $validate_statement );
  if(count($values) == 0){
    $format = array('%s', '%s', '%s');
    $wpdb->insert($table_name, $array, $format);

    $validate_statement = $wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE 
      Address = %s 
      AND Datetime = %s",
      $array['Address'], 
      $array['Datetime']
    );
    $values = $wpdb->get_col( $validate_statement );
    $value = array_pop($values);
    return $value;
  }
  else{
    return false;
  }
}














