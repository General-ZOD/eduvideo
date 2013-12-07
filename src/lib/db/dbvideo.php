<?php
class DbVideo extends Db {
    private $registry;

    public function __construct(array $db_settings, RegistryInterface $registry){
        $db_setting = array('server'=>$db_settings['server'], 'database'=>$db_settings['db'], 'username'=>$db_settings['db_cred']['user']['user'],
                            'password'=>$db_settings['db_cred']['user']['password']); //I know; the credentials seem too long
        parent::__construct($db_setting);
        $this->registry = $registry;
    }
    
    public function selectLatestVideos($offset=0, $limit=5){
        try{
            $query = 'select ' . Videos::COL_VIDEO_ID . ', ' . Videos::COL_VIDEO_CAT_ID . ', ' . Videos::COL_UPLOADED_BY . ', '
               . Videos::COL_FILE_LOCATION . ', ' . Videos::COL_TITLE . ', ' . Videos::COL_DESCRIPTION . ', ' .
               Videos::COL_PRESENTER_INFO . ', ' . Videos::COL_TAGS . ', ' . Videos::COL_THUMB_NAIL . ', ' .
               Videos::COL_DATE_UPLOADED . ' from ' . Videos::TABLE . ' order by ' . Videos::COL_DATE_UPLOADED . ' desc
               limit ' . $offset . ', ' . $limit . ';';
            $stmt = $this->db_obj->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch (PDOException $e){
            return null;
        }
    }    
    
    public function selectVideoByCategoryName($category_name){
        try{
            $query = 'select ' . Videos::COL_VIDEO_ID . ', b.' . Videos::COL_VIDEO_CAT_ID . ' ' . Videos::COL_VIDEO_CAT_ID . ', '
                     . Videos::COL_UPLOADED_BY . ', ' . Videos::COL_FILE_LOCATION . ', ' . Videos::COL_TITLE . ', ' .
                     Videos::COL_DESCRIPTION . ', ' . Videos::COL_PRESENTER_INFO . ', ' . Videos::COL_TAGS . ', ' .
                     Videos::COL_THUMB_NAIL . ', ' . Videos::COL_DATE_UPLOADED . ' from ' . Videos::TABLE . ' a join ' .
                     VideoCategories::TABLE . ' b on a.' . Videos::COL_VIDEO_CAT_ID . '=b.' . VideoCategories::COL_VIDEO_CAT_ID .
                     ' where b.' . VideoCategories::COL_NAME . '=? order by ' . Videos::COL_DATE_UPLOADED . ' desc;';
            $stmt = $this->db_obj->prepare($query);
            $stmt->bindValue(1, $category_name);                     

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch (PDOException $e){
            return null;
        }
    }
    
    public function selectVideoId($video_id){
        try{
            $query = 'select ' . Videos::COL_VIDEO_ID . ', ' . Videos::COL_VIDEO_CAT_ID . ', ' . Videos::COL_UPLOADED_BY . ', '
                     . Videos::COL_FILE_LOCATION . ', ' . Videos::COL_TITLE . ', ' . Videos::COL_DESCRIPTION . ', '
                     . Videos::COL_PRESENTER_INFO . ', ' . Videos::COL_TAGS . ', ' . Videos::COL_THUMB_NAIL . ', '
                     . Videos::COL_DATE_UPLOADED . ' from ' . Videos::TABLE  . ' where ' . Videos::COL_VIDEO_ID . '=?;';
            $stmt = $this->db_obj->prepare($query);
            $stmt->bindValue(1, $video_id);                     
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }catch (PDOException $e){
            return null;
        }
    }
    
    public function searchVideo($search_word){
        try{
            $query = 'select ' . Videos::COL_VIDEO_ID . ', ' . Videos::COL_VIDEO_CAT_ID . ', ' . Videos::COL_UPLOADED_BY . ', '
                     . Videos::COL_FILE_LOCATION . ', ' . Videos::COL_TITLE . ', ' . Videos::COL_DESCRIPTION . ', '
                     . Videos::COL_PRESENTER_INFO . ', ' . Videos::COL_TAGS . ', ' . Videos::COL_THUMB_NAIL . ', '
                     . Videos::COL_DATE_UPLOADED . ' from ' . Videos::TABLE  . ' where match(' . Videos::COL_TITLE .
                     ', ' . Videos::COL_DESCRIPTION . ', ' . Videos::COL_PRESENTER_INFO . ', ' . Videos::COL_TAGS .
                     ') against(? in natural language mode); ';
            $stmt = $this->db_obj->prepare($query);
            $stmt->bindValue(1, $search_word);                     
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch (PDOException $e){
            return null;
        }
    }    
} 