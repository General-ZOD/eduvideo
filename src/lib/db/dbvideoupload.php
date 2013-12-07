<?php
class DbVideoUpload extends Db {
    private $registry;

    public function __construct(array $db_settings, RegistryInterface $registry){
        $db_setting = array('server'=>$db_settings['server'], 'database'=>$db_settings['db'], 'username'=>$db_settings['db_cred']['user']['user'],
                            'password'=>$db_settings['db_cred']['user']['password']); //I know; the credentials seem too long
        parent::__construct($db_setting);
        $this->registry = $registry;
    }
 
    public function checkVideoCategory($cat_id){
        $query = 'select ' . VideoCategories::COL_NAME . ' from ' . VideoCategories::TABLE . ' where ' .
                 VideoCategories::COL_VIDEO_CAT_ID . '=?;';
        try{
            $stmt = $this->db_obj->prepare($query);
            $stmt->bindValue(1, $cat_id);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }catch (PDOException $e){
            return null;
        }        
    } 
    
    public function deleteVideo($video_id){
        $query = 'delete from ' . Videos::TABLE . ' where ' . Videos::COL_VIDEO_ID . '=?;';
        try{
            $stmt = $this->db_obj->prepare($query);
            $stmt->bindValue(1, $video_id);
            $stmt->execute();
            return true;
        }catch (PDOException $e){
            return null;
        }        
    }
    
    public function insertVideo($cat_id, $uploader, $file, $title, $desc, $presenter, $tags, $thumb){
        $query = 'insert into ' . Videos::TABLE . '(' . Videos::COL_VIDEO_CAT_ID . ', ' . Videos::COL_UPLOADED_BY .', ' .
                Videos::COL_FILE_LOCATION . ', ' . Videos::COL_TITLE . ', ' . Videos::COL_DESCRIPTION . ', ' .
                Videos::COL_PRESENTER_INFO . ', ' . Videos::COL_TAGS . ', ' . Videos::COL_THUMB_NAIL . ', ' .
                Videos::COL_DATE_UPLOADED . ') values(:cat_id, :uploader, :file, :title, :desc, :presenter, :tags, :thumb, now());';
        try{
            $stmt = $this->db_obj->prepare($query);
            $stmt->bindValue(':cat_id', $cat_id);
            $stmt->bindValue(':uploader', $uploader);
            $stmt->bindValue(':file', $file);
            $stmt->bindValue(':title', $title);
            $stmt->bindValue(':desc', $desc);
            $stmt->bindValue(':presenter', $presenter);
            $stmt->bindValue(':tags', $tags);
            $stmt->bindValue(':thumb', $thumb);            
            $stmt->execute();
            return true;
        }catch (PDOException $e){
            return null;
        }
    }
    
    public function selectVideo($user_id='', $video_id=''){
        try{
            if ($video_id == ''){
                if ($user_id == ''){
                    $query = 'select ' . Videos::COL_VIDEO_ID . ', ' . Videos::COL_VIDEO_CAT_ID . ', ' . Videos::COL_UPLOADED_BY .
                             ', ' . Videos::COL_FILE_LOCATION . ', ' . Videos::COL_TITLE . ', ' . Videos::COL_DESCRIPTION . ', ' .
                    Videos::COL_PRESENTER_INFO . ', ' . Videos::COL_TAGS . ', ' . Videos::COL_THUMB_NAIL . ', ' .
                    Videos::COL_DATE_UPLOADED . ' from ' . Videos::TABLE . ' order by ' . Videos::COL_VIDEO_CAT_ID;
                    $stmt = $this->db_obj->prepare($query);                    
                }else{
                    $query = 'select ' . Videos::COL_VIDEO_ID . ', ' . Videos::COL_VIDEO_CAT_ID . ', ' . Videos::COL_UPLOADED_BY .
                             ', ' . Videos::COL_FILE_LOCATION . ', ' . Videos::COL_TITLE . ', ' . Videos::COL_DESCRIPTION . ', ' .
                    Videos::COL_PRESENTER_INFO . ', ' . Videos::COL_TAGS . ', ' . Videos::COL_THUMB_NAIL . ', ' .
                    Videos::COL_DATE_UPLOADED . ' from ' . Videos::TABLE . ' where ' . Videos::COL_UPLOADED_BY .
                    '=? order by ' . Videos::COL_VIDEO_CAT_ID;
                    $stmt = $this->db_obj->prepare($query);
                    $stmt->bindValue(1, $user_id);                     
                }

            }else{
                $query = 'select ' . Videos::COL_VIDEO_ID . ', ' . Videos::COL_VIDEO_CAT_ID . ', ' . Videos::COL_UPLOADED_BY .', ' .
                Videos::COL_FILE_LOCATION . ', ' . Videos::COL_TITLE . ', ' . Videos::COL_DESCRIPTION . ', ' .
                Videos::COL_PRESENTER_INFO . ', ' . Videos::COL_TAGS . ', ' . Videos::COL_THUMB_NAIL . ', ' .
                Videos::COL_DATE_UPLOADED . ' from ' . Videos::TABLE . ' where ' . Videos::COL_VIDEO_ID . '=?;';
                $stmt = $this->db_obj->prepare($query);
                $stmt->bindValue(1, $video_id);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch (PDOException $e){
            return null;
        }

    }    
    
    public function updateVideo($cat_id, $title, $desc, $presenter, $tags, $video_id){
        $query = 'update ' . Videos::TABLE . ' set ' . Videos::COL_VIDEO_CAT_ID . '= :cat_id, ' . Videos::COL_TITLE . '= :title,'
                . Videos::COL_DESCRIPTION . '= :desc, ' . Videos::COL_PRESENTER_INFO . '= :presenter, ' . Videos::COL_TAGS
                . '= :tag where ' . Videos::COL_VIDEO_ID . '= :video_id;';
        try{
            $stmt = $this->db_obj->prepare($query);
            $stmt->bindValue(':cat_id', $cat_id);
            $stmt->bindValue(':title', $title);
            $stmt->bindValue(':desc', $desc);
            $stmt->bindValue(':presenter', $presenter);
            $stmt->bindValue(':tag', $tags);
            $stmt->bindValue(':video_id', $video_id);
            $stmt->execute();
            return true;
        }catch (PDOException $e){
            return null;
        }
    }    
} 