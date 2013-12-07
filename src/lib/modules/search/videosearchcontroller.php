<?php
class VideoSearchController {
    private $video_search_model;
    private $loggedin_data;
    private $video_search_view;
    private $registry;
    private $path;

    public function __construct(array $path, RegistryInterface $registry){
        $this->registry = $registry;
        $this->path = $path;
        $this->loggedin_data = $this->registry->get('logged_data');
        $this->video_search_view = $this->registry->get('video_search_view');
        
    }

    public function process(array $POST){
        if (isset($POST['video_search'])){
            $this->video_search_model = $this->registry->get('video_search_model');
            $result = $this->video_search_model->getSearch($POST);
            $this->video_search_view->setupContent('search', $result);
        }else
            $this->video_search_view->setupContent('');
    }
} 