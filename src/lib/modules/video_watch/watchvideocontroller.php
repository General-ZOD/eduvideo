<?php
class WatchVideoController {
    private $watch_video_model;
    private $loggedin_data;
    private $watch_video_view;
    private $registry;
    private $path;

    public function __construct(array $path, RegistryInterface $registry){
        $this->registry = $registry;
        $this->path = $path;
        $this->loggedin_data = $this->registry->get('logged_data');
        $this->watch_video_view = $this->registry->get('watch_video_view');
    
        if (!isset($this->path[2])){
           header('location: /');
           exit;
        }else if ($this->path[2] == ''){
            header('location: /');
            exit;
        }
    }

    public function process(array $POST){
        $this->watch_video_view->setupContent($this->path[2]);
    }
} 