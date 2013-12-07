<?php
class AdminQuestionsModel {
    private $db_questions_obj;
    private $input_data;
    const SUCCESS = 0;
    const ERROR = 1;
    const EMPTY_QUESTION = 2;
    const DUPLICATE_QUESTION = 3;

    public function __construct(RegistryInterface $registry){
        $this->db_questions_obj = $registry->get('db_questions');
        $this->input_data = [];
    }
    
    public function getInputData(){
        return $this->input_data;
    }
    
    public function deleteQuestion(array $data){
        $this->input_data = $data;
        $id = $this->input_data['updated_question_id'];
        if ($id == '')
          return self::EMPTY_QUESTION;
        else{
            $result = $this->db_questions_obj->deleteQuestion($id);
            return ($result === true)? self::SUCCESS : self::ERROR;
        }
    }     
    
    public function insertQuestion(array $data){
        $this->input_data = $data;
        $name = strtolower(trim($this->input_data['security_question']));
        if ($name == '')
          return self::EMPTY_QUESTION;
        $result = $this->db_questions_obj->checkDuplicateName($name);
        if ($result === null)
          return self::ERROR;
        else if (count($result) > 0)
          return self::DUPLICATE_QUESTION;
        else{
            $result = $this->db_questions_obj->insertQuestion($name);
            return ($result === true)? self::SUCCESS : self::ERROR;
        }
    }
    
    public function updateQuestion(array $data){
        $this->input_data = $data;
        $name = strtolower(trim($this->input_data['updated_question_name']));
        $id = $this->input_data['updated_question_id'];
        if ($name == '')
          return self::EMPTY_QUESTION;
        $result = $this->db_questions_obj->checkDuplicateName($name);
        if ($result === null)
          return self::ERROR;
        else if (count($result) > 0)
          return self::DUPLICATE_QUESTION;
        else{
            $result = $this->db_questions_obj->updateQuestions($name, $id);
            return ($result === true)? self::SUCCESS : self::ERROR;
        }
    }    
} 