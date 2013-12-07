<?php
class AdminController {
    private $registry;
    private $path;
    private $controller;

    public function __construct($path, RegistryInterface $registry){
        $this->registry = $registry;
        $this->path = $path;
        switch($path[2]){
            case 'categories': $this->controller = $this->registry->get('admin_cat_controller', $this->path); break;
            case 'questions': $this->controller = $this->registry->get('admin_questions_controller', $this->path); break;
            case 'videos': $this->controller = $this->registry->get('admin_video_controller', $this->path); break;
            case 'users': $this->controller = $this->registry->get('admin_users_controller', $this->path); break;    
            default: header('location: /'); exit;
        }
    }

    public function process(array $POST){
        $this->controller->process($POST);
    }
} 