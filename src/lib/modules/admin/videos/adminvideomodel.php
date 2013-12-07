<?php
class AdminVideoModel {
    private $db_upload_obj;
    private $loggedin_data_obj;
    private $input_data;
    private $registry;
    private $video_id;
    private $video_name;
    const SUCCESS = 0;
    const ERROR = 1;

    public function __construct(RegistryInterface $registry){
        $this->registry = $registry;
        $this->input_data = [];
    }   
    
    public function deleteVideo(array $data){
        $video_id = $data['video_id'];
        $this->db_upload_obj = $this->registry->get(Definitions::DB_VIDEOS_UPLOAD);
        $video_array = $this->db_upload_obj->selectVideo('', $video_id);
        if (count($video_array) > 0){
            $video_file = VID_SRC . $video_array[0][Videos::COL_FILE_LOCATION];
            $thumb_nail = THB_NAIL . $video_array[0][Videos::COL_THUMB_NAIL];
            $result = $this->db_upload_obj->deleteVideo($video_id);
            
            if ($result){
                try{
                    if (file_exists($video_file)) //remove video
                        unlink($video_file);
                        
                    if (file_exists($thumb_nail)) //remove thumb_nail
                        unlink($thumb_nail);
                    return self::SUCCESS;    
                }catch(Exception $e){
                    self::ERROR;
                }
            }
        }
        return self::SUCCESS;
    }
    
    public function getInputData(){
        return $this->input_data;
    }
    
    public function getVideoId(){
        return $this->video_id;
    }     
} 