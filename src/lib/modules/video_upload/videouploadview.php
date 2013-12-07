<?php
class VideoUploadView extends View {
    private $registry;
    private $model;
    private $err_msg;
    private $content;
    private $user_id;
    private $loggedin_data_obj;
    private $model_data;
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
    
    private function _getTabs($make_insert_default=true){
        if ($make_insert_default){
            $header = '
                  <h2 class="active" onClick="makeActive(this, \'upload_video\');">Upload Video</h2>    
                  <h2 onClick="makeActive(this, \'manage_uploaded_video\');">Manage Uploaded Videos</h2>           
            ';
            $create_div = '<div id="upload_video">' . $this->err_msg;
            $manage_div = '<div id="manage_uploaded_video" style="display:none;">';
        }else{
            $header = '
                  <h2 onClick="makeActive(this, \'upload_video\');">Upload Video</h2>    
                  <h2 class="active" onClick="makeActive(this, \'manage_uploaded_video\');">Manage Uploaded Videos</h2>           
            ';
            $create_div = '<div id="upload_video" style="display:none;">';
            $manage_div = '<div id="manage_uploaded_video">' . $this->err_msg;           
        }
        
        $content = '
          <div id="upload_videos_div">
            <h1>Video Upload</h1>
            <div id="upload_videos_header">
            ' . $header . '
            </div>

            ' . $create_div. '
            <style text="text/css">
               #video_file{height: 0px;width: 0px; overflow:hidden;}
                #uploadfile{font-family:ABeeZee; width:150px; padding:10px; border:1px solid #bbb; text-align: center;
                   background:#ddd; cursor:pointer; border-radius:4px;}
            </style>
             <form method="post" action="" enctype="multipart/form-data" name="video_upload_form">
               <table cellspacing="0" cellpadding="0" id="upload_video_tbl">
                <tr><th id="upload_movies_top_th" class="top_th" colspan="2">&nbsp;</th></tr>
                  <tr>
                    <td><strong>Select Category:</strong></td>
                    <td>';
        $content .= $this->_getCategories($this->model_data[Definitions::VIDEO_CATEGORIES]);            
        $content .= '</td>
                  </tr>
                  <tr class="even">
                    <td><strong>Title:</strong></td>
                    <td><input type="text" name="' . Definitions::VIDEO_TITLE . '" id="' . Definitions::VIDEO_TITLE . '"
                    placeholder="Video title" value="';
        $content .= (isset($this->model_data[Definitions::VIDEO_TITLE]))? $this->model_data[Definitions::VIDEO_TITLE] : '';
        $content .= '" maxsize="200" /> </td>
                  </tr>
                  <tr>
                    <td><strong>Select Video:</strong></td>
                    <td>
                     <div id="uploadfile" title="Max file size must be less than 30MB">Browse File</div>
                      <input type="file" name="' . Definitions::VIDEO_FILE . '" id="' . Definitions::VIDEO_FILE . '"
                       />
                    </td>
                  </tr>
                  <tr class="even">
                    <td><strong>Presenter\'s Information:</strong></td>
                    <td><input type="text" name="' . Definitions::VIDEO_INFO . '" id="' . Definitions::VIDEO_INFO . '"
                      placeholder="Presenter Info" value="';
        $content .= (isset($this->model_data[Definitions::VIDEO_INFO]))? $this->model_data[Definitions::VIDEO_INFO] : '';
        $content .= '" maxsize="255" /> </td>
                  </tr>
                  <tr>
                    <td><strong>Description:</strong></td>
                    <td><textarea name="' . Definitions::VIDEO_DESC . '" id="' . Definitions::VIDEO_DESC . '"> ';
        $content .= (isset($this->model_data[Definitions::VIDEO_DESC]))? $this->model_data[Definitions::VIDEO_DESC] : '';
        $content .= '</textarea> </td>
                  </tr>
                  <tr class="even">
                    <td><strong>Tag:</strong></td>
                    <td><input type="text" name="' . Definitions::VIDEO_TAG . '" id="' . Definitions::VIDEO_TAG . '"
                     placeholder="separate each tag with a comma" value="';
        $content .= (isset($this->model_data[Definitions::VIDEO_TAG]))? $this->model_data[Definitions::VIDEO_TAG] : '';
        $content .= '" maxsize="255" /> </td>
                  </tr>                   
                  <tr>
                    <td colspan="2" align="center">
                      <button type="button" name="' . Definitions::UPLOAD_VIDEO_BTN . '" id="' . Definitions::UPLOAD_VIDEO_BTN .
                      '">Upload</button>
                    </td>
                  </tr>
                  <tr class="even">
                    <th id="upload_movies_bottom_th" class="bottom_th" colspan="2">&nbsp;</th>
                </tr>
               </table>
             </form>
            </div>
            
            ' . $manage_div . '
               <table cellspacing="0" cellpadding="0" id="manage_uploaded_video_tbl">
                <tr><th id="manage_upload_movies_top_th" class="top_th" colspan="2">My Uploaded Videos</th></tr>';
        $content .= $this->_getVideos();
        $content .= '<tr><th id="manage_upload_movies_bottom_th" class="bottom_th" colspan="2">&nbsp;</th></tr>
               </table>              
            </div>
          </div>
        ';
        return $content;
    }
    
    private function _getVideos(){
        if (count($this->video_array) == 0)
            $this->video_array = $this->db_videos_obj->selectVideo($this->user_id, $this->video_id);
        
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
                                <button type="submit" name="upload_video_details_btn">Details</button>
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
        $this->video_id = $video_id;
        $video_array = [];
        if (count($this->video_array) == 0)
            $this->video_array = $this->db_videos_obj->selectVideo($this->user_id, '');
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
        $category = $this->_getCategories($value[Videos::COL_VIDEO_CAT_ID]);
        $thmb_nail = $value[Videos::COL_THUMB_NAIL];
        $author = $value[Videos::COL_PRESENTER_INFO];
        $tag = $value[Videos::COL_TAGS];
        $desc = $value[Videos::COL_DESCRIPTION];
        $date = DateTime::createFromFormat('Y-m-d H:i:s' ,$value[Videos::COL_DATE_UPLOADED]);
        $date_uploaded = $date->format('F jS, Y');
        
        $this->err_msg ='<form method="post">     
                        <table cellspacing="0" cellpadding="0" id="details_uploaded_video_tbl">
                           <tr><th colspan="2" class="top_th">Detailed Video Information</th></tr>';
        $this->err_msg .= '<tr><td><strong>Title:</strong></td><td><input type="text" name="' . Definitions::VIDEO_TITLE . '" value="' . $title .
                               '" /></td></tr>
                           <tr class="even"><td><strong>Category:</strong></td><td>' . $category . '</td></tr>
                           <tr><td><strong>Uploaded on:</strong></td><td>' . $date_uploaded . '</td></tr>
                           <tr class="even"><td>&nbsp;</td><td><a href="' . DOMAIN . 'watch/' . $id . '" target="_blank">
                              <img alt="" src="' . THUMBNAILS . $thmb_nail . '" /></a></td></tr>
                           <tr><td><strong>Author:</strong></td><td><input type="text" name="' . Definitions::VIDEO_INFO . '" value="' . $author .  '" /></td></tr>
                           <tr class="even"><td><strong>Tags:</strong></td><td><input type="text" name="' . Definitions::VIDEO_TAG . '" id="update_upload_tag" value="' . $tag .  '" /></td></tr>
                           <tr><td><strong>Description:</strong></td><td><textarea name="' . Definitions::VIDEO_DESC . '" id="update_upload_desc">' . $desc . '</textarea></td></tr>
                           <tr class="even"><td colspan="2" align="center"><input type="hidden" name="video_id" value="' . $id . '" />
                               <button type="submit" name="update_upload_video_btn">Update</button>
                               <button type="submit" name="delete_upload_video_btn">Delete</button>
                           </tr>    
                          ';                   
        $this->err_msg .= '<tr><th class="bottom_th" colspan="2">&nbsp;</th></tr>
                        </table>
                        </form>';
    }
    
    private function _setUpdateContent($code){
        $err_msg;
        $this->model = $this->registry->get('video_upload_model');
        $this->video_id = $this->model->getVideoId();
        switch($code){
            case VideoUploadModel::SUCCESS: $err_msg = '<p id="upload_success">Video Information has been updated</p>'; break;
            case VideoUploadModel::INVALID_CATEGORY: $err_msg = '<p id="upload_error">Invalid Category</p>'; break;
            case VideoUploadModel::NO_TITLE: $err_msg = '<p id="upload_error">Title is Required</p>'; break;    
            default: $err_msg = '<p id="upload_error">System Error. Please, try again later</p>';
        }
        $this->_setDetailsContent($this->video_id);
        $this->err_msg = $err_msg . $this->err_msg;
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
        $this->setInnerNavigation($role_id, 'upload');
        $this->setFooterNavigation();
        $css='<link rel="stylesheet" href="/css/video_upload.css" type="text/css" />';
        $js = '<script type="text/javascript" src="/js/video_upload.js"></script>';
        $this->setHeader($title="EduVideo.com | Upload Your Video", $meta="", $css=$css, $js=$js);
        switch($action){
            case 'update': $this->_setUpdateContent($code); $content = $this->_getTabs(false); break;
            case 'delete': $this->_setDeleteContent($code); $content = $this->_getTabs(false); break;
            case 'details': $this->_setDetailsContent($code); $content = $this->_getTabs(false); break;    
            default: $content = $this->_getTabs();
        }        

        $this->template = str_replace("{{content}}", $content, $this->template);
        $this->displayContent();       
    }    
} 