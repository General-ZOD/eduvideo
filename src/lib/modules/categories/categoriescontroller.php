<?php
class CategoriesController {
    private $categories_model;
    private $loggedin_data;
    private $categories_view;
    private $registry;
    private $path;

    public function __construct(array $path, RegistryInterface $registry){
        $this->registry = $registry;
        $this->path = $path;
        $this->loggedin_data = $this->registry->get("logged_data");
        $this->categories_view = $this->registry->get("categories_view");

        if (!isset($this->path[2])){
           header('location: /');
           exit;
        }else if ($this->path[2] == ''){
            header('location: /');
            exit;
        }
    }

    public function process(array $POST){
        $this->categories_view->setupContent($this->path[2]);
    }
} 