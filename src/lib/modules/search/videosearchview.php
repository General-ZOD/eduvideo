<?php
class VideoSearchView extends View {
    private $registry;
    private $model;
    private $profile_model;
    private $err_msg;
    private $content;
    private $user_id;
    private $video_search_result;
    private $loggedin_data_obj;

    public function __construct(RegistryInterface $registry){
        $this->registry = $registry;
        $this->loggedin_data_obj = $this->registry->get("logged_data");
        $this->db_categories = $this->registry->get('db_categories');
        $this->content = "";
        $this->err_msg = "";
    }
    
    private function _getContent(){
        $content = '<div id="video_container">';
        $counter = 0;
        if (count($this->video_search_result) > 0){
            $this->profile_model = $this->registry->get('db_profile');
            $content .= '<ul>';
            foreach ($this->video_search_result as $key=>$value){
                ++$counter;
                $date = DateTime::createFromFormat('Y-m-d H:i:s', $value[Videos::COL_DATE_UPLOADED]);
                $date_uploaded = $date->format('F jS, Y');
                $content .= ($counter % 2 == 0)? '<li class="each_video_even">' : '<li class="each_video_odd">';
               $content .= '
    <div class="each_video_search">
      <div class="each_video_link"><a href="' . DOMAIN . 'watch/' . $value[Videos::COL_VIDEO_ID] . '" target="_blank">
        <img alt="" src="' . THUMBNAILS . $value[Videos::COL_THUMB_NAIL] . '" /></a></div>
      <div class="each_video_info">
       <a href="' . DOMAIN . 'watch/' . $value[Videos::COL_VIDEO_ID] . '" target="_blank">' . $value[Videos::COL_TITLE] .  '</a>
       <p><strong>by </strong> ' . $this->_getUserName($value[Videos::COL_UPLOADED_BY]) . '</p>
       <p><strong>Uploaded on</strong> ' . $date_uploaded . '</p>
       <p class="each_video_desc">' . $value[Videos::COL_DESCRIPTION] . '</p>
      </div>
    </div>
                            </li>';
            }
            $content .= '</ul>';
        }else
            $content .= '<p id="no_video">Search yielded 0 results</p>';
        $content .= '</div>';
        return $content;
    }
    
    private function _getUserName($user_id){
        $user_array = $this->profile_model->selectUserName($user_id);
        return ($user_array[Users::COL_USER_NAME] == '')? $user_array[Users::COL_EMAIL] : $user_array[Users::COL_USER_NAME];
    }

    protected function displayContent(){
        echo $this->template;
    }

    protected function getTemplate(){
        $this->user_id = $this->loggedin_data_obj->getData(Definitions::USER_ID);
        $this->template = (file_exists(TEMPLATES . "loggedin.html"))? file_get_contents(TEMPLATES . "loggedin.html") : "";
    }

    public function setupContent($action='', $code=''){
        //get the template, compose the message, put message in template, display the content
        if ($action == 'search'){
            if ($code == 0){
                $this->model = $this->registry->get('video_search_model');
                $this->video_search_result = $this->model->getSearchData();
            }else
                $this->video_search_result = [];    
        }
        $this->getTemplate();
        if (null === $this->user_id)
            $this->setOuterNavigation('watch');
        else{
            $role_id = $this->loggedin_data_obj->getData(Definitions::ROLE_ID);
            $this->setInnerNavigation($role_id, 'watch');
        }
        $this->setFooterNavigation();
        $css='<link rel="stylesheet" href="/css/video_search.css" type="text/css" />';
        $js='';
        $this->setHeader("EduVideo.com | Search Video", $meta="", $css=$css, $js);
        $content ='
        <div id="search_video">
          <h1>Search Video</h1>
          ' . $this->_getContent() . '
        </div>
                  ';
        $this->template = str_replace("{{content}}", $content, $this->template);
        $this->displayContent();
    }
} 