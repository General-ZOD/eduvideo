<?php
class DbQuestion extends Db {
    private $registry;

    public function __construct(array $db_settings, RegistryInterface $registry){
        $db_setting = array('server'=>$db_settings['server'], 'database'=>$db_settings['db'], 'username'=>$db_settings['db_cred']['user']['user'],
                            'password'=>$db_settings['db_cred']['user']['password']); //I know; the credentials seem too long
        parent::__construct($db_setting);
        $this->registry = $registry;
    }
    
    public function checkDuplicateName($name){
        $query = 'select ' . SecurityQuestions::COL_QUESTION . ' from ' . SecurityQuestions::TABLE . ' where ' .
                  SecurityQuestions::COL_QUESTION . '=?;';
        try{
            $stmt = $this->db_obj->prepare($query);
            $stmt->bindValue(1, $name);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }catch (PDOException $e){
            return null;
        }
    }
    
    public function deleteQuestion($question_id){
        $query = 'delete from ' . SecurityQuestions::TABLE . ' where ' . SecurityQuestions::COL_QUESTION_ID . '=?;';
        try{
            $stmt = $this->db_obj->prepare($query);
            $stmt->bindValue(1, $question_id);
            $stmt->execute();
            return true;
        }catch (PDOException $e){
            return null;
        }        
    }
    
    public function insertQuestion($question){
        $query = 'insert into ' . SecurityQuestions::TABLE . ' values(null, ?);';
        try{
            $stmt = $this->db_obj->prepare($query);
            $stmt->bindValue(1, $question);
            $stmt->execute();
            return true;
        }catch (PDOException $e){
            return null;
        }
    }
    
    public function selectQuestions($id=""){
        try{
            if ($id == ''){
                $query = 'select ' . SecurityQuestions::COL_QUESTION_ID . ', ' . SecurityQuestions::COL_QUESTION . ' from ' .
                  SecurityQuestions::TABLE . ' order by ' . SecurityQuestions::COL_QUESTION_ID . ';';
                $stmt = $this->db_obj->prepare($query);
            }else{
                $query = 'select ' . SecurityQuestions::COL_QUESTION_ID . ', ' . SecurityQuestions::COL_QUESTION . ' from ' .
                  SecurityQuestions::TABLE . ' where ' . SecurityQuestions::COL_QUESTION_ID . '=?;';
                $stmt = $this->db_obj->prepare($query);
                $stmt->bindValue(1, $id);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch (PDOException $e){
            return null;
        }

    }
    
    public function updateQuestions($name, $question_id){
        $query = 'update ' . SecurityQuestions::TABLE . ' set ' . SecurityQuestions::COL_QUESTION . '=? where ' .
                 SecurityQuestions::COL_QUESTION_ID . '=?;';
        try{
            $stmt = $this->db_obj->prepare($query);
            $stmt->bindValue(1, $name);
            $stmt->bindValue(2, $question_id);
            $stmt->execute();
            return true;
        }catch (PDOException $e){
            return null;
        }
    }    
} 