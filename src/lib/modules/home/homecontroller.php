<?php
class HomeController {
    private $home_view;
    private $registry;

    public function __construct($path, RegistryInterface $registry){
        $this->registry = $registry;
        $this->home_view = $this->registry->get("home_view");
    }

    public function process(array $POST){
        //for now, just display the homepage
        $this->home_view->setupContent($from_post=false);
    }
} 