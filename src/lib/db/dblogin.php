<?php
class DbLogin extends Db {
    private $registry;
    private $email;

    public function __construct(array $db_settings, RegistryInterface $registry){
        $db_setting = array("server"=>$db_settings["server"], "database"=>$db_settings["db"], "username"=>$db_settings["db_cred"]["user"]["user"],
                            "password"=>$db_settings["db_cred"]["user"]["password"]); //I know; the credentials seem too long
        parent::__construct($db_setting);
        $this->registry = $registry;
    }
    
    public function updateLoginTime(){
        $query = "update " . Users::TABLE . " set " . Users::COL_LAST_LOGIN . "=now() where " . Users::COL_EMAIL . "=?;";
        try{
            $stmt = $this->db_obj->prepare($query);
            $stmt->bindValue(1, $this->email);
            $stmt->execute();
            return true;
        }catch (PDOException $e){
            return null;
        }
    }    

    public function updateNumberOfAttempts($number_of_attempts, $lock_acct=false){
        $query = ($lock_acct)? "update " . Users::TABLE . " set " . Users::COL_LOGIN_ATTEMPT_NUMBER . "=?, " . Users::COL_IS_LOCKED_OUT . "= true " .
             "where " . Users::COL_EMAIL . "=?;" : "update " . Users::TABLE . " set " . Users::COL_LOGIN_ATTEMPT_NUMBER . "=?, " .
            Users::COL_IS_LOCKED_OUT . "= false " . "where " . Users::COL_EMAIL . "=?;";
        try{
            $stmt = $this->db_obj->prepare($query);
            $stmt->bindValue(1, $number_of_attempts);
            $stmt->bindValue(2, $this->email);
            $stmt->execute();
            $request = $stmt->rowCount();
            return $request;
        }catch (PDOException $e){
            return null;
        }
    }

    public function validate($email){
        $this->email = $email;
        $query = "select " . Users::COL_USER_ID . ", " . Users::COL_EMAIL . ", " . Users::COL_PASSWORD . ", " . Users::COL_DOB . ", " .
                  Users::COL_LOGIN_ATTEMPT_NUMBER . ", " . Users::COL_USER_NAME . ", " . Users::COL_LAST_LOGIN . ", " . Users::COL_IS_LOCKED_OUT . ", " .
                  Users::COL_IS_ACTIVE . ", " . Users::COL_ROLE_ID . " from " . Users::TABLE . " where " . Users::COL_EMAIL . "=?;";
        try{
            $stmt = $this->db_obj->prepare($query);
            $stmt->bindValue(1, $this->email);
            $stmt->execute();
            $request = $stmt->fetch(PDO::FETCH_ASSOC);
            return $request;
        }catch (PDOException $e){
            return null;
        }
    }
} 