<?php
class DbSignup extends Db {
    private $registry;

    public function __construct(array $db_settings, RegistryInterface $registry){
        $db_setting = array("server"=>$db_settings["server"], "database"=>$db_settings["db"], "username"=>$db_settings["db_cred"]["user"]["user"],
                            "password"=>$db_settings["db_cred"]["user"]["password"]); //I know; the credentials seem too long
        parent::__construct($db_setting);
        $this->registry = $registry;
    }

    public function checkEmail($email){
        $query = 'select ' . Users::COL_USER_ID .  ' from ' . Users::TABLE . ' where ' . Users::COL_EMAIL . '=?;';
        try{
            $stmt = $this->db_obj->prepare($query);
            $stmt->bindValue(1, $email);
            $stmt->execute();
            $request = $stmt->fetch(PDO::FETCH_ASSOC);
            return $request;
        }catch (PDOException $e){
            return null;
        }
    }

    public function insertValidationCode(array $data){
        $query = 'insert into ' . RegistrationConfirmation::TABLE . ' (' . RegistrationConfirmation::COL_USER_ID . ', ' .
                 RegistrationConfirmation::COL_VALIDATION_CODE . ', ' . RegistrationConfirmation::COL_DATE_CREATED . ') values(:user_id, :code, now());';
        try{
            $stmt = $this->db_obj->prepare($query);
            $stmt->bindValue(':user_id', $data["user_id"]);
            $stmt->bindValue(':code', $data["code"]);
            $stmt->execute();
            return $stmt->rowCount();
        }catch (PDOException $e){
            return null;
        }
    }

    public function registerUser(array $data){
        $query = 'insert into ' . Users::TABLE . ' (' . Users::COL_EMAIL . ', ' . Users::COL_PASSWORD . ', ' . Users::COL_DOB . ', ' .
            Users::COL_DATE_REGISTERED . ', ' . Users::COL_IS_ACTIVE . ', ' . Users::COL_LAST_LOGIN . ', ' . Users::COL_LOGIN_ATTEMPT_NUMBER .
            ', ' . Users::COL_IS_LOCKED_OUT . ', ' . Users::COL_USER_NAME . ', ' . Users::COL_ROLE_ID . ') values(:email, :password,
                  :dob, now(), false, now(), 0, false, :username, 3);';
        try{
            $stmt = $this->db_obj->prepare($query);
            $stmt->bindValue(':email', $data["email"]);
            $stmt->bindValue(':password', $data["password"]);
            $stmt->bindValue(':dob', $data["dob"]);
            $stmt->bindValue(':username', '');
            $stmt->execute();
            return $this->db_obj->lastInsertId();
        }catch (PDOException $e){
            return null;
        }
    }
} 