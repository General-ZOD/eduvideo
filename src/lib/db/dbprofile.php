<?php
class DbProfile extends Db {
    private $registry;

    public function __construct(array $db_settings, RegistryInterface $registry){
        $db_setting = array('server'=>$db_settings['server'], 'database'=>$db_settings['db'],
                            'username'=>$db_settings['db_cred']['user']['user'],
                            'password'=>$db_settings['db_cred']['user']['password']); //I know; the credentials seem too long
        parent::__construct($db_setting);
        $this->registry = $registry;
    }
    
    public function selectAllUsers(){
        try{
            $query = 'select ' . Users::COL_USER_ID . ', ' . Users::COL_EMAIL . ', ' . Users::COL_DOB . ', ' .
               Users::COL_DATE_REGISTERED . ', ' . Users::COL_IS_ACTIVE . ', ' . Users::COL_LAST_LOGIN . ', ' .
               Users::COL_LOGIN_ATTEMPT_NUMBER . ', ' . Users::COL_IS_LOCKED_OUT . ', ' . Users::COL_USER_NAME . ', ' .
               Users::COL_ROLE_ID . ' from ' . Users::TABLE . ';';
            $stmt = $this->db_obj->prepare($query);                     
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch (PDOException $e){
            return null;
        }
    }
    
    public function selectRoles(){
        try{
            $query = 'select ' . Roles::COL_ROLE_ID . ', ' . Roles::COL_ROLE_NAME . ' from ' . Roles::TABLE . ';';
            $stmt = $this->db_obj->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch (PDOException $e){
            return null;
        }
    }     
    
    public function selectUserById($user_id){
        try{
            $query = 'select ' . Users::COL_USER_ID . ', ' . Users::COL_EMAIL . ', ' . Users::COL_DOB . ', ' .
               Users::COL_DATE_REGISTERED . ', ' . Users::COL_IS_ACTIVE . ', ' . Users::COL_LAST_LOGIN . ', ' .
               Users::COL_LOGIN_ATTEMPT_NUMBER . ', ' . Users::COL_IS_LOCKED_OUT . ', ' . Users::COL_USER_NAME . ', ' .
               Users::COL_ROLE_ID . ' from ' . Users::TABLE . ' where ' . Users::COL_USER_ID . '=?;';
            $stmt = $this->db_obj->prepare($query);
            $stmt->bindValue(1, $user_id);                     
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }catch (PDOException $e){
            return null;
        }
    }    
    
    public function selectUserName($user_id){
        try{
            $query = 'select ' . Users::COL_EMAIL . ', ' . Users::COL_USER_NAME . ' from ' . Users::TABLE . ' where ' .
            Users::COL_USER_ID . '=?;';
            $stmt = $this->db_obj->prepare($query);
            $stmt->bindValue(1, $user_id);                     
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }catch (PDOException $e){
            return null;
        }
    }    
} 