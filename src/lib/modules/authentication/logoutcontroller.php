<?php
class LogoutController {
    private $loggedin_data;
    private $logout_view;
    private $registry;

    public function __construct($path, RegistryInterface $registry){
        $this->registry = $registry;
        $this->loggedin_data = $this->registry->get("logged_data");
    }

    public function process(){
        $user_id = $this->loggedin_data->getData(Definitions::USER_ID); //verify that user is currently logged in
        if (null === $user_id){
            header("location: /");
            exit;
        }
        $this->loggedin_data->logoutUser();
        $this->logout_view = $this->registry->get("logout_view");
        $this->logout_view->setupContent();
    }
} 