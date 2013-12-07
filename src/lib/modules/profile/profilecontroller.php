<?php
class ProfileController {
    private $profile_model;
    private $loggedin_data;
    private $profile_view;
    private $registry;

    public function __construct($path, RegistryInterface $registry){
        $this->registry = $registry;
        $this->loggedin_data = $this->registry->get("logged_data");
        $user_id = $this->loggedin_data->getData(Definitions::USER_ID);
        if (null === $user_id){//store destination and redirect to login 
            $destination = implode("/", $path);
            $this->loggedin_data->setDestination($destination);
            header("location: /login");
            exit;
        }
        $this->profile_view = $this->registry->get("profile_view");
    }

    public function process(array $POST){
        $this->profile_view->setupContent($from_post=false);
    }
} 