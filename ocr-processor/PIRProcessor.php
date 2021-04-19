<?php

class PIRProcessor extends Processor{

    public function run($standard_blocks){

        // $standard_left_blocks = $this->seperate_standard_blocks($standard_blocks, "left_offset");
        // $purified_left_blocks = $this->combine_close_standard_blocks($standard_left_blocks);

        // $keys = $this->concat_standard_blocks_text($purified_left_blocks, "standard");
        // $heights = $this->find_the_height_of_blocks($keys);
        // $standard_right_blocks = $this->seperate_standard_blocks($standard_blocks, "right_offset");
        // $purified_right_blocks = $this->combine_close_standard_blocks($standard_right_blocks);
        // $values = $this->concat_standard_blocks_text($purified_right_blocks, "standard");
        // $data = $this->pair_key_value_by_height($keys, $values, $heights);

        //return $data;

        $middle = $this->determine_middle($standard_blocks);
        $standard_left_blocks = $this->seperate_standard_blocks($standard_blocks, "left_offset", $middle);
        
        $purified_left_blocks = $this->combine_close_standard_blocks($standard_left_blocks);
        $keys = $this->concat_standard_blocks_text($purified_left_blocks, "standard");
        //display_array($keys);

        // Remove Page footer
        foreach($keys as $elementKey => $key) {
            if (preg_match("/CT.*?[0-9]+?\/[0-9]+?$/", $key["text"])) {
                // echo "<br>";
                // echo $key["top_offset"] . "||";
                // echo $elementKey;
                // echo "<br>";
                unset($keys[$elementKey]);
            }
        }
        $keys = array_values($keys);
        // display_array($keys);

        // Note: Reordered! Remove invalid keys first.
        
        $keys = $this->validate_keys($keys);
        $keys = $this->validate_keys($keys, "remove invalid keys");
        

        // display_array($purified_left_blocks);
        // display_array($keys);
        // display_array($standard_blocks);
        $heights = $this->find_the_height_of_blocks($keys);
        $standard_right_blocks = $this->seperate_standard_blocks($standard_blocks, "right_offset", $middle);
        $purified_right_blocks = $this->combine_close_standard_blocks($standard_right_blocks);
        $values = $this->concat_standard_blocks_text($purified_right_blocks, "standard");
        $values = $this->validate_values($values);
        $data = $this->pair_key_value_by_height($keys, $values, $heights);

        return $data;
    }

    public function __construct()
    {
        
    }

    public function test($standard_blocks){
        
        $middle = $this->determine_middle($standard_blocks);
        $standard_left_blocks = $this->seperate_standard_blocks($standard_blocks, "left_offset", $middle);
        
        $purified_left_blocks = $this->combine_close_standard_blocks($standard_left_blocks);
        $keys = $this->concat_standard_blocks_text($purified_left_blocks, "standard");
        $keys = $this->validate_keys($keys);
        $keys = $this->validate_keys($keys, "remove invalid keys");
        // display_array($purified_left_blocks);
        // display_array($keys);
        // display_array($standard_blocks);
        $heights = $this->find_the_height_of_blocks($keys);
        $standard_right_blocks = $this->seperate_standard_blocks($standard_blocks, "right_offset", $middle);
        $purified_right_blocks = $this->combine_close_standard_blocks($standard_right_blocks);
        $values = $this->concat_standard_blocks_text($purified_right_blocks, "standard");
        $values = $this->validate_values($values);
        $data = $this->pair_key_value_by_height($keys, $values, $heights);
        // display_array($data);
        return $data;
    }

    public function fabricator($standard_blocks, $type="left_key_value",){
        $middle = $this->determine_middle($standard_blocks) - 10;
        $standard_left_blocks = $this->seperate_standard_blocks($standard_blocks, "left_offset", $middle);
        $purified_left_blocks = $this->combine_close_standard_blocks($standard_left_blocks);

        $keys = $this->concat_standard_blocks_text($purified_left_blocks, "standard");
        $keys = $this->validate_keys($keys);        
        
        // $keys = $this->validate_keys($keys, "remove invalid keys");
        // display_array($keys);
        $heights = $this->find_the_height_of_blocks($keys);
        // display_array($heights);
        $standard_right_blocks = $this->seperate_standard_blocks($standard_blocks, "right_offset", $middle);
        $purified_right_blocks = $this->combine_close_standard_blocks($standard_right_blocks);
        $values = $this->concat_standard_blocks_text($purified_right_blocks, "standard");
        $values = $this->validate_values($values);
        $data = $this->pair_key_value_by_height($keys, $values, $heights);
        // display_array($data);
        return $data;

    }
    private function remove_redundant_text($text, $params){

        foreach($params as $pattern){
            $text = preg_replace($pattern, "", $text);
        }
    
        return $text;
    }
    
    private function count_newlines_in_text($text){
        $array = str_split($text);
        $count = 0;
        foreach($array as $char){
            if(preg_match('/[\x00-\x1F\x7F]/', $char)){
                $count++;
                echo $char;
            }
        }
    
        return $count;
    }
    
    private function transfer_text_to_array($text){
        $text = preg_replace('/[\x00-\x1F\x7F]/', ' ,', $text);
        $array = explode(' , ', $text);
        return $array;
     }
    
     private function standardize_array($array){
         foreach($array as $key => &$value){
             $value = str_replace(',', "", $value);
    
            if(strpos($value, "also") !== false){
                if($value == 'also'){
                    $temp = $array[$key - 1]. " " . $value . " ". $array[$key + 1];
                    $array[$key] = $temp;
                    unset($array[$key - 1]);
                    unset($array[$key + 1]);
        
                    $array = array_values($array);
                 }
                 elseif(endsWith($value, "also")){
                    $temp = $value . " ". $array[$key + 1];
                    $array[$key] = $temp;
                    unset($array[$key + 1]);
        
                    $array = array_values($array);
                 }
             }
         }
         $array = array_filter($array);
         $array = array_values($array);
         return $array;
     }

    private function standardize_data($text){
        $params = array('/Page\s[0-9]+\sof\s[0-9]+/', '/further information will be provided\)/');
        $text = $this->remove_redundant_text($text,$params);
        $array = $this->transfer_text_to_array($text);
        $array = $this->standardize_array($array);

        return $array;
    }

    private function combine_keys_and_values($keys, $values){
        $output = array();
        foreach($keys as $key=>$value){
            $output[$key] = $values[$value];
        }

        return $output;
    }

    private function seperate_standard_blocks($entities, $type, $middle_point){
        $opt = array();
        foreach($entities as $entity){

            if($type == "right_offset"){
                if($entity["left_offset"] >= $middle_point){
                    array_push($opt, $entity);
                }
            }
            else{
                if($entity["left_offset"] < $middle_point){
                    array_push($opt, $entity);
                }
            }
        }
        
        return $opt;
    }

    private function determine_middle($standard_blocks){
        $all_arr = array();
        
        foreach($standard_blocks as $block){
            $left_offset = $block["left_offset"];
            if($left_offset != 0){
                array_push($all_arr, $left_offset);
            }
        }
        
        $average = ceil( array_sum($all_arr) / count($all_arr) );
        $middle_point = $this->find_middle_point_in_offsets($all_arr, $average);
        if($middle_point == 0){
            return $average;
        }
        return $middle_point;
    }

    private function find_middle_point_in_offsets($all_arr, $average){
        $count = 0;
        $middle_point = 0;
        
        $left_arr = array();
        while($count < count($all_arr)){
            
            if($count == count($all_arr) - 1){
                break;
            }
            $current_offset = $all_arr[$count];
            $next_offset = $all_arr[$count + 1];
            // print_in_newline("current ". $current_offset . " next ".$next_offset);
            if($next_offset < $current_offset || $next_offset - $current_offset > 300){
                // print_in_newline("detect new lines");
                // print_in_newline("current ". $current_offset . " next ".$next_offset);
                if($next_offset - $average < 60 && $next_offset - $average > - 60){
                   
                    array_push($left_arr, $next_offset);
                    $count++;
                    continue;
                }

            }
            // print_in_newline("current ". $current_offset . " next ".$next_offset);
            // if($next_offset - $current_offset < 25){
            //     $middle_point = $next_offset;
            //     print_in_newline($middle_point);
            // }
            $count++;
        }
        if(count($left_arr) == 0){
            array_push($left_arr, intval($average));
        }
        $count=array_count_values($left_arr);//Counts the values in the array, returns associatve array
        arsort($count);//Sort it from highest to lowest
        $keys=array_keys($count);
        $most = $keys[0];
        foreach($left_arr as $offset){
            if($offset < $most && $most - $offset < 30){
                $middle_point = $offset;
            }
        }
        if(count($left_arr) == 1){
            $middle_point = $left_arr[0];
        }
        // display_array($left_arr);
        // if($middle_point < $average){
        //     $new_params = array_diff($all_arr, $left_arr);
        //     $new_params = array_values($new_params);
        //     $this->find_middle_point_in_offsets($new_params, $average);
        // }
        return $middle_point;
    }

    private function combine_close_standard_blocks($standard_blocks){
        $max_offsets = $this->find_max_offset_of_block($standard_blocks, "standard");
        $min_offsets = $this->find_min_offset_of_block($standard_blocks, 'standard');
        $count = 0;
        $opt = array();
        //display_array($standard_blocks);
        // foreach($standard_blocks as $block){
        //     if($block["conf"] == -1){
        //         print_in_newline("found newline");
        //         display_array($block);
        //     }
        // }
        //display_array($standard_blocks);
        // foreach($standard_blocks as $block){
        //     print_in_newline("block num ". $block["block_num"]);
        //     print_in_newline("Block text ". $block["text"]);
        //     print_in_newline("Top offset " . $block["top_offset"]);
        // }
        while($count < count($standard_blocks)){
            
            $entity = &$standard_blocks[$count];
            if($count == count($standard_blocks) - 1){
                break;
            }
            
            $next_entity = &$standard_blocks[$count + 1];
            $current_block = $entity["block_num"];
            $next_block = $next_entity["block_num"];
            // print_in_newline("Current block " . $current_block . " Next Blcok " . $next_block);
            // print_in_newline("Top offset of current " . $entity["top_offset"] .
            // " offset of next ". $next_entity["top_offset"]);
            // print_in_newline("This text " . $entity["text"] . " Next Text " . $next_entity["text"]);
            // print_in_newline("-----------------");
            if($current_block != $next_block){

                $min_offset_of_next = $min_offsets[$next_block];
                $max_offset_of_block = $max_offsets[$current_block];


                $bottom_of_current = $max_offset_of_block + $entity["height"];
                $diff = $min_offset_of_next - $max_offset_of_block;
                // print_in_newline("Max of current " . $max_offset_of_block);
                // print_in_newline("Min of next ". $min_offset_of_next);
                // print_in_newline("Diff " . $diff);

                if($diff < 30 && $diff > -20){
                    
                    $max_offsets[$current_block] = $max_offsets[$next_block];
                    $next_entity["block_num"] = $current_block;
                }
            }
            $count++;
        }
        return $standard_blocks;
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

    private function concat_standard_blocks_text($standard_blocks){
        $count = 0;
        $opt = array();
        $id_of_starting = 0;
        $text = "";
        $offsets = array();
        $line_heights = array();
        $line_bottom = array();
        // display_array($standard_blocks);
        while($count < count($standard_blocks)){
            $entity = $standard_blocks[$count];
            if($count == count($standard_blocks) - 1){
                $id_of_starting = $entity["id"];
                $text = $text . " ". $entity["text"];
                $opt_entity["text"] = $text;
                $opt_entity["id"] = $id_of_starting;
                $opt_entity["block_num"] = $entity["block_num"];
                if(count($offsets)){
                    $max_offset = max($offsets);
                    $min_offset = min($offsets);
                    // $opt_entity["offsets"] = $offsets;
                }
                else{
                    $min_offset = $entity["top_offset"];
                    $max_offset = $min_offset + $entity["height"];
                }
                if(count($line_heights)){
                    $height = max($line_heights);
                }
                else{
                    $height = $entity["height"];
                    $opt_entity["line_heights"] = $height;
                }
                $opt_entity["top_offset"] = $min_offset;
                if(count($line_bottom)){
                    $opt_entity["bottom_offset"] = max($line_bottom);
                }
                else{
                    $bottom = $max_offset + $height;
                    $opt_entity["bottom_offset"] = $bottom;
                }
                array_push($opt, $opt_entity);
            
                break;
            }
            $next_entity = $standard_blocks[$count + 1];
            $max_offset = $entity["top_offset"];
            if($entity["conf"] == -1){
                
                $height = 1;
            }
            else{
                $height = $entity["height"];
            }

            $bottom = $max_offset + $height;
            array_push($offsets, $max_offset);
            array_push($line_heights, $height);
            array_push($line_bottom, $bottom);
            if($entity["block_num"] == $next_entity["block_num"]){
                $text = $text . " ". $entity["text"];
                // print_in_newline("text ". $text);
                // print_in_newline("entity text " . $entity["text"]);
                // print_in_newline("Block num". $entity["block_num"]);
                // if($max_offset < $entity["top_offset"]){
                //     $max_offset = $entity["top_offset"];
                // }
                // if($max_height < $entity["height"]){
                //     $max_offset = $entity["top_offset"];
                // }
                array_push($offsets, $entity["top_offset"]);
                array_push($line_heights, $entity["height"]);
                array_push($line_bottom, $bottom);
            }
            else{
                $id_of_starting = $entity["id"];
                $text = $text . " ". $entity["text"];
                $opt_entity["text"] = $text;
                $opt_entity["id"] = $id_of_starting;
                $opt_entity["block_num"] = $entity["block_num"];
                $max_offset = max($offsets);
                $min_offset = min($offsets);
                // $height = max($line_heights);
                $opt_entity["top_offset"] = $min_offset;
                $opt_entity["bottom_offset"] = max($line_bottom);
                array_push($opt, $opt_entity);
                
                $offsets = array();
                $line_heights = array();
                $line_bottom = array();
                $text = "";
            }
            //print_in_newline($id_of_starting);
            $count++;
        }
        $opt = array_filter($opt);
        // display_array($opt);
        return $opt;
    }

    private function pair_key_value_by_height($left, $right, $heights){
        $right_heights = $this->find_the_height_of_blocks($right);
        $left_bottoms = $this->calculate_bottom_by_heights($left, $heights);
        $right_bottoms = $this->calculate_bottom_by_heights($right, $right_heights);
        // display_array($left);
        $top_of_left = array();
        $result = array();
        
        $temp_arr = array();
        // display_array($left);
        // display_array($right);
        foreach($left as $key){
            if($key["text"] != "continued"){
                array_push($top_of_left, $key["top_offset"]);
            }
        }
        foreach($right as $element){
           $right_top = $element["top_offset"];
           $right_bottom = $element["bottom_offset"];
            // print_in_newline("right top " . $right_top);
            // print_in_newline("right bottom " . $right_bottom);
            // display_array($element["offsets"]);
            // display_array($element["line_heights"]);
           $count = 0;
           
           while($count < count($left)){

                $entity = $left[$count];
                // $next_entity = $left[$count + 1];
                $left_top = $entity["top_offset"];
                $left_bottom = $left_bottoms[$entity["block_num"]];

                switch ($entity["text"]){
                    case "continued":
                    $left_bottom = min($top_of_left);
               }
            //    if(strlen($entity["text"]) < 6){
            //        $count++;
            //        continue;
            //    }

                if($right_top + 9 > $left_top && $right_top < $left_bottom){
                   if($right_bottom > $left_top && $right_bottom < $left_bottom){
                        // print_in_newline("Left top " .$left_top . " Right top " . $right_top);
                        // print_in_newline("Left bottom " .$left_bottom . " Right bottom " . $right_bottom);
        
                        // print_in_newline("Right text ". $element["text"]);
                        // print_in_newline("Left text " . $entity["text"]);
                        // print_in_newline("-------------------------------");
                        if($entity["text"] == "Prescribed encumbrance"){
                            print_in_newline("found");
                        }
                        if(array_key_exists($entity["text"], $temp_arr)){
                            $temp = $temp_arr[$entity["text"]];
                            $temp = $temp . " " . $element["text"];
                            $temp_arr[$entity["text"]] = $temp;
                        }
                        else{
                            $key = $entity["text"];
                            $value = $element["text"];
                            if(is_null($value) || $value == " "){
                                $count++;
                                continue;
                            }
                            
                            $temp_arr[$key] = $value;
                
                        }
                        
                        array_push($result, $temp_arr);
                        break;
                   }

                }
                $count++;
            }
        };
        return $temp_arr;
    }

    
    private function calculate_bottom_by_heights($blocks, $heights){
        $opt = array();

        foreach($blocks as $block){
            $block_num = $block["block_num"];
            $height = $heights[$block_num];
            $bottom_offset = $block["top_offset"] + $height;

            $opt[$block_num] = $bottom_offset;
        }

        return $opt;
    }

    private function find_the_height_of_blocks($blocks){
        $opt = array();
        $count = 0;
        while($count < count($blocks)){
            if($count == count($blocks) - 1){
                $height = 500;
                $opt[$blocks[$count]["block_num"]] = $height;
                break;
            }
            $current_top = $blocks[$count]["top_offset"];
            if($blocks[$count + 1]["text"] != 'continued'){

                $next_top = $blocks[$count + 1]["top_offset"];
                // print_in_newline("Text " . $blocks[$count]["text"]
                // ." block " . $blocks[$count]["block_num"] . " next top" . $next_top .
                // " this top " . $current_top);
                $height = $next_top - $current_top;
            }
            else{
                $height = 500;
            }
            $opt[$blocks[$count]["block_num"]] = $height;
            $count++;
        }

        return $opt;
    }

    private function validate_keys($keys, $type=""){

        $count = 0;
        $opt = array();
        $continued = array();
        $continued["text"] = "continued";
        $continued["block_num"] = 0;
        $continued["top_offset"] = 0;
        //display_array($keys);
        if($type == "remove invalid keys"){
            foreach($keys as $entity){
            
                if(strlen($entity["text"]) < 7){
                    unset($keys[$count]);
                }
                $count++;
            }
            
            $keys = array_values($keys);
        }
        else{
            $count = 0;
            while($count < sizeof($keys)){

                $entity = &$keys[$count];
                $next_entity = &$keys[$count + 1];
                $current_block = $entity["block_num"];
                $current_offset = $entity["top_offset"];
                $next_offset = $next_entity["top_offset"];
                if(!array_key_exists("bottom_offset", $continued)){
                    $continued["bottom_offset"] = $entity["top_offset"];
                }
                if($count == count($keys) - 2){
    
                    break;
                }
                $second_next_entity = &$keys[$count + 2];
    
                $second_next_offset = $second_next_entity["top_offset"];
     
                if($current_offset < $second_next_offset && $second_next_offset < $next_offset){
                    $entity["text"] = $entity["text"] . " " .$second_next_entity["text"];
                    $entity["bottom_offset"] = $second_next_entity["bottom_offset"];
                    $second_next_entity["block_num"] = $current_block;
                    $second_next_entity["top_offset"] = $current_offset;
                        
                }
                $count++;
            }
            array_push($keys, $continued);
        }

        return $keys;
    }
    private function validate_values($values){
        $pattern = '/Page\s[0-9]+\sof\s[0-9]+/';

        foreach($values as &$value){
            if(preg_match( $pattern, $value["text"])){
                $value["text"] = preg_replace($pattern, "", $value["text"]);
            }
        }

        return $values;
    }

}