<?php
class LoginModel {
    private $db_login_obj;
    private $number_of_attempts;
    private $input_data;
    private $output_data;
    const SUCCESS = 0;
    const ERROR = 1;
    const NO_USER = 2;
    const INVALID_EMAIL_OR_PASSWORD = 3;
    const IS_INACTIVE = 4;
    const IS_LOCKED_OUT = 5;
    const EMPTY_EMAIL = 6;
    const INVALID_EMAIL_FORMAT = 7;
    private $max_attempts;


    public function __construct($max_attempts, RegistryInterface $registry){
        $this->db_login_obj = $registry->get("db_login");
        $this->number_of_attempts = 0;
        $this->output_data = array();
        $this->input_data = array();
        $this->max_attempts = $max_attempts["max_attempts"];
    }

    public function getData(){
        return $this->output_data;
    }
    public function getNumberOfAttempts(){
        return $this->number_of_attempts;
    }

    public function updateNumberOfAttempts(){
        ++$this->number_of_attempts;
        if ($this->number_of_attempts >= $this->max_attempts)
            $lock_acct = true;
        else
            $lock_acct = false;
        $result = $this->db_login_obj->updateNumberOfAttempts($this->number_of_attempts, $lock_acct);
        return ($result !== null)? $lock_acct: self::ERROR;
    }

    public function validate(array $input_data){
        //validate returns null|false|array("user_id", "email", "password", "dob", "login_attempt_number", "user_name", "last_login", "is_locked_out", "is_active")
        $email = trim($input_data["email"]);
        if ($email == "")
            return self::EMPTY_EMAIL;
        else if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            return self::INVALID_EMAIL_FORMAT;

        $this->output_data = $this->db_login_obj->validate($email);
        if ($this->output_data === null)//error accessing the system
            return self::ERROR;
        else if ($this->output_data === false)//no user by that email
            return self::NO_USER;
        else if ($this->output_data[Users::COL_IS_ACTIVE] == "0")
            return self::IS_INACTIVE;
        else if ($this->output_data[Users::COL_IS_LOCKED_OUT])
            return self::IS_LOCKED_OUT;
        else{
            if (password_verify($input_data["password"], $this->output_data[Users::COL_PASSWORD]) === true){
                $this->db_login_obj->updateLoginTime();
                return self::SUCCESS;
            }else{
                $this->number_of_attempts = $this->output_data[Users::COL_LOGIN_ATTEMPT_NUMBER];
                return self::INVALID_EMAIL_OR_PASSWORD;
            }
        }
    }
} 