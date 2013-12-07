<?php
class WatchVideoModel {
    private $db_categories_obj;
    private $input_data;
    const SUCCESS = 0;
    const ERROR = 1;
    const EMPTY_CATEGORY = 2;
    const DUPLICATE_CATEGORY = 3;

    public function __construct(RegistryInterface $registry){
        $this->db_categories_obj = $registry->get('db_categories');
        $this->input_data = [];
    }
    
    public function getInputData(){
        return $this->input_data;
    }
    
    public function deleteCategory(array $data){
        $this->input_data = $data;
        $id = $this->input_data['updated_category_id'];
        if ($id == '')
          return self::EMPTY_CATEGORY;
        else{
            $result = $this->db_categories_obj->deleteCategory($id);
            return ($result === true)? self::SUCCESS : self::ERROR;
        }
    }     
    
    public function insertCategory(array $data){
        $this->input_data = $data;
        $name = strtolower(trim($this->input_data['category_name']));
        if ($name == '')
          return self::EMPTY_CATEGORY;
        $result = $this->db_categories_obj->checkDuplicateName($name);
        if ($result === null)
          return self::ERROR;
        else if (count($result) > 0)
          return self::DUPLICATE_CATEGORY;
        else{
            $result = $this->db_categories_obj->insertCategory($name);
            return ($result === true)? self::SUCCESS : self::ERROR;
        }
    }
    
    public function updateCategory(array $data){
        $this->input_data = $data;
        $name = strtolower(trim($this->input_data['updated_category_name']));
        $id = $this->input_data['updated_category_id'];
        if ($name == '')
          return self::EMPTY_CATEGORY;
        $result = $this->db_categories_obj->checkDuplicateName($name);
        if ($result === null)
          return self::ERROR;
        else if (count($result) > 0)
          return self::DUPLICATE_CATEGORY;
        else{
            $result = $this->db_categories_obj->updateCategory($name, $id);
            return ($result === true)? self::SUCCESS : self::ERROR;
        }
    }    
} 