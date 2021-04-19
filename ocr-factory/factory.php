<?php

use thiagoalessio\TesseractOCR\TesseractOCR;

use function PHPSTORM_META\type;

require_once dirname( dirname(__FILE__) ) . '/ocr-processor/processor.php';
require_once dirname( dirname(__FILE__) ) . '/ocr-processor/PIRProcessor.php';
require_once dirname( dirname(__FILE__) ) . '/ocr-processor/MiscProcessor.php';
require_once dirname( dirname(__FILE__) ) . '/ocr-processor/CTProcessor.php';
require_once dirname(__FILE__).'/factory-tester.php';
class Factory{
    private TesseractOCR $ocr;
    private PIRProcessor $pir;
    private FactoryTester $tester;
    private MiscProcessor $misc;
    private CTProcessor $ct;

    public function __construct()
    {
        $this->ocr = new TesseractOCR();
        $this->pir = new PIRProcessor();
        $this->misc = new MiscProcessor($this->ocr);
        $this->ct = new CTProcessor($this->ocr);
        $this->tester = new FactoryTester();
    }

    public function get_full_text($image_uri){

        $full_text = $this->read_image($image_uri);

        return $full_text;
    }

    public function run_factory($image_uri, $type="pir"){
        // $image_uri = get_stylesheet_directory().'/form-1/U1, 118 Cross Road, Highgate/Form 1 U1, 118 Cross Road, Highgate-042.png';

        $this->set_ocr_image($image_uri);
        $delimiter = '/';
        $pos = strrpos($image_uri, $delimiter);
        $img_name = preg_replace("/(\.png)|(\.jpg)/", "", $image_uri);
        $img_name = substr($img_name, $pos + 1);
        $dir = substr($image_uri, 0, $pos);
        $output_dir = $dir.$delimiter.$type."_output";
        if (!is_dir($output_dir)) {
            mkdir($output_dir, 0755);
        }
        
        $tsv_url = $output_dir.$delimiter.$img_name.'_output.tsv';
        if (file_exists($tsv_url) != 1) {
            echo "working ocr for file:--" . $image_uri;
            $this->ocr->tsv()
            ->setOutputFile($tsv_url)
            ->config('oem', '2')
            ->psm(3)
            ->dpi(300)
            ->run();
        }
        $standard_blocks = $this->standardize_blocks($tsv_url);
        if ($type === "pir") {
            $pair = $this->pir->run($standard_blocks);
            return $pair;
        } else if ($type === "cot") {
            $pair = $this->ct->run($standard_blocks);
            return $pair;
        }
        // Testing
        // $this->test($tsv_url);
    
    }
    
    public function test($tsv_url){

        // echo nl2br("Testing correctness of storing data \n _________________ \n");
        
        // $tsv_url = dirname(dirname(__FILE__)).'\form-1\23 Marlborough Street/output.tsv';
        // echo nl2br("Testing correctness of reading text from db \n _________________ \n");

        $standard_blocks = $this->standardize_blocks($tsv_url);
        $pair = $this->ct->run($standard_blocks);
        // $pair = $this->pir->test($standard_blocks);
        //display_array($pair);
        // print_in_newline("Testing Misc");
        // print_in_newline("__________________________________");
        // $pair = $this->misc->run($standard_blocks);
    }

    private function read_image($image_uri){

        $this->set_ocr_image($image_uri);
        $full_text = $this->ocr->run();
        return $full_text;
    }

    private function set_ocr_image($image_uri){
        $this->ocr->image($image_uri);
    }

    private function store_tsv_to_db($url){

        $user_id = get_current_user_id();
        //$this->delete_previous_data($user_id);
        $delimiter = "\t";
        $fp = fopen($url, 'r');

        $col_names = array();
        $testing_count = 1;
        $loop_count = 1;
        
        while(!feof($fp)){
            $line = fgets($fp);
            $data = str_getcsv($line, $delimiter);

            if(in_array("page_num", $data)){
                $col_names = $data;
            }
            elseif(sizeof($data) == 12){
                $input_array = array();
                $input_array["user_id"] = $user_id;

                $input_array = $this->generate_input_array($data, $col_names); 
                $result = $this->store_array_to_db($input_array);

                $result = 1;
                $opt = $this->tester->testing_store_function($input_array, $data, $testing_count, $testing_count);
                $loop_count += $opt['loop_count'];
                $testing_count += $opt['testing_count'];
            }
        }
        echo nl2br("The loop has been executed for " . $loop_count. " times \n");
        echo nl2br($testing_count. " correct counts \n");
        
        return $result;
    }

    private function standardize_blocks($object, $type="url"){
        $delimiter = "\t";
        if($type == "url"){

            $fp = fopen($object, 'r');
        }
        else{
            $fp = $object;
        }
        $opt = array();
        $text_count = 0;
        $block_num = 0;
        $col_names = array();
        $id = 1;

        while(!feof($fp)){
            $line = fgets($fp);
            $data = str_getcsv($line, $delimiter);

            if(in_array("page_num", $data)){
                $col_names = $data;
            }
            elseif(sizeof($data) == 12){
                
                $pattern = '/[\x00-\x1F\x7F]/';
                if(strstr($pattern, $data[11])){
                    $block_num++;
                }
                if($data[10] != -1){
                    $entity = array();
                    $entity = $this->generate_input_array($data, $col_names, "else");
                    $entity["block_num"] = $block_num;
                    // $input_array = $this->generate_input_array($data, $col_names); 
                    // $result = $this->store_array_to_db($input_array);
                    $entity["id"] = $id;
                    // $result = 1;
                    // $opt = $this->tester->testing_store_function($input_array, $data, $testing_count, $testing_count);
                    // $loop_count += $opt['loop_count'];
                    // $testing_count += $opt['testing_count'];
                    $id++;
                    array_push($opt, $entity);
                }

            }

            $text_count ++;
        } 
        return $opt;
    }

    private function generate_input_array($data, $col_names, $type="db"){
        $id = 0;
        $input_array = array();
        if($type == "db"){
            $user_id = get_current_user_id();
            $input_array["user_id"] = $user_id;
            foreach($data as $key => $value){
                $new_key = $col_names[$key];
                if($new_key !== "text"){
                    if($new_key == "left"){
                        
                        $new_key = "left_offset";
                    }
                    elseif($new_key == "top"){
                        $new_key = "top_offset";
                    }
    
                    $input_array[$new_key] = intval($value);
                }
    
                else{
                    $input_array[$new_key] = $value;
                }
            }
        }
        else{
            foreach($data as $key => $value){

                $new_key = $col_names[$key];
                if($new_key !== "text"){
                    if($new_key == "left"){
                        
                        $new_key = "left_offset";
                    }
                    elseif($new_key == "top"){
                        $new_key = "top_offset";
                    }
    
                    $input_array[$new_key] = intval($value);
                }
    
                else{
                    $input_array[$new_key] = $value;
                }
            }
        }
        return $input_array;
    }

    private function store_array_to_db($array){

        global $wpdb;
        $table_name = $wpdb->prefix.'form1_tsv';
        $format = array('%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%s');
        $result = $wpdb->insert($table_name, $array, $format);

        return $result;
    }
    
    private function delete_previous_data($user_id){
        global $wpdb;
        $table_name = $wpdb->prefix.'form1_tsv';

        $wpdb->delete($table_name, array("user_id" => $user_id), array('%d'));
    }


    private function find_max_offset_of_block($blocks, $type = "db"){
        if($type == "db"){

            global $wpdb;
            $table_name = $wpdb->prefix."form1_tsv";
            $user_id = get_current_user_id();
            $field_name = "MAX(top_offset)";
            $prepared_statement = $wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE  user_id = %d
            AND block_num = %d", $user_id, $blocks );
            $values = $wpdb->get_col( $prepared_statement );
    
            return array_pop($values);
        }
        else{
            $offsets = array();
            foreach($blocks as $block){
                $block_num = strval($block["block_num"]);
                if(!array_key_exists($block_num, $offsets)){
                    $offsets[$block_num] = $block["top_offset"];
                    
                }
                elseif($offsets[$block_num] < $block["top_offset"]){
                    $offsets[$block_num] = $block["top_offset"];
                }
            }
            return $offsets;
        }
    }

    private function find_min_offset_of_block($blocks, $type = "db"){
        if($type == "db"){

            global $wpdb;
            $table_name = $wpdb->prefix."form1_tsv";
            $user_id = get_current_user_id();
            $field_name = "MIN(top_offset)";
            $prepared_statement = $wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE  user_id = %d
            AND block_num = %d", $user_id, $blocks );
            $values = $wpdb->get_col( $prepared_statement );
    
            return array_pop($values);
        }
        if($type == "internal"){
            $offsets = array();
            foreach($blocks as $block){
                $block_num = strval($block["block_num"]);
                if(!array_key_exists($block_num, $offsets)){
                    $offsets[$block_num] = $block["top_offset"];
                    
                }
                elseif($offsets[$block_num] > $block["top_offset"]){
                    $offsets[$block_num] = $block["top_offset"];
                }
            }
            return $offsets;
        }
        else{
            $offsets = array();
            foreach($blocks as $block){
                $block_num = strval($block["block_num"]);
                if(!array_key_exists($block_num, $offsets)){
                    $offsets[$block_num] = $block["top_offset"];
                    
                }
                elseif($offsets[$block_num] > $block["top_offset"]){
                    $offsets[$block_num] = $block["top_offset"];
                }
            }
            return $offsets;
        }
    }

    private function find_offsets_of_block($block, $type="top"){
        global $wpdb;
        $table_name = $wpdb->prefix."form1_tsv";
        $user_id = get_current_user_id();
        if($type = "left"){
            $field_name = "left_offset";
        }
        else{
            $field_name = "top_offset";
        }
        
        $prepared_statement = $wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE  user_id = %d
        AND block_num = %d", $user_id, $block );
        $values = $wpdb->get_col( $prepared_statement );

        return $values;
    }

    private function seperate_page_into_left_right($user_id){
        global $wpdb;
        $table_name = $wpdb->prefix."form1_tsv";
        $field_name = "*";
        $prepared_statement = $wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE  user_id = %d
        AND left_offset < %d", $user_id, 650 );
        $opt["left"] = $wpdb->get_results( $prepared_statement );

        $prepared_statement = $wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE  user_id = %d
        AND left_offset > %d", $user_id, 690 );
        $opt["right"] = $wpdb->get_results( $prepared_statement );

        return $opt;
    }
    
    private function find_all_text_by_block($block){
        global $wpdb;
        $table_name = $wpdb->prefix."form1_tsv";
        $field_name = "text";
        $user_id = get_current_user_id();
        $prepared_statement = $wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE  user_id = %d
        AND block_num < %d", $user_id, $block);

        $values = $wpdb->get_col( $prepared_statement );

        return $values;
    }

}

function print_in_newline($text){
    echo nl2br($text. "\n");
}

function exchange_array_key_value($array_1, $array_2){

    if(sizeof($array_1) == sizeof($array_2)){
        foreach($array_1 as $key => $value){
            $new_key = $array_2[$key];
            $array_1[$new_key] = $value;

            unset($array_1[$key]);
        }
        return $array_1;
    }
}
// private function construct_entities($url){

//     $delimiter = "\t";
//     $fp = fopen($url.'/output.tsv', 'r');

//     $col_names = array();
//     $testing_count = 1;
//     $loop_count = 1;
//     $entities = array();
    
//     while(!feof($fp)){
//         $line = fgets($fp);
//         $data = str_getcsv($line, $delimiter);

//         if(in_array("page_num", $data)){
//             $col_names = $data;
//         }
//         elseif(sizeof($data) == 12){

//             $single_entity = $this->construct_single_entity($data, $col_names);
//             $entities[$loop_count - 1] = array();
//             foreach($single_entity as $key => $value){
//                 $entities[$loop_count - 1][$key] = $value;
//             }
//             //$result = $this->store_array_to_db($input_array);

//             // $result = 1;
//             // $opt = $this->tester->testing_store_function($input_array, $data, $testing_count, $testing_count);
//             // $loop_count += $opt['loop_count'];
//             // $testing_count += $opt['testing_count'];
            
//             $loop_count++;

//         }
        
//     }
//     // echo nl2br("The loop has been executed for " . $loop_count. " times \n");
//     // echo nl2br($testing_count. " correct counts \n");
//     return $entities;
// }

// private function construct_single_entity($data, $col_names){
//     $entity = array();

//     foreach($data as $key => $value){
//         $new_key = $col_names[$key];

//         if($new_key !== "text"){
//             $entity[$new_key] = intval($value);
//         }
//         else{
//             $entity[$new_key] = $value;
//         }

//     }
//     return $entity;
// }

// private function find_block_nums($entities){
//     $values = array();
//     foreach($entities as $entity){
//         array_push($values, $entity["block_num"]);
//     }
//     return $values;
// }

// private function short_data_by_block($blocks_array, $entities){
//     $opt = array();
//     $block_position_keys = array_unique($blocks_array);
//     $position_count = 0;
//     display_array($blocks_array);
//     return $opt;
// }

// private function concat_text($array){
//     $string = "";
//     foreach($array as $value){
//         $string = $string . " ". $value . " ";
//     }
//     return $string;
// }
