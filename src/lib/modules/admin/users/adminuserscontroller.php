<?php
class AdminUsersController {
    private $users_model;
    private $loggedin_data;
    private $users_view;
    private $registry;
    private $path;

    //$path is of array('', 'admin', 'module', ...)
    public function __construct($path, RegistryInterface $registry){
        $this->registry = $registry;
        $this->path = $path;
        $this->loggedin_data = $this->registry->get('logged_data');
        $user_id = $this->loggedin_data->getData(Definitions::USER_ID);
        if (null === $user_id){//store destination and redirect to login 
            $destination = implode('/', $path);
            $this->loggedin_data->setData(Definitions::DESTINATION, $destination);
            header('location: /login');
            exit;
        }
        $this->users_view = $this->registry->get('admin_users_view');
    }

    public function process(array $POST){
        $this->users_model = $this->registry->get('admin_users_model');
        if (isset($POST['users_details_btn']))
            $this->users_view->setupContent('details', $POST['video_id']);
        else
            $this->users_view->setupContent('');
    }
} 