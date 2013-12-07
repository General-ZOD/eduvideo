<?php
class SignupConfirmView extends View {
    private $registry;
    private $model;
    private $err_msg;
    private $content;
    private $data;

    public function __construct(RegistryInterface $registry){
        $this->registry = $registry;
        $this->db_categories = $this->registry->get('db_categories');        
        $this->content = "";
        $this->err_msg = "";
    }

    protected function displayContent(){
        echo $this->template;
    }

    protected function getTemplate(){
        $this->template = (file_exists(TEMPLATES . 'anonymous.html'))? file_get_contents(TEMPLATES . 'anonymous.html') : '';
    }

    public function setupContent($code, $message=''){
        //get the template, compose the message, put message in template, display the content
        $this->getTemplate();
        $this->setOuterNavigation("signup");

        $this->setFooterNavigation();
        $css='<link rel="stylesheet" href="/css/signup.css" type="text/css" />';
        $this->setHeader($title="EduVideo.com | Signup Confirmation", $meta="", $css=$css, $js="");

        $content = '<h1>Signup Confirmation</h1>';
        switch($code){
            case 3: $content .= '<p id="signupconfirm_error">Bad Request</p>'; break;
            case 2: $content .= '<p id="signupconfirm_error">Validation Code does not exist or it has expired. You\'ll have to <a href="/signup">signup</a>
            again</p>'; break;
            case 1: $content .= '<p id="signupconfirm_error">Internal System Error. Please, try again shortly</p>'; break;
            case 0: $content .= '<p id="signupconfirm_success">Your account has been activated. You can <a href="/login">login</a>
            anytime.</p>
            <script type="text/javascript">
              window.onload = function(){
                window.setTimeout(function(){window.location.href="/login"}, 4000);
              }
            </script>'; break;
        }

        $this->template = str_replace("{{content}}", $content, $this->template);
        $this->displayContent();
    }
} 