<?php
class SignupMail implements MailInterface {
    private $from;
    private $to;
    private $body;
    private $header;
    private $subject;

    public function __construct(RegistryInterface $registry){
        $this->from = null;
        $this->to = [];
        $this->body = null;
        $this->header = null;
        $this->subject = null;
    }

    public function getParameters(array $parameters){
        $this->from = (isset($parameters["from"]))? $parameters["from"] : null;
        $this->to = (isset($parameters["to"]))? $parameters["to"] : null;
        $this->header = (isset($parameters["header"]))? $parameters["header"] : null;
        $this->subject = (isset($parameters["subject"]))? $parameters["subject"] : null;
    }

    public function getTemplate(){
        $this->body = (file_exists(MAIL_TEMPLATE . "signup.html"))? file_get_contents(MAIL_TEMPLATE . "signup.html") : "";
    }

    public function setMailBody(array $content){
        $link = DOMAIN . 'signup/confirm/' . $content["code"];
        $mail_content = '<div>
                           You have been registered. Please, click on the <a href="' . $link . '">following link</a> to confirm your membership and activate your account
                         </div>';
        $this->body = str_replace("{{recipient}}", $content["recipient"], $this->body);
        $this->body = str_replace("{{content}}", $mail_content, $this->body);
    }

    public function sendMail(){
        try{
            $this->to = implode(", ", $this->to);
            mail($this->to, $this->subject, $this->body, $this->header);
        }catch(Exception $e){}
    }
} 