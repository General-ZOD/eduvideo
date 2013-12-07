<?php
class DbSignupConfirm extends Db {
    private $registry;

    public function __construct(array $db_settings, RegistryInterface $registry){
        $db_setting = array("server"=>$db_settings["server"], "database"=>$db_settings["db"], "username"=>$db_settings["db_cred"]["user"]["user"],
                            "password"=>$db_settings["db_cred"]["user"]["password"]); //I know; the credentials seem too long
        parent::__construct($db_setting);
        $this->registry = $registry;
    }
    
    public function activateUser($user_id){
        $query = 'update ' . Users::TABLE . ' set ' . Users::COL_IS_ACTIVE . '=true where ' . Users::COL_USER_ID . '=?;';
        try{
            $stmt = $this->db_obj->prepare($query);
            $stmt->bindValue(1, $user_id);
            $stmt->execute();
            return true;
        }catch (PDOException $e){
            return false;
        }
    }

    public function checkValidation($code){
        $query = 'select ' . RegistrationConfirmation::COL_USER_ID . ' from ' .
                RegistrationConfirmation::TABLE . ' where ' . RegistrationConfirmation::COL_VALIDATION_CODE
                . '=?;';
        try{
            $stmt = $this->db_obj->prepare($query);
            $stmt->bindValue(1, $code);
            $stmt->execute();
            $request = $stmt->fetch(PDO::FETCH_ASSOC);
            return $request;
        }catch (PDOException $e){
            return null;
        }
    }
} 