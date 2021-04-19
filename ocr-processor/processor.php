<?php

use thiagoalessio\TesseractOCR\TesseractOCR;
abstract class Processor{
    private TesseractOCR $ocr;
    private string $uri;

    public function __construct($ocr_instance)
    { 
      $this->ocr = $ocr_instance;  
    }

    public function set_image_uri($uri){
    }

    private function run_ocr(){
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
     private function find_max_offset_of_block($blocks, $type = "db"){

    }

    private function find_min_offset_of_block($blocks, $type = "db"){

    }

}
?>