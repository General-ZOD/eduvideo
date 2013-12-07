<?php
class VideoUploadModel {
    private $db_upload_obj;
    private $video_conversion_obj;
    private $loggedin_data_obj;
    private $input_data;
    private $registry;
    private $video_id;
    private $video_name;
    const SUCCESS = 0;
    const ERROR = 1;
    const INVALID_CATEGORY = 2;
    const NO_TITLE = 3;
    const INVALID_VIDEO_TYPE = 4;

    public function __construct(RegistryInterface $registry){
        $this->registry = $registry;
        $this->input_data = [];
    }
    
    public function checkIfVideoNameExist(){
        $this->loggedin_data_obj = $this->registry->get(Definitions::LOGGED_DATA_MODEL);
        $this->video_name = $this->loggedin_data_obj->getData('video_name');
        return ($this->video_name !== null)? true : false;
    }
    
    public function checkVideoData(array $data){
        $this->input_data = $data;
        if (trim($this->input_data[Definitions::VIDEO_CATEGORIES]) == '')
          return self::INVALID_CATEGORY;
        $this->db_upload_obj = $this->registry->get(Definitions::DB_VIDEOS_UPLOAD);
        $cat_result = $this->db_upload_obj->checkVideoCategory($this->input_data[Definitions::VIDEO_CATEGORIES]);
        if ($cat_result === null)
          return self::ERROR;
        else if (count($cat_result) == 0)
          return self::INVALID_CATEGORY;
        
        if (trim($this->input_data[Definitions::VIDEO_TITLE]) == '')
          return self::NO_TITLE;
        if (preg_match('/^video\/*/', trim($this->input_data['video_type'])) == 0)
          return self::INVALID_VIDEO_TYPE;        
        $this->loggedin_data_obj = $this->registry->get(Definitions::LOGGED_DATA_MODEL);
        $this->loggedin_data_obj->setData('video_name', $this->loggedin_data_obj->getData(Definitions::USER_ID) . '_' .
                                          $this->input_data['video_name']);
        $this->loggedin_data_obj->setData('upload_data', $this->input_data);
        return self::SUCCESS;
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
    
    public function encodeVideo(){
        $this->loggedin_data_obj = $this->registry->get(Definitions::LOGGED_DATA_MODEL);
        $this->video_name = $this->loggedin_data_obj->getData('video_name');
        $user_id = $this->loggedin_data_obj->getData(Definitions::USER_ID);

        if ($this->video_name === null) return false;
        
        $this->video_conversion_obj = $this->registry->get('video_conversion');
        $movie_name = md5($user_id . time()) . '.flv';
        $array = [];
        //encode video
        $result = $this->video_conversion_obj->encodeVideo(TMP_VIDEO . $this->video_name, VID_SRC . $movie_name, $size='480x360');
        if ($result > 0)
            return self::ERROR;
        
        $thb_nail = md5($user_id . time()) . '.png';
        $thb_array = [];
        //take picture; result not needed
        $result = $this->video_conversion_obj->extractThumbnail(VID_SRC . $movie_name, THB_NAIL . $thb_nail, $size='320x240');
        
        //save the data into the db
        $this->input_data = $this->loggedin_data_obj->getData('upload_data');
        $cat_id = $this->input_data[Definitions::VIDEO_CATEGORIES];
        $title = $this->input_data[Definitions::VIDEO_TITLE];
        $presenter = $this->input_data[Definitions::VIDEO_INFO];
        $desc = $this->input_data[Definitions::VIDEO_DESC];
        $tag = $this->input_data[Definitions::VIDEO_TAG];
        
        $this->db_upload_obj = $this->registry->get('db_video_upload');
        $result = $this->db_upload_obj->insertVideo($cat_id, $user_id, $movie_name, $title, $desc, $presenter, $tag, $thb_nail);
        $this->loggedin_data_obj->removeData('video_name');
        $this->loggedin_data_obj->removeData('upload_data');        
        return ($result)? self::SUCCESS : self::ERROR;
    }
    
    public function getInputData(){
        return $this->input_data;
    }
    
    public function getVideoId(){
        return $this->video_id;
    }    
    
    public function mergeVideoPackets(){
        try{
            $infile = fopen('php://input', 'rb');
            $outfile = fopen(TMP_VIDEO . $this->video_name, 'ab');
    
            while ($buffer = fread($infile, 4096)){
                fwrite($outfile, $buffer);
            }
            fclose($infile);
            fclose($outfile);
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    
    public function updateVideoData(array $data){
        //$user_id = $this->loggedin_data_obj->getData(Definitions::USER_ID);
        $this->input_data = $data;
        $this->video_id = trim($this->input_data['video_id']);
        if ($this->video_id == '')
          return self::INVALID_CATEGORY;
        $this->db_upload_obj = $this->registry->get(Definitions::DB_VIDEOS_UPLOAD);
        $cat_result = $this->db_upload_obj->checkVideoCategory($this->input_data[Definitions::VIDEO_CATEGORIES]);
        if ($cat_result === null)
          return self::ERROR;
        else if (count($cat_result) == 0)
          return self::INVALID_CATEGORY;
        
        if (trim($this->input_data[Definitions::VIDEO_TITLE]) == '')
          return self::NO_TITLE;       
        //$this->loggedin_data_obj = $this->registry->get(Definitions::LOGGED_DATA_MODEL);
        
        $cat_id = $this->input_data[Definitions::VIDEO_CATEGORIES];
        $title = $this->input_data[Definitions::VIDEO_TITLE];
        $presenter = $this->input_data[Definitions::VIDEO_INFO];
        $desc = $this->input_data[Definitions::VIDEO_DESC];
        $tag = $this->input_data[Definitions::VIDEO_TAG];        
        $result = $this->db_upload_obj->updateVideo($cat_id, $title, $desc, $presenter, $tag, $this->video_id);
        return ($result)? self::SUCCESS : self::ERROR;
    }  
} 