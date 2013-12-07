<?php
class SignupView extends View {
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

    private function _setErrorContent($code){
        $this->model = $this->registry->get("signup_model");
        $this->data = $this->model->getInputData();
        switch ($code){
            case 0:
                $this->err_msg='<p id="signup_success">Registration was successful.
                               A confirmation link has been sent to your email. This link will expire after <strong>24 hours</strong>.
                              You need to confirm your account</p>'; break;
            case 1: //error within the db
                $this->err_msg='<p id="signup_error">Email must be provided</p>'; break;
            case 2:
                $this->err_msg='<p id="signup_error">Email format is not valid</p>'; break;
            case 3:
                $this->err_msg='<p id="signup_error">Password and Password Confirm must match</p>'; break;
            case 5:
                $this->err_msg='<p id="signup_error">Email already exists</p>'; break;
            case 6:
                $this->err_msg='<p id="signup_error">Birthday provided is invalid</p>'; break;
            case 7:
                $this->err_msg='<p id="signup_error">Only those 13 and older can register</p>'; break;
        }
    }

    protected function displayContent(){
        echo $this->template;
    }

    protected function getTemplate(){
        $this->template = (file_exists(TEMPLATES . 'anonymous.html'))? file_get_contents(TEMPLATES . 'anonymous.html') : '';
    }

    public function setupContent($from_post=false, $code=null){
        //get the template, compose the message, put message in template, display the content
        $this->getTemplate();
        $this->setOuterNavigation("signup");

        $this->setFooterNavigation();
        $css='<link rel="stylesheet" href="/css/signup.css" type="text/css" />';
        $this->setHeader($title="EduVideo.com | Register", $meta="", $css=$css, $js="");

        $content = '<h1>Registration</h1>';
        if ($code === 0){
            $this->_setErrorContent($code);
            $content .= $this->err_msg;
        }else{
            if ($from_post) $this->_setErrorContent($code);
            $form = '
          <form method="post" action="">
            <table id="register_form_tbl" cellspacing="0" cellpadding="0">
              <tr>
                <th id="register_form_top_th" colspan="2">&nbsp;</th>
              </tr>
              <tr>
                <td><strong>Email:</strong></td>
                <td><input type="text" name="email" id="email" placeholder="Email Address" value="' . $this->data["email"] . '" /> </td>
              </tr>
              <tr>
                <td><strong>Password:</strong></td>
                <td><input type="password" name="password" id="password" value="' . $this->data["password"] . '" /> </td>
              </tr>
              <tr>
                <td><strong>Confirm Password:</strong></td>
                <td><input type="password" name="confirm_password" id="confirm_password" value="' . $this->data["confirm_password"] . '" /> </td>
              </tr>
              <tr>
                <td><strong>Date of Birth:</strong></td>
                <td>Month ' . DateAndTime::getMonthAsSelect("dob_month", $this->data["dob_month"], "") . ' Day ' . DateAndTime::getDayAsSelect("dob_day", $this->data["dob_day"], "") . ' Year ' .
                DateAndTime::getYearAsSelect("2000", "dob_year", $this->data["dob_year"], "") . ' </td>
              </tr>
              <tr>
                <td colspan="2"><small>By submitting this form, you have agreed to the <a href="/tos">terms and agreement</a>.</small>  </td>
              </tr>
              <tr>
                <td colspan="2" align="center"><button type="submit" name="register_btn" id="register_btn">Login</button> </td>
              </tr>
              <tr>
                <input type="hidden" name="register" />
                <th id="register_form_bottom_th" colspan="2">&nbsp;</th>
              </tr>
            </table>
          </form>';
            $content .= $this->err_msg . $form;
        }

        $this->template = str_replace("{{content}}", $content, $this->template);
        $this->displayContent();
    }
} 