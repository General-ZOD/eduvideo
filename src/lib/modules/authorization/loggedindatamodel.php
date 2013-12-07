<?php
class LoggedInDataModel {
    private $storage;
    private $is_cookie;

    public function __construct($storage_type){
        switch($storage_type){
            case Definitions::SESSION:
                if (!isset($_SESSION)){
                    session_start();
                    $this->storage = $_SESSION;
                    $this->is_cookie = false;
                } break;
            case Definitions::COOKIE:
                $this->storage = $_COOKIE;
                $this->is_cookie = true;
                break;
        }
    }

    private function _delCookie($name, $path='/'){
        setcookie($name, '', time()-3600, $path);
    }
    
    public function getData($name){
        return (isset($this->storage[$name]))? $this->storage[$name] : null;
    }     

    public function logoutUser(){
        if ($this->is_cookie){
            if (isset($this->storage['user_id']))
                $this->_delCookie('user_id', '/');
            if (isset($this->storage['dob']))
                $this->_delCookie('dob', '/');
            if (isset($this->storage['email']))
                $this->_delCookie('email', '/');
            if (isset($this->storage['username']))
                $this->_delCookie('username', '/');
            if (isset($this->storage['last_login']))
                $this->_delCookie('last_login', '/');
            if (isset($this->storage['role_id']))
                $this->_delCookie('role_id', '/');
        }else{
            session_destroy();
            session_unset();
        }
    }
    
    public function removeData($name){
        if ($this->is_cookie)
            $this->_delCookie($name);
        else
            unset($_SESSION[$name]);
    }   

    private function _setCookie($name, $value, $time, $path='/'){
        setcookie($name, $value, $time, $path);
    }
    
    public function setData($name, $value){
        if ($this->is_cookie)
            $this->_setCookie($name, $value, '', '/');
        else
            $_SESSION[$name] = $value;
    }
    
    public function setUserData(array $value){
        /* this must always match the data returned in loginmodel.php
         * array("user_id", "email", "password", "dob", "login_attempt_number", "user_name", "last_login", "is_locked_out", "is_active", "role_id")
         */
        if ($this->is_cookie){
            if (isset($value[Users::COL_USER_ID]))
                $this->_setCookie(Definitions::USER_ID, $value[Users::COL_USER_ID], '', '/');
            if (isset($value[Users::COL_DOB]))
                $this->_setCookie(Definitions::DOB, $value[Users::COL_DOB], '', '/');
            if (isset($value[Users::COL_EMAIL]))
                $this->_setCookie(Definitions::EMAIL, $value[Users::COL_EMAIL], '', '/');
            if (isset($value[Users::COL_USER_NAME]))
                $this->_setCookie(Definitions::USERNAME, $value[Users::COL_USER_NAME], '', '/');
            if (isset($value[Users::COL_LAST_LOGIN]))
                $this->_setCookie(Definitions::LAST_LOGIN, $value[Users::COL_LAST_LOGIN], '', '/');
            if (isset($value[Users::COL_ROLE_ID]))
                $this->_setCookie(Definitions::ROLE_ID, $value[Users::COL_ROLE_ID], '', '/');
        }else{  
            if (isset($value[Users::COL_USER_ID]))
                $_SESSION[Definitions::USER_ID] = $value[Users::COL_USER_ID];
            if (isset($value[Users::COL_DOB]))
                $_SESSION[Definitions::DOB] = $value[Users::COL_DOB];
            if (isset($value[Users::COL_EMAIL]))
                $_SESSION[Definitions::EMAIL] = $value[Users::COL_EMAIL];
            if (isset($value[Users::COL_USER_NAME]))
                $_SESSION[Definitions::USERNAME] = $value[Users::COL_USER_NAME];
            if (isset($value[Users::COL_LAST_LOGIN]))
                $_SESSION[Definitions::LAST_LOGIN] = $value[Users::COL_LAST_LOGIN];
            if (isset($value[Users::COL_ROLE_ID]))
                $_SESSION[Definitions::ROLE_ID] = $value[Users::COL_ROLE_ID];                
        }

    }
} 