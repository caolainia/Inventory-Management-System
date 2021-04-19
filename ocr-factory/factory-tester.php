<?php

class FactoryTester{

    public function testing_store_function($input_array, $data, $loop_count){

        $testing_result = $this->validate_inserted_data($input_array, $data);
        
        $loop_count++;
        if($testing_result != 0){
            $opt['testing_count'] = 1;
        }
        else{
            echo nl2br("error occured in ". $loop_count. " times \n");
            echo nl2br("Inserted: \n");
            display_array($input_array);
            echo nl2br("Original: \n");
            display_array($data);
            $opt['testing_count'] = 0;
        }

        $opt["loop_count"] = 1;

        return $opt;
    }
    
    private function validate_inserted_data($inserted_row, $origin_row){

        unset($inserted_row["user_id"]);
        if(sizeof($inserted_row) == sizeof($origin_row)){
            $count = 0;
            foreach($inserted_row as $key => $value){
                if(strval($value) == $origin_row[$count]){

                    $count++;
                    return 1;
                }

                else{
                    
                    return 0;
                }
            }
        }
        else{
            return 0;
        }
    }


}