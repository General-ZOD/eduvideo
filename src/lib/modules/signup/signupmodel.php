<?php
class SignupModel {
    private $db_signup_obj;
    private $input_data;
    private $validation_array;
    const SUCCESS = 0;
    const EMPTY_EMAIL = 1;
    const INVALID_EMAIL_FORMAT = 2;
    const INVALID_PASSWORD = 3;
    const ERROR = 4;
    const USER_EXISTS = 5;
    const INVALID_BIRTHDAY = 6;
    const TOO_YOUNG_TO_REGISTER = 7;


    public function __construct(RegistryInterface $registry){
        $this->db_signup_obj = $registry->get("db_signup");
        $this->input_data = [];
    }

    public function getInputData(){
        return $this->input_data;
    }

    public function getValidationData(){
        return $this->validation_array;
    }

    public function registerUser(array $input_data){
        //check email and password not empty; check password and confirm are same; check that user is at least 13 yrs; save and send confirmation code
        $this->input_data = $input_data;
        $email = trim($input_data["email"]);
        $password = trim($input_data["password"]);
        $confirm_pass = trim($input_data["confirm_password"]);
        $dob_month = trim($input_data["dob_month"]);
        $dob_day = trim($input_data["dob_day"]);
        $dob_year = trim($input_data["dob_year"]);

        //verify email
        if ($email == "")
            return self::EMPTY_EMAIL;
        else if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            return self::INVALID_EMAIL_FORMAT;

        //verify password
        if ($password != "" && $password == $confirm_pass){
        }else
            return self::INVALID_PASSWORD;

        //verify date of birth
        $current_date = new DateTime();
        $dob = DateTime::createFromFormat('Y-m-d', "$dob_year-$dob_month-$dob_day");

        if ($dob === false)
            return self::INVALID_BIRTHDAY;

        $dob_gen_errors = $dob->getLastErrors();
        $current_date->sub(new DateInterval("P13Y")); //subtract 13 yrs from current date
        if ($dob_gen_errors["warning_count"] > 0 || $dob_gen_errors["error_count"] > 0)//invalid DOB
            return self::INVALID_BIRTHDAY;
        else if ($current_date->format("Y") < $dob->format("Y"))
            return self::TOO_YOUNG_TO_REGISTER;

        $password = password_hash($password, PASSWORD_DEFAULT);
        $insert_data = ["email"=>$email, "password"=>$password, "dob"=>$dob->format("Y-m-d")];
        $result = $this->db_signup_obj->checkEmail($email);
        if ($result === null)//error accessing the system
            return self::ERROR;
        else if (is_array($result))//a user by that email already exists
            return self::USER_EXISTS;
        else{
            $user_id = $this->db_signup_obj->registerUser($insert_data);
            if ($user_id === null)
                return self::ERROR;
            else{
                $validation_code = hash('md5', $current_date->format('Y-m-d'));
                $this->validation_array = ["user_id"=>$user_id, "code"=>$validation_code];
                $validation_result = $this->db_signup_obj->insertValidationCode($this->validation_array);

                return ($validation_result === null)? self::ERROR : self::SUCCESS;
            }

        }
    }
} 