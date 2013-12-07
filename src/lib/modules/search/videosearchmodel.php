<?php
class VideoSearchModel {
    private $db_video_obj;
    private $search_result;
    const SUCCESS = 0;
    const ERROR = 1;
    const EMPTY_CATEGORY = 2;
    const DUPLICATE_CATEGORY = 3;

    public function __construct(RegistryInterface $registry){
        $this->db_video_obj = $registry->get(Definitions::DB_VIDEOS);
        $this->search_result = [];
    }
    
    public function getSearchData(){
        return $this->search_result;
    }
    
    public function getSearch(array $data){
        $search_word = trim(urldecode($data['video_search']));
        if ($search_word == '')
          return self::SUCCESS;
        $result = $this->db_video_obj->searchVideo($search_word);
        if ($result === null)
          return self::ERROR;
        $this->search_result = $result;
        return self::SUCCESS;
    }    
} 