<?php
class SignupConfirmModel {
    private $db_confirm_obj;
    const SUCCESS = 0;
    const ERROR = 1;
    const INVALID_CONFIRMATION_CODE = 2;

    public function __construct(RegistryInterface $registry){
        $this->db_confirm_obj = $registry->get("db_signup_confirm");
    }

    /*
     * Validate confirmation_code and if active user if code is valid 
     */
    public function confirmUser($validation_code){
        $result = $this->db_confirm_obj->checkValidation($validation_code);
        
        if (null === $result)
            return self::ERROR;
        else if (!$result)
            return self::INVALID_CONFIRMATION_CODE;
        else{
            $user_id = $result[RegistrationConfirmation::COL_USER_ID];
            $result = $this->db_confirm_obj->activateUser($user_id);
            return ($result)? self::SUCCESS : self::ERROR;
        }
    }
} 