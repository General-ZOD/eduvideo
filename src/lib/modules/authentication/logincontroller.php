<?php
class LoginController {
    private $login_model;
    private $loggedin_data;
    private $login_view;
    private $registry;

    public function __construct($path, RegistryInterface $registry){
        $this->registry = $registry;
        $this->login_view = $this->registry->get("login_view");
    }

    public function process(array $POST){
        if (isset($POST["post"])){ //logging into the system
            $this->login_model = $this->registry->get("login_model");
            $validation_result = $this->login_model->validate($POST);
            $number_of_attempts=0;
            //0-success, 1-db error, 2-email not available, 3-invalid email/password, 4-inactive, 5-locked out
            switch ($validation_result){
                case 0: //success
                    $this->loggedin_data = $this->registry->get("logged_data");
                    $output_data = $this->login_model->getData();
                    $this->loggedin_data->setUserData($output_data); //set data in session/cookie
                    $destination = $this->loggedin_data->getData(Definitions::DESTINATION);
                    if (null === $destination)
                      header("location: /profile");
                    else{
                        $this->loggedin_data->removeData(Definitions::DESTINATION);
                      header("location: {$destination}");
                    }
                    exit; break;
                case 3: //invalid email/password
                    $result = $this->login_model->updateNumberOfAttempts(); //update number of attempts
                    switch($result){
                        case 1: //an error occurred
                             break;
                        case true: //acct has just been locked
                            break;
                        case false: //acct has not been locked
                            break;
                    }
                    $validation_result = $result;
                    $number_of_attempts = $this->login_model->getNumberOfAttempts();
                    break;
            }
            $this->login_view->setupContent($from_post=true, $validation_result, $number_of_attempts);
        }else{
            $this->login_view->setupContent($from_post=false);
        }

    }
} 