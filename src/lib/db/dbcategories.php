<?php
class DbCategories extends Db {
    private $registry;

    public function __construct(array $db_settings, RegistryInterface $registry){
        $db_setting = array('server'=>$db_settings['server'], 'database'=>$db_settings['db'], 'username'=>$db_settings['db_cred']['user']['user'],
                            'password'=>$db_settings['db_cred']['user']['password']); //I know; the credentials seem too long
        parent::__construct($db_setting);
        $this->registry = $registry;
    }
    
    public function checkDuplicateName($name){
        $query = 'select ' . VideoCategories::COL_NAME . ' from ' . VideoCategories::TABLE . ' where ' .
                  VideoCategories::COL_NAME . '=?;';
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
    
    public function deleteCategory($category_id){
        $query = 'delete from ' . VideoCategories::TABLE . ' where ' . VideoCategories::COL_VIDEO_CAT_ID . '=?;';
        try{
            $stmt = $this->db_obj->prepare($query);
            $stmt->bindValue(1, $category_id);
            $stmt->execute();
            return true;
        }catch (PDOException $e){
            return null;
        }        
    }
    
    public function insertCategory($category){
        $query = 'insert into ' . VideoCategories::TABLE . ' values(null, ?);';
        try{
            $stmt = $this->db_obj->prepare($query);
            $stmt->bindValue(1, $category);
            $stmt->execute();
            return true;
        }catch (PDOException $e){
            return null;
        }
    }
    
    public function selectCategories($id=""){
        try{
            if ($id == ''){
                $query = 'select ' . VideoCategories::COL_VIDEO_CAT_ID . ', ' . VideoCategories::COL_NAME . ' from ' .
                  VideoCategories::TABLE . ' order by ' . VideoCategories::COL_VIDEO_CAT_ID . ';';
                $stmt = $this->db_obj->prepare($query);
            }else{
                $query = 'select ' . VideoCategories::COL_VIDEO_CAT_ID . ', ' . VideoCategories::COL_NAME . ' from ' .
                  VideoCategories::TABLE . ' where ' . VideoCategories::COL_VIDEO_CAT_ID . '=?;';
                $stmt = $this->db_obj->prepare($query);
                $stmt->bindValue(1, $id);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch (PDOException $e){
            return null;
        }

    }
    
    public function updateCategory($name, $category_id){
        $query = 'update ' . VideoCategories::TABLE . ' set ' . VideoCategories::COL_NAME . '=? where ' .
                 VideoCategories::COL_VIDEO_CAT_ID . '=?;';
        try{
            $stmt = $this->db_obj->prepare($query);
            $stmt->bindValue(1, $name);
            $stmt->bindValue(2, $category_id);
            $stmt->execute();
            return true;
        }catch (PDOException $e){
            return null;
        }
    }    
} 