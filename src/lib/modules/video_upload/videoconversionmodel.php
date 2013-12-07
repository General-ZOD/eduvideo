<?php
class VideoConversionModel {
    const ERROR = 1;
    
    public function __construct(){}
    
    public function encodeVideo($mov_name, $encoded_name, $size='480x360'){
        $array = [];
        $conversion_code = 0;
        try{
            exec("ffmpeg -i $mov_name -r 24 -s $size -ar 44100 $encoded_name", $array, $conversion_code);
            if ($conversion_code == 0)
               unlink($mov_name);
            return ($conversion_code == 0)? $conversion_code : self::ERROR;
        }catch(Exception $e){
            return self::ERROR;
        }

    }
    
    public function extractThumbnail($mov_name, $thumb_nail, $size='320x240'){
        $thb_array = [];
        $conversion_code = 0;
        try{
            exec("ffmpeg -i  $mov_name -r 1 -y -s $size -vframes 1 $thumb_nail", $thb_array, $conversion_code);
            return ($conversion_code == 0)? $conversion_code : self::ERROR;
        }catch(Exception $e){
            return self::ERROR;
        }
    }
       
} 