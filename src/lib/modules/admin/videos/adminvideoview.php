<?php
class AdminVideoView extends View {
    private $registry;
    private $model;
    private $err_msg;
    private $content;
    private $user_id;
    private $loggedin_data_obj;
    private $model_data;
    private $profile_model;
    private $db_videos_obj;
    private $video_id;
    private $video_array;

    public function __construct(RegistryInterface $registry){
        $this->registry = $registry;
        $this->loggedin_data_obj = $this->registry->get('logged_data');
        $this->db_categories = $this->registry->get('db_categories');
        $this->db_videos_obj = $this->registry->get(Definitions::DB_VIDEOS_UPLOAD);
        $this->content = "";
        $this->err_msg = "";
        $this->video_array = [];
    }
    
    private function _getCategories($input_value=''){
        $content = '<select name="video_categories" id="video_categories">
                     <option value="">Select Category</option>' . PHP_EOL;
        if (count($this->categories_array) > 0){
            foreach($this->categories_array as $key=>$value){
                $content .= '<option value="' . $value[VideoCategories::COL_VIDEO_CAT_ID] . '"';
                $content .= ($value[VideoCategories::COL_VIDEO_CAT_ID] == $input_value)? ' selected="selected"': '';
                $content .= '>' . ucwords($value[VideoCategories::COL_NAME]) . '</option>' . PHP_EOL;
            }
        }
        $content .= '</select>';
        return $content;
    }
    
    private function _getCategoryName($input_value=''){
        $content='';
        foreach($this->categories_array as $key=>$value){
            if ($value[VideoCategories::COL_VIDEO_CAT_ID] == $input_value)
               return ucwords($value[VideoCategories::COL_NAME]);
        }
        return $content;
    }    
    
    private function _getTabs(){
        $header = '  
                  <h2 class="active">Manage Uploaded Videos</h2>           
            ';
        $manage_div = '<div id="manage_uploaded_video">' . $this->err_msg;           
        
        $content = '
          <div id="upload_videos_div">
            <h1>Admin Management - Videos</h1>
            <div id="upload_videos_header">
            ' . $header . '
            </div>' . $manage_div . '
               <table cellspacing="0" cellpadding="0" id="manage_uploaded_video_tbl">
                <tr><th id="manage_upload_movies_top_th" class="top_th" colspan="2">All Uploaded Videos</th></tr>';
        $content .= $this->_getVideos();
        $content .= '<tr><th id="manage_upload_movies_bottom_th" class="bottom_th" colspan="2">&nbsp;</th></tr>
               </table>              
            </div>
          </div>
        ';
        return $content;
    }
    
    private function _getUserName($user_id){
        $user_array = $this->profile_model->selectUserName($user_id);
        return ($user_array[Users::COL_USER_NAME] == '')? $user_array[Users::COL_EMAIL] : $user_array[Users::COL_USER_NAME];
    }    
    
    private function _getVideos(){
        if (count($this->video_array) == 0)
            $this->video_array = $this->db_videos_obj->selectVideo('', $this->video_id);
        
        if (count($this->video_array) > 0){
            $content = '';
            $counter = 0;
            foreach($this->video_array as $key=>$value){
                $id = $value[Videos::COL_VIDEO_ID];
                $title = $value[Videos::COL_TITLE];
                $category = $this->_getCategoryName($value[Videos::COL_VIDEO_CAT_ID]);
                $thmb_nail = $value[Videos::COL_THUMB_NAIL];
                $date = DateTime::createFromFormat('Y-m-d H:i:s' ,$value[Videos::COL_DATE_UPLOADED]);
                $date_uploaded = $date->format('F jS, Y');
                
                $tr = ($counter %2 == 0)? '<tr class="even">' : '<tr>';
                
                $content .= '<form method="post">' . $tr . '
                              <td valign="top" width="70%">
                               <h3>' . $title . '</h3>
                               <p>Uploaded on <small>' . $date_uploaded . '</small></p>
                               <p><strong>Category:</strong> ' . $category . '</p>
                               <p class="upload_details_p"> <input type="hidden" name="video_id" value="' . $id . '" /> 
                                <button type="submit" name="video_details_btn">Details</button>
                               <button type="submit" name="delete_upload_video_btn">Delete</button></p>
                              </td>
                              <td width="30%"><a href="' . DOMAIN . 'watch/' . $id . '" target="_blank"><img alt="" src="' . THUMBNAILS . $thmb_nail . '" /></a></td>
                             </tr></form>';
                ++$counter;             
            }
            return $content;
        }else{
            return '<tr><td colspan="2">No Videos Available</td></tr>';
        }
    }
    
    private function _setDeleteContent($code){
        switch($code){
            case VideoUploadModel::SUCCESS : $this->err_msg = '<p id="upload_success">Video has been deleted</p>'; break;
            default: $this->err_msg = '<p id="upload_error">System Error. Please, try again later</p>';
        }
    }
    
    private function _setDetailsContent($video_id){
        $this->profile_model = $this->registry->get('db_profile');
        $this->video_id = $video_id;
        $video_array = [];
        if (count($this->video_array) == 0)
            $this->video_array = $this->db_videos_obj->selectVideo('', '');
        if (count($this->video_array) > 0){
            foreach ($this->video_array as $key=>$value){
                if ($value[Videos::COL_VIDEO_ID] == $this->video_id){
                  $video_array = $value;
                  break;
                }
            }
        }
        
        $id = $value[Videos::COL_VIDEO_ID];
        $title = $value[Videos::COL_TITLE];
        $category = $this->_getCategoryName($value[Videos::COL_VIDEO_CAT_ID]);
        $thmb_nail = $value[Videos::COL_THUMB_NAIL];
        $author = $value[Videos::COL_PRESENTER_INFO];
        $tag = $value[Videos::COL_TAGS];
        $desc = $value[Videos::COL_DESCRIPTION];
        $date = DateTime::createFromFormat('Y-m-d H:i:s' ,$value[Videos::COL_DATE_UPLOADED]);
        $date_uploaded = $date->format('F jS, Y');
        
        $this->err_msg ='<form method="post">     
                        <table cellspacing="0" cellpadding="0" id="details_uploaded_video_tbl">
                           <tr><th colspan="2" class="top_th">Detailed Video Information</th></tr>';
        $this->err_msg .= '<tr><td><strong>Title:</strong></td><td>' . $title . '</td></tr>
                           <tr class="even"><td><strong>Category:</strong></td><td>' . $category . '</td></tr>
                           <tr><td><strong>Uploaded on:</strong></td><td>' . $date_uploaded . '</td></tr>
                           <tr class="even"><td><strong>Uploaded by:</strong></td><td>'  .
                             $this->_getUserName($value[Videos::COL_UPLOADED_BY]) . '</td></tr>
                           <tr><td>&nbsp;</td><td><a href="' . DOMAIN . 'watch/' . $id . '" target="_blank">
                              <img alt="" src="' . THUMBNAILS . $thmb_nail . '" /></a></td></tr>
                           <tr class="even"><td><strong>Author:</strong></td><td>' . $author .  '</td></tr>
                           <tr><td><strong>Tags:</strong></td><td>' . $tag .  '</td></tr>
                           <tr class="even"><td><strong>Description:</strong></td><td>' . $desc . '</td></tr>
                           <tr><td colspan="2" align="center"><input type="hidden" name="video_id" value="' . $id . '" />
                               <button type="submit" name="delete_upload_video_btn">Delete</button>
                           </tr>    
                          ';                   
        $this->err_msg .= '<tr><th class="bottom_th" colspan="2">&nbsp;</th></tr>
                        </table>
                        </form>';
    }    

    protected function displayContent(){
        echo $this->template;
    }

    protected function getTemplate(){
        $this->user_id = $this->loggedin_data_obj->getData(Definitions::USER_ID);
        $this->template = (file_exists(TEMPLATES . "loggedin.html"))? file_get_contents(TEMPLATES . "loggedin.html") : "";
    }

    public function setupContent($action='', $code=null){
        //get the template, compose the message, put message in template, display the   
        $role_id = $this->loggedin_data_obj->getData(Definitions::ROLE_ID);
        $this->getTemplate();
        $this->setInnerNavigation($role_id, 'admin');
        $this->setFooterNavigation();
        $css='<link rel="stylesheet" href="/css/admin_videos.css" type="text/css" />';
        $js = '';
        $this->setHeader($title="EduVideo.com | Video Management by Admin", $meta="", $css=$css, $js=$js);
        switch($action){
            case 'delete': $this->_setDeleteContent($code); $content = $this->_getTabs(); break;
            case 'details': $this->_setDetailsContent($code); $content = $this->_getTabs(); break;    
            default: $content = $this->_getTabs();
        }        

        $this->template = str_replace("{{content}}", $content, $this->template);
        $this->displayContent();       
    }    
} 