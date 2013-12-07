<?php
class CategoriesView extends View {
    private $registry;
    private $model;
    private $err_msg;
    private $content;
    private $user_id;
    private $db_video;
    private $video_array;
    private $loggedin_data_obj;

    public function __construct(RegistryInterface $registry){
        $this->registry = $registry;
        $this->loggedin_data_obj = $this->registry->get("logged_data");
        $this->db_categories = $this->registry->get('db_categories');
        $this->db_video = $this->registry->get(Definitions::DB_VIDEOS);
        $this->content = "";
        $this->err_msg = "";
    }
    
    private function _getContent(){
        $content = '';
        $is_closed = true;
        if (count($this->video_array) > 0){
            $counter = 0;
            foreach($this->video_array as $key=>$value){
               ++$counter;
               if ($counter % 4 == 1){//open div
                $is_closed = false;
                $content .= '<div id="home_video_links">';
               }
            $content .= '<div class="each_video_link">
              <p class="video_links"><a href="' . DOMAIN . 'watch/' . $value[Videos::COL_VIDEO_ID] . '"><img alt=""
                src="' . THUMBNAILS . $value[Videos::COL_THUMB_NAIL] . '" /></a></p>
              <p class="video_links_title"><strong>' . ucwords($value[Videos::COL_TITLE]) . '</strong></p>
              <p class="video_links_author">' . ucwords($value[Videos::COL_PRESENTER_INFO]) . '</p>
              <p class="video_links_uploaded_on"><strong>Uploaded on</strong> ' . DateTime::createFromFormat('Y-m-d H:i:s', $value[Videos::COL_DATE_UPLOADED])->format('F jS, Y') . '</p>
            </div>';               
               
               if ($counter % 4 == 0){//close div
                $is_closed = true;
                $content .= '</div><p style="clear: both;">&nbsp;</p>';
               }               
            }
            
            if (!$is_closed)
              $content .= '</div><p style="clear: both;">&nbsp;</p>';
        }else{
            $content = '<p id="no_video_p">No Video under this category</p>';
        }
        return $content;
    }

    protected function displayContent(){
        echo $this->template;
    }

    protected function getTemplate(){
        $this->user_id = $this->loggedin_data_obj->getData(Definitions::USER_ID);
        $this->template = (file_exists(TEMPLATES . "loggedin.html"))? file_get_contents(TEMPLATES . "loggedin.html") : "";
    }

    public function setupContent($action=''){
        //get the template, compose the message, put message in template, display the content
        $action = urldecode($action);
        $this->video_array = $this->db_video->selectVideoByCategoryName(strtolower($action));
        $this->getTemplate();
        if (null === $this->user_id)
            $this->setOuterNavigation('categories');
        else{
            $role_id = $this->loggedin_data_obj->getData(Definitions::ROLE_ID);
            $this->setInnerNavigation($role_id, 'categories');
        }
        $this->setFooterNavigation();
        $css='<link rel="stylesheet" href="/css/categories.css" type="text/css" />';
        $this->setHeader("EduVideo.com | Categories", $meta="", $css=$css, $js="");
        $content ='
        <div id="homepage">
          <h1>' . ucwords($action) . ' Videos</h1>
          ' . $this->_getContent() . '
        </div>
                  ';
        $this->template = str_replace("{{content}}", $content, $this->template);
        $this->displayContent();
    }
} 