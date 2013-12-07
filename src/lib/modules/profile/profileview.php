<?php
class ProfileView extends View {
    private $registry;
    private $model;
    private $err_msg;
    private $content;
    private $user_id;
    private $loggedin_data_obj;

    public function __construct(RegistryInterface $registry){
        $this->registry = $registry;
        $this->loggedin_data_obj = $this->registry->get("logged_data");
        $this->db_categories = $this->registry->get('db_categories');
        $this->content = "";
        $this->err_msg = "";
    }

    private function _setErrorContent($code){
        //
    }

    protected function displayContent(){
        echo $this->template;
    }

    protected function getTemplate(){
        $this->user_id = $this->loggedin_data_obj->getData(Definitions::USER_ID);
        $this->template = (file_exists(TEMPLATES . "loggedin.html"))? file_get_contents(TEMPLATES . "loggedin.html") : "";
    }

    public function setupContent($from_post=false){
        //get the template, compose the message, put message in template, display the 
        $role_id = $this->loggedin_data_obj->getData(Definitions::ROLE_ID);
        $this->getTemplate();
        $this->setInnerNavigation($role_id, 'profile');
        $this->setFooterNavigation();
        $css='<link rel="stylesheet" href="/css/profile.css" type="text/css" />';
        $this->setHeader($title="EduVideo.com | My Profile", $meta="", $css=$css, $js="");

        $form = '
            <div id="login_div">
             <h1>My Profile</h1>
             My Profile will be here
          </div>
        ';
        $content = $this->err_msg . $form;
        $this->template = str_replace("{{content}}", $content, $this->template);
        $this->displayContent();
    }
} 