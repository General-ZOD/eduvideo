<?php
class SignupController {
    private $signup_model;
    private $signup_view;
    private $signup_mail;
    private $confirm_model;
    private $confirm_view;
    private $registry;
    private $path;

    public function __construct(array $path, RegistryInterface $registry){
        $this->registry = $registry;
        $this->path = $path;
    }

    public function process(array $POST){
        if (isset($POST["register"])){
            // check if email & password are properly formatted. Then, check if the email is already taken; save and then send confirm mail
            $this->signup_model = $this->registry->get("signup_model");
            $validation_result = $this->signup_model->registerUser($POST);
            //0-success, 1-empty email, 2-invalid email format, 3-invalid password, 4-error, 5-user already exists, 6-invalid birthday, 7-too young
            switch ($validation_result){
                case 0: //success; send email
                    $validation_data = $this->signup_model->getValidationData();
                    $this->signup_mail = $this->registry->get("signup_mail");
                    $header= "MIME-Version: 1.0 \r\nContent-type: text/html; charset=utf-8 \r\nFrom: VLIS Team <vlis@pazzionate.com> \r\n";
                    $this->signup_mail->getParameters(["from"=>"vlis@pazzionate.com", "to"=>[$POST["email"]], "header"=>$header, "subject"=>"Registration Confirmation"]);
                    $this->signup_mail->getTemplate();
                    $this->signup_mail->setMailBody(["recipient"=>$POST["email"], "code"=>$validation_data["code"]]);
                    $this->signup_mail->sendMail();
                    break;
                case 4:
                    exit('Error accessing the database');
                    break;
            }
            $this->signup_view = $this->registry->get("signup_view");
            $this->signup_view->setupContent($from_post=true, $validation_result);
        }else if (isset($this->path[2]) && $this->path[2] == 'confirm'){
            $this->confirm_view = $this->registry->get("signupconfirm_view");
            if (isset($this->path[3])){
              $confirm_code = $this->path[3];
              $this->confirm_model = $this->registry->get("signupconfirm_model");
              $result = $this->confirm_model->confirmUser($confirm_code);
              $this->confirm_view->setupContent($result);
            }else{
                $this->confirm_view->setupContent(3);
            }
            
        }else{
            $this->signup_view = $this->registry->get("signup_view");
            $this->signup_view->setupContent($from_post=false);
        }
    }
} 