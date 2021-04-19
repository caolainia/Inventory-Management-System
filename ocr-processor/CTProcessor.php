<?php

class CTProcessor extends Processor{

    private $default_titles = array("Certificate of Title", "Estate Type", "Registered Proprietor", "Description of Land",
     "Easements", "Schedule of Dealings", "Notations", "Last Sale Details", "Constraints", "Valuation Numbers",
     "Valuation Record", "Parcels", "Values", "Building Details");
    
    private $pair = array();
    private PIRProcessor $pir;

    public function __construct()
    {
       $this->pir = new PIRProcessor();
    }
    public function run($standard_blocks){
        
        $blocks = $this->concat_standard_blocks_text($standard_blocks);
        $titles = $this->find_titles_in_page($blocks);
        $contents = $this->find_content_by_title($titles, $standard_blocks);
        $pair = $this->get_key_value($contents, $titles);
        
        // display_array($blocks);
        // display_array($pair);

        return $pair;
    }

    private function find_titles_in_page($blocks){
        $opt = array();
        $temp = array();
        $all_heights = array();
        $continued = array();
        foreach($blocks as $block){
            
            $height = $block["average"];
            $text = $block["text"];                
            if(preg_match("/[a-zA-Z]/", $text)){
                if($height > 30 && $block["confs"] > 85){
                    $text = preg_replace('/[\x00-\x1F\x7F]/', '', $text);
                    
                    $title["top_offset"] = $block["top_offset"];
                    $title["text"] = $text;
                    $title["average_height"] = $height;
                    $title["block_num"] = $block["block_num"];
                    array_push($temp, $title);
                    array_push($all_heights, $height);
                }
            }
        }
        $count = 0;
        $continued["top_offset"] = 0;
        $continued["average_height"] = array_sum($all_heights) / count($all_heights);
        $continued["text"] = "Continued";
        $continued["block_num"] = 0;

        array_push($opt, $continued);

        foreach($temp as $e){
            array_push($opt, $e);
        }

        return $opt;
    }

    private function find_content_by_title($titles, $standard_blocks){
        $opt = array();
        $block_count = 0;
        foreach($standard_blocks as $block){
            $count = 0;
            if($block_count == count($standard_blocks) - 1){

                break;
            }
            $next_block = $standard_blocks[$block_count + 1];
            while($count < count($titles)){
                $title = $titles[$count];
                $block_top = $block["top_offset"];
                $current_top = $title["top_offset"];
                $next_block_top = $next_block["top_offset"];
                $diff = $next_block_top - $block_top - $block["height"];
                $next_height = $next_block["height"];
                $top_offsets = array();
                if($count == count($titles) - 1){
                    // display_array(array("next top" => $next_block_top, "this top" => $block_top, "this height" => $block["height"],
                    // "this height" => $block["height"], "this text" => $block["text"], "Next height" => $next_height, "Next Text" => $next_block["text"]
                    // , "diff" => $diff, "title top" => $title["top_offset"]));
                    
                    if($block_top > $current_top + 9 && $block_top < $current_top + 500){
                        $block["belong_to"] = $title["text"];
                        //$title["top_offset"] = $next_block_top;
                        $title_text = $title["text"];

                        array_push($opt, $block);
                    }
                    break;
                }
                $next_title = $titles[$count + 1];
                $next_top = $next_title["top_offset"];

                if($block_top > $current_top + 9 && $block_top < $next_top){
                //    print_in_newline("Current title top " . $current_top . " block top "
                //     . $block_top . " next top " . $next_top);
                //     print_in_newline("Block text ".$block["text"]);
                //     display_array($block);
                    if($block["block_num"] == $title["block_num"]){
                        // print_in_newline("Text ".$block["text"]. " Title " . $title["text"]);
                        $count++;
                        continue;
                    }
                    $block["belong_to"] = $title["text"];
                    array_push($opt, $block);
                }
                // elseif($block_top < $toppest + 15){
                //     if($block["block_num"] == $title["block_num"]){
                        
                //         $count++;
                //         continue;
                //     }
                //     $block["belong_to"] = "Continued";
                //     array_push($opt, $block);
                // }
                $count++;
            }
            $block_count++;
        }
        // display_array($opt);
        return $opt;
    }

    private function get_key_value($contents, $titles){
        $opt = array();
        $type = $this->verify_block_type($contents);
        // display_array($contents);
        // display_array($type);
        foreach($type as $key => $value){
           // print_in_newline("type " . $value);
            
        }

        foreach($titles as $title){
            $title_text = $title["text"];
            $temp = array();
            $count = 0;
            $text = "";
            $key_value = array();

            if(strstr(strtolower($title_text), "certificate of title")){
                $key_value = $this->combine_contents("cot", $contents, $title_text);
                $key_value = $this->key_value_pair_make_up($key_value);
                $entity["key"] = $title_text;
                $entity["value"] = $key_value;

                // $arr = array();
                // foreach($contents as $content){
                //     if($content["belong_to"] == $title_text){
                //         array_push($arr, $content);
                //     }
                // };
                // $key_value = $this->pir->fabricator($arr);
                // $entity["key"] = $title_text;
                // $entity["value"] = $key_value;
            }
            elseif(strstr(strtolower($title_text), "schedule of dealings")){
                $key_value = $this->combine_contents("schedule of dealings", $contents, $title_text);
                $key_value = $this->key_value_pair_make_up($key_value, "vertical");
                $entity["key"] = $title_text;
                $entity["value"] = $key_value;
            }
            elseif(strstr(strtolower($title_text), "notations")){
                $key_value = $this->combine_contents("notations", $contents, $title_text);
                $key_value = $this->key_value_pair_make_up($key_value);

                // display_array($key_value);
                $entity["key"] = $title_text;
                $entity["value"] = $key_value;
            }
            elseif(strstr(strtolower($title_text), "last sale details")){
                
                $key_value = $this->combine_contents("last sale details", $contents, $title_text);
                $key_value = $this->key_value_pair_make_up($key_value);

                // display_array($key_value);
                $entity["key"] = $title_text;
                $entity["value"] = $key_value;
            }
            elseif(strstr(strtolower($title_text), "constraints")){
                $key_value = $this->combine_contents("last sale details", $contents, $title_text);

                $key_value = $this->key_value_pair_make_up($key_value, "else");

                $entity["key"] = $title_text;
                $entity["value"] = $key_value;
            }
            elseif(strstr(strtolower($title_text), "continued")){
                $key_value = $this->combine_contents("continued", $contents, $title_text);
                // $key_value = $this->key_value_pair_make_up($key_value);

                // display_array($key_value);
                $entity["key"] = $title_text;
                $entity["value"] = $key_value;
            }
            elseif(strstr(strtolower($title_text), "schedule of dealings")){
                $key_value = $this->combine_contents("notations", $contents, $title_text);
                $key_value = $this->key_value_pair_make_up($key_value, "vertical");

                // display_array($key_value);
                $entity["key"] = $title_text;
                $entity["value"] = $key_value;
            }
            elseif(strstr(strtolower($title_text), "valuation record")){
                $arr = array();
                foreach($contents as $content){
                    if($content["belong_to"] == $title_text){
                        array_push($arr, $content);
                    }
                }
                $some_arr = $this->pir->fabricator($arr);
                $entity["key"] = $title_text;
                $entity["value"] = $some_arr;
            }
            elseif(strstr(strtolower($title_text), "building details")){
                $arr = array();
                foreach($contents as $content){
                    if($content["belong_to"] == $title_text){
                        array_push($arr, $content);
                    }
                }
                // display_array($arr);
                $key_value = $this->pir->fabricator($arr);
                $entity["key"] = $title_text;
                $entity["value"] = $key_value;
            }
            elseif(strstr(strtolower($title_text), "parcels")){
                $key_value = $this->combine_contents("notations", $contents, $title_text);
                $key_value = $this->key_value_pair_make_up($key_value, "vertical");

                // display_array($key_value);
                $entity["key"] = $title_text;
                $entity["value"] = $key_value;
            }
            elseif(strstr(strtolower($title_text), "values")){
                $key_value = $this->combine_contents("notations", $contents, $title_text);
                $key_value = $this->key_value_pair_make_up($key_value, "vertical");

                // display_array($key_value);
                $entity["key"] = $title_text;
                $entity["value"] = $key_value;
            }
            else{
                $temp = $this->combine_contents("else", $contents, $title_text);
                $combined_text = $this->concat_standard_blocks_text($temp);
                
                $entity["key"] = $title_text;
                $entity["value"] = $combined_text;
            }

            array_push($opt, $entity);
        }
        return $opt;
    }

    private function combine_contents($type, $contents, $title_text){
       $count = 0;
       $text = "";
       $opt = array();
       switch($type){
           case "cot":
            foreach($contents as $content){
                if($title_text == $content["belong_to"]){
                    if($count == count($contents) - 1){
                        $last_content = $contents[$count - 1];
                        $bool = $this->verify_exception_contents($last_content, $content, "left-right");
                    }
                    else{
                        $next_content = $contents[$count + 1];
                        $bool = $this->verify_exception_contents($content, $next_content, "left-right");
                    }
                    $bool = false;
                    $next_content = $contents[$count + 1];
                    $bool = $this->verify_exception_contents($content, $next_content, "left-right");
                    // $text = $text . " " . $content["text"];
                    $text = $text . " ". $content["text"];
                    
                    switch(true){
                        case $bool === false:
                            array_push($opt, $text);
                            $text = "";
                            break;
                        case $bool === "new line":
                            array_push($opt, $text);
                            $text = "";
                            break;
                        default:
                            break;
                    }
                }
                $count++;
            }
            break;
            
            case "schedule of dealings":
                foreach($contents as $content){
                    if($title_text == $content["belong_to"]){
                        if($count == count($contents) - 1){
                            $last_content = $contents[$count - 1];
                            $bool = $this->verify_exception_contents($last_content, $content, "left-right");
                        }
                        else{
                            $next_content = $contents[$count + 1];
                            $bool = $this->verify_exception_contents($content, $next_content, "left-right");
                        }
                        $bool = false;
                        $next_content = $contents[$count + 1];
                        $bool = $this->verify_exception_contents($content, $next_content, "top-bottom");
                        // $text = $text . " " . $content["text"];
                        $text = $text . " ". $content["text"];
                        if($bool == false){
                            array_push($opt, $text);
                            $text = "";
                        }
                        elseif($bool === "new line"){ 
                            array_push($opt, $text);
                            array_push($opt, "new line");
                            $text = "";
                            // array_push($opt, "new line");
                        }
                    }
                    $count++;
                }
            break;

            case "notations":
                
                $status = "default";
                foreach($contents as $content){
                    if($title_text == $content["belong_to"]){
                        if($count == count($contents) - 1){
                            $last_content = $contents[$count - 1];
                            $bool = $this->verify_exception_contents($last_content, $content, "left-right", $status);
                        }
                        else{
                            $next_content = $contents[$count + 1];
                            $bool = $this->verify_exception_contents($content, $next_content, "left-right", $status);
                        }
                        // $text = $text . " " . $content["text"];
                        $text = $text . " ". $content["text"];
                        
                        if($bool == false){

                            array_push($opt, $text);
                            $text = "";
                        }
                        elseif($bool === "new line"){ 
                            array_push($opt, $text);
                            
                            array_push($opt, "new line");
                            $text = "";
                            $status = "new line";
                        }
                    }
                    $count++;
                }
                
                // display_array($opt);
            break;

            case "last sale details":
                $status = "default";
                foreach($contents as $content){
                    if($title_text == $content["belong_to"]){
                        if($count == count($contents) - 1){
                            $last_content = $contents[$count - 1];
                            $bool = $this->verify_exception_contents($last_content, $content, "left-right", $status);
                        }
                        else{
                            $next_content = $contents[$count + 1];
                            $bool = $this->verify_exception_contents($content, $next_content, "left-right", $status);
                        }
                        $text = $text . " ". $content["text"];

                        if($content["text"] !== " "){
                            if($bool == false){

                                array_push($opt, $text);
                                $text = "";
                            }
                            elseif($bool === "new line"){ 
                                array_push($opt, $text);
                                array_push($opt, "new line");
                                $text = "";
                                $status = "new line";
                            }
                        }
                    }
                    $count++;
                };
            break;

            case "continued":
                foreach($contents as $content){
                    if($title_text == $content["belong_to"]){
                        if($count == count($contents) - 1){
                            $last_content = $contents[$count - 1];
                            $bool = $this->verify_exception_contents($last_content, $content, "left-right");
                        }
                        else{
                            $next_content = $contents[$count + 1];
                            $bool = $this->verify_exception_contents($content, $next_content, "left-right");
                        }
                        $text = $text . " ". $content["text"];

                        if($content["text"] !== " "){
                            if($bool == false){

                                array_push($opt, $text);
                                $text = "";
                            }
                            elseif($bool === "new line"){ 
                                array_push($opt, $text);
                                array_push($opt, "new line");
                                $text = "";
                                $status = "new line";
                            }
                        }
                    }
                    $count++;
                };
            break;

            case "else":
                foreach($contents as $content){
                    if($title_text == $content["belong_to"]){
                        array_push($opt, $content);
                    }
                }
            break;
       }

       return $opt;
    }

    private function key_value_pair_make_up($key_value, $type="default"){
        $opt = array();
        $count = 0;
        $width = array();
        foreach($key_value as $key => $value){
            $len = strlen($value);
            array_push($width, $len);
            $value = preg_replace('/[\x00-\x1F\x7F]/', '', $value);
            if($value == " "){
                unset($key_value[$key]);
            }
        }

        if($type == "default"){
            $key_value = array_values($key_value);
            if(count($width) != 0){
    
                $average_widht = array_sum($width);
                
            }
            else{
                $average_widht = 1;
            }
            $fix = $average_widht - ($average_widht * 0.35);
            foreach($key_value as $key=>$value){
                if(strlen($value) > $average_widht - $fix){
                    print_in_newline("len " . strlen($value) . " average " . $fix);
                    $first_part = substr($value, 0, ceil(strlen($value) / 3));
                    array_push($key_value, $first_part);
                    array_push($key_value, $value);
                    unset($key_value[$key]);
                }
            }
            
            $key_value = array_values($key_value);
        }

        if($type == "vertical"){
            $keys = array();
            $pos = 0;
            foreach($key_value as $key => $value){
                if($value != "new line"){
                    array_push($keys, $value);
                    $pos++;
                }
                else{
                    $pos++;
                    break;
                }
            }
            $count = 0;
            
            while($pos < count($key_value)){
                if($key_value[$pos] != "new line"){
                    $opt[$keys[$count]] = $key_value[$pos];
                }
                else{
                    break;
                }
                $count++;
                $pos++;
            }
            return $opt;
        }
        else{
            foreach($key_value as $key => $value){
                if($key == count($key_value) -1){
                    break;
                }
                $next = &$key_value[$key + 1];
                if($key == 0){
                    
                    if($next == "new line"){
                        $next = "continued";
                    }
                    continue;
                }
                $next = &$key_value[$key + 1];
                $last = $key_value[$key - 1];
                if($last == "new line" && $next == "new line"){
                    $next = "continued";
                }
            }

            foreach($key_value as $key => $value){
                if($value == "new line"){
                    unset($key_value[$key]);
                }
            }

            $key_value = array_values($key_value);
            foreach($key_value as $key => $value){
                if($count == count($key_value) - 1){
                    break;
                }
                
                if($key % 2){
                    
                    $count++;
                    continue;
                }
                else{
                    
                    if($key_value[$count + 1] == "continued"){
                        if($count != count($key_value) - 2){
                            $target = $key_value[$count + 2];
                            $opt[$value] =  $target;
                        }
                        else{
                            $opt[$value] = $key_value[$count + 1];
                        }

                    }
                    else{
                        
                        $opt[$value] = $key_value[$count + 1];
                    }
                }
                $count++;
            }
            return $opt;
        }

    }

    private function verify_block_type($contents){
        $count = 0;
        $opt = array();
        while($count < count($contents)){
            $content = $contents[$count];
            if($count == count($contents) - 1){
                break;
            }
            $next_content = $contents[$count + 1];
            $this_left = $content["left_offset"];
            $next_left = $next_content["left_offset"];
            $next_width = $next_content["width"];
            $next_word_count = strlen($next_content["text"]);
            $next_average_width = $next_width / $next_word_count;
            $this_width = $content["width"];
            $this_text = $content["text"];
            $this_block = $content["block_num"];
            $next_block = $next_content["block_num"];
            $this_right = $this_left + $this_width;
            $this_bottom = $content["top_offset"] + $content["height"];
            $next_top = $next_content["top_offset"];
            $top_diff = $next_top - $this_bottom;
            $gap = $next_left - $this_right;
            

            if($gap > $next_average_width * 2){
                if(!isset($opt[$content["id"]])){
                    $opt[$content["id"]] = "key-value";
                }
                 
                // print_in_newline("text " . $this_text);
                // print_in_newline("Gap " . $gap);
                // print_in_newline("next text " . $next_content["text"]);
                // print_in_newline("average " . $next_average_width);
            }
            elseif($top_diff > 10){
                $opt[$content["id"]] = "new line";
            }
            else{
                $opt[$content["id"]] = "concat";
            }
            $count++;
        }

        return $opt;
    }
    private function verify_exception_contents($content, $next_content, $type = "default", $status = "default"){
        $arr = array();

        switch($type){
            case "left-right":
                $this_left = $content["left_offset"];
                $next_left = $next_content["left_offset"];
                $next_width = $next_content["width"];
                $next_word_count = strlen($next_content["text"]);
                $next_average_width = $next_width / $next_word_count;
                $this_width = $content["width"];
                $this_text = $content["text"];
                $this_block = $content["block_num"];
                $next_block = $next_content["block_num"];
                $this_right = $this_left + $this_width;
                $this_bottom = $content["top_offset"] + $content["height"];
                $next_top = $next_content["top_offset"];
                $top_diff = $next_top - $this_bottom;
                $left_diff = $next_left - $this_left;
                $gap = $next_left - $this_right;
                // display_array($content);
                // display_array($next_content);
                // if($this_text == "NIL"){
                //     print_in_newline("text " . $this_text);
                //                         print_in_newline("Top diff " . $top_diff);
                //     print_in_newline("Next top ". $next_top);

                //     display_array(array("This left"=> $this_left, "next left" => $next_left,
                //     "This width" => $this_width, "Next width" => $next_width, "this text" => $this_text,
                //     "Next text" => $next_content["text"], "This block" => $this_block,
                //     "Next block" => $next_block));
                // }
        
                // if($content["text"] == "12411316"){
                //     print_in_newline("found");
                //                         print_in_newline("Top diff " . $top_diff);
                //     print_in_newline("Next top ". $next_top . " gap " . $gap);

                //     display_array(array("This left"=> $this_left, "next left" => $next_left,
                //     "This width" => $this_width, "Next width" => $next_width, "this text" => $this_text,
                //     "Next text" => $next_content["text"], "This block" => $this_block,
                //     "Next block" => $next_block));
                // }
                if($gap > $next_average_width * 2){
                    // print_in_newline("Top diff " . $top_diff);
                    // print_in_newline("Next top ". $next_top);

                    // display_array(array("This left"=> $this_left, "next left" => $next_left,
                    // "This width" => $this_width, "Next width" => $next_width, "this text" => $this_text,
                    // "Next text" => $next_content["text"], "This block" => $this_block,
                    // "Next block" => $next_block));
                    
                    return false;
                }
                elseif($top_diff > 10){
                  return "new line";
                }
                // elseif($top_diff < 0){

                    
                // }
                else{
                    // print_in_newline("text " . $this_text);
                    return true;
                }
            break;
                
            case "top-bottom":
                $this_left = $content["left_offset"];
                $next_left = $next_content["left_offset"];
                $next_height = $next_content["height"];
                $next_word_count = strlen($next_content["text"]);
                $next_average_height = $next_height / $next_word_count;
                $this_width = $content["width"];
                $this_text = $content["text"];
                $this_block = $content["block_num"];
                $next_block = $next_content["block_num"];
                $this_right = $this_left + $this_width;
                $this_bottom = $content["top_offset"] + $content["height"];
                $next_top = $next_content["top_offset"];
                $left_diff = $next_left - $this_right;
                $gap = $next_top - $this_bottom;
                // display_array($content);
                // display_array($next_content);
                if($gap > $next_average_height * 2 && $left_diff > 0){
                    // print_in_newline("Top diff " . $top_diff);
                    // print_in_newline("Next top ". $next_top);

                    // display_array(array("This left"=> $this_left, "next left" => $next_left,
                    // "This width" => $this_width, "Next width" => $next_width, "this text" => $this_text,
                    // "Next text" => $next_content["text"], "This block" => $this_block,
                    // "Next block" => $next_block));
                    return false;
                }
                elseif($left_diff > 20){
                    return false;
                }
                elseif($left_diff < 0){
                    return 'new line';
                }
                else{
                    return true;
                }
            break;
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
        $confs = array();
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
                    $height = $this->find_average_height($line_heights);
                    $opt_entity["average"] = $height;
                }
                else{
                    $height = $entity["height"];
                    $opt_entity["average"] = array($height);
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
                if(count($confs)){
                    $opt_entity["confs"] = $this->find_average_conf($confs);
                }
                else{
                    $opt_entity["confs"] = array($entity["conf"]);
                }
                break;
            }
            $next_entity = $standard_blocks[$count + 1];
            $diff = $entity["top_offset"] - $next_entity["top_offset"];
            if($diff < 20 && $diff > - 20 && $entity["block_num"] != $next_entity["block_num"]){
                $next_entity["block_num"] = $entity["block_num"];
                // print_in_newline("Next top ". $next_entity["top_offset"] . " Next text ". $next_entity["text"]);
            }
            $max_offset = $entity["top_offset"];
            $height = $entity["height"];
            $bottom = $max_offset + $height;
            array_push($offsets, $max_offset);
            array_push($line_heights, $height);
            array_push($line_bottom, $bottom);
            $conf = $entity["conf"];
            array_push($confs, $conf);
            $combo = 0;
            // if($entity["text"] == "-"){
            //     print_in_newline("found you");
            //     print_in_newline("Next top ". $next_entity["top_offset"] . " Next text ". $next_entity["text"]);
            //     display_array($offsets);
            //     print_in_newline($max_offset);
            // }
            // if($combo == 0){
            //     array_push($offsets, $entity["top_offset"]);
            //     array_push($line_heights, $entity["height"]);
            //     array_push($line_bottom, $bottom);
            // }

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
                $combo++;
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
                $height = $this->find_average_height($line_heights);
                $opt_entity["average"] = $height;
                $opt_entity["top_offset"] = $min_offset;
                $opt_entity["bottom_offset"] = max($line_bottom);
                $opt_entity["confs"] = $this->find_average_conf($confs);
                array_push($opt, $opt_entity);
                $offsets = array();
                $line_heights = array();
                $line_bottom = array();
                $confs = array();
                $text = "";
                $combo == 0;
            }
            //print_in_newline($id_of_starting);
            $count++;
        }
        $opt = array_filter($opt);
        return $opt;
    }

    private function find_average_height($line_heights){
        $length = count($line_heights);
        sort($line_heights);
        if($length == 1){
            return $line_heights[0];
        }
        elseif($length > 3){

            if($length % 2){
                
                $middle = ($length - 1) / 2;
                // print_in_newline("middle ". $middle . " len " .$length);
                // print_in_newline("return ". $line_heights[$middle]);
                return $line_heights[$middle];
            }
            else{
                
                $first = $length / 2;
                $last = $first - 1;
                $sum = $line_heights[$first] + $line_heights[$last];
                $middle =  $sum / 2;
                // print_in_newline("middle ". $line_heights[$first] . " len " .$line_heights[$last]);
                // print_in_newline("return ". $middle);
                return $middle;
            }
        }
        else{
            $sum = array_sum($line_heights);

            return $sum/$length;
        }
    }

    private function find_average_conf($confs){
        $length = count($confs);
        sort($confs);
        if($length == 1){
            return $confs[0];
        }
        elseif($length > 3){
            $count = 0;
            foreach($confs as $conf){
                if($conf < 50){
                    $count++;
                }
            }
            $value = $count / $length;
            if($value > 0.3){

                return 0;
            }

            if($length % 2){
                
                $middle = ($length - 1) / 2;
                // print_in_newline("middle ". $middle . " len " .$length);
                // print_in_newline("return ". $confs[$middle]);
                return $confs[$middle];
            }
            else{
                
                $first = $length / 2;
                $last = $first - 1;
                $sum = $confs[$first] + $confs[$last];
                $middle =  $sum / 2;
                // print_in_newline("middle ". $confs[$first] . " len " .$confs[$last]);
                // print_in_newline("return ". $middle);
                return $middle;
            }
        }
        else{
            $sum = array_sum($confs);

            return $sum/$length;
        }
    }

}