<?php

use thiagoalessio\TesseractOCR\TesseractOCR;

use function PHPSTORM_META\type;

class MiscProcessor extends Processor{
    
    public function run($standard_blocks){
        $data = $this->concat_standard_blocks_text($standard_blocks);
        // display_array($data);
        $type = $this->determine_type($data);
        if($type != "water"){
            $opt = $this->catch_keywords('/AMOUNT\sPAYABLE\s\$\s/', $data, $type);
        }
        else{
            $opt = $this->catch_keywords('/Balance\soutstanding\s/', $data, $type);
        }
        print_in_newline("___________");
        display_array($opt);
    }

    private function concat_standard_blocks_text($standard_blocks){
        $count = 0;
        $opt = array();
        $id_of_starting = 0;
        $text = "";
        $offsets = array();
        $line_heights = array();
        $line_bottom = array();
        //display_array($standard_blocks);
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
        return $opt;
    }

    private function determine_type($data){
        $emergency_count = 0;
        $tax_count = 0;
        $water_count = 0;
        $arr = array();
        foreach($data as $entity){
            $text = strtolower($entity["text"]);
            if(strpos($text, 'emergency services levy payable')){
                $emergency_count++;
            }
            elseif(strpos($text, 'land tax payable')){
                
                $tax_count++;
                
            }
            elseif(strpos($text, "water and sewer charges")){
                $water_count++;
                
            }

        }
        $arr['emergency payable'] = $emergency_count;
        $arr['land tax payable'] = $tax_count;
        $arr['water'] = $water_count;
        $max_value = max($arr);

        $key = array_keys($arr, $max_value);
        
        return $key[0];
    }

    private function catch_keywords($pattern, $data, $type){
        $result = array();
        foreach($data as $entity){
            $text = $entity["text"];
            if(preg_match($pattern, $text, $arr)){
                $value = preg_replace($pattern, "", $text);

                if(preg_match('/\=/', $value)){
                    $value = preg_replace('/\=/', "", $value);
                }
                $result[$type] = $value;
            }
        }

        return $result;
    }
}