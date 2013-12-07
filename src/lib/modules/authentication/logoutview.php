<?php
class LogoutView extends View {
    private $registry;
    private $model;
    private $err_msg;
    private $content;

    public function __construct(RegistryInterface $registry){
        $this->registry = $registry;
        $this->content = "";
        $this->err_msg = "";
        $this->db_categories = $this->registry->get('db_categories');
    }

    protected function displayContent(){
        echo $this->template;
    }

    protected function getTemplate(){
        $this->template = (file_exists(TEMPLATES . 'anonymous.html'))? file_get_contents(TEMPLATES . 'anonymous.html') : '';
    }

    public function setupContent(){
        //get the template, compose the message, put message in template, display the content
        $this->getTemplate();
        $this->setOuterNavigation("");
        $this->setFooterNavigation();
        $css='<link rel="stylesheet" href="/css/login.css" type="text/css" />';
        $this->setHeader($title="EduVideo.com | Login", $meta="", $css=$css, $js="");

        $content = '
            <div id="login_div">
             <h1>Logged Out</h1>
             <p id="logout_success">You\'ve successfully logged out</p>
                         <script type="text/javascript">
              window.onload = function(){
                window.setTimeout(function(){window.location.href="/"}, 7000);
              }
            </script>
          </div>
        ';
        $this->template = str_replace("{{content}}", $content, $this->template);
        $this->displayContent();
    }
} 