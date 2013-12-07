<?php
class LoginView extends View {
    private $registry;
    private $model;
    private $err_msg;
    private $content;
    private $user_id;

    public function __construct(RegistryInterface $registry){
        $this->registry = $registry;
        $this->db_categories = $this->registry->get('db_categories');
        $this->content = "";
        $this->err_msg = "";
    }

    private function _setErrorContent($code){
        $this->model = $this->registry->get("login_model");
        $number_of_attempts = $this->model->getNumberOfAttempts();
        switch ($code){
            case 6: //no email provided
                $this->err_msg='<p id="login_error">Please, provide an email</p>'; break;
            case 7: //email provided is in invalid format
                $this->err_msg='<p id="login_error">Email provided is in the wrong format</p>'; break;
            case 1: //error within the db
                $this->err_msg='<p id="login_error">There is an error in the system</p>'; break;
            case 2: //email is not in the system
                $this->err_msg='<p id="login_error">Email does not exist in the system</p>'; break;
            case true: //invalid email/password; acct has just been locked
                $this->err_msg='<p id="login_error">You have reached your maximum attempt. Account has been locked</p>'; break;
            case false:
                $this->err_msg = '<p id="login_error">Invalid Email/Password. You have made ' . $number_of_attempts . ' Please, try again</p>'; break;
            case 4: // is inactive
                $this->err_msg='<p id="login_error">Account is not active. You cannot login</p>'; break;
            case 5: //is locked out
                $this->err_msg='<p id="login_error">Your account has been locked. You cannot login</p>'; break;
        }
    }

    protected function displayContent(){
        echo $this->template;
    }

    protected function getTemplate(){
        $logged_in_data = $this->registry->get("logged_data");
        $this->user_id = $logged_in_data->getData(Definitions::USER_ID);
        $this->template = (null === $this->user_id)? file_get_contents(TEMPLATES . "anonymous.html") : file_get_contents(TEMPLATES . "loggedin.html");
    }

    public function setupContent($from_post=false, $code=0, $number_of_attempts=0){
        //get the template, compose the message, put message in template, display the content
        $this->getTemplate();
        if (null === $this->user_id)
            $this->setOuterNavigation("login");
        else
            $this->setInnerNavigation();
        $this->setFooterNavigation();
        $css='<link rel="stylesheet" href="/css/login.css" type="text/css" />';
        $this->setHeader($title="EduVideo.com | Login", $meta="", $css=$css, $js="");

        if ($from_post) $this->_setErrorContent($code, $number_of_attempts);
        $form = '
            <div id="login_div">
             <h1>Please, login</h1>
              <form method="post" action="">
                <table id="login_form_tbl" cellspacing="0" cellpadding="0">
                  <tr>
                    <th id="login_form_top_th" colspan="2">&nbsp;</th>
                  </tr>
                  <tr>
                    <td><strong>Email:</strong></td>
                    <td><input type="text" name="email" id="email" placeholder="Email Address" value="" /> </td>
                  </tr>
                  <tr>
                    <td><strong>Password:</strong></td>
                    <td><input type="password" name="password" id="password" value="" /> </td>
                  </tr>
                  <tr>
                    <td colspan="2" align="center"><button type="submit" name="login_btn" id="login_btn">Login</button> </td>
                  </tr>
                  <tr>
                    <input type="hidden" name="post" />
                    <th id="login_form_bottom_th" colspan="2">&nbsp;</th>
                  </tr>
                </table>
              </form>
          </div>
        ';
        $content = $this->err_msg . $form;
        $this->template = str_replace("{{content}}", $content, $this->template);
        $this->displayContent();
    }
} 