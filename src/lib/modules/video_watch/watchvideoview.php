<?php
class WatchVideoView extends View {
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
        $content = '<div id="video_container">';
        if ($this->video_array !== false){
            $date = DateTime::createFromFormat('Y-m-d H:i:s' ,$this->video_array[Videos::COL_DATE_UPLOADED]);
            $date_uploaded = $date->format('F jS, Y');
            $content .= '<div id="video_div">
                   <div id="actual_video">
                    <script type="text/javascript">
                       if (document.getElementById("actual_video")){
                            var flashvars={};
                            var params={};
                            var attributes={};
                            flashvars.src="' . VID . $this->video_array[Videos::COL_FILE_LOCATION] . '";
                            flashvars.autoPlay = "false";
                            params.allowfullscreen="true";
                            swfobject.embedSWF("' . JS . 'StrobeMediaPlayback.swf", "actual_video", "600", "300", "8.0.0",
                              "' . JS . 'expressInstall.swf", flashvars, params, attributes);
                       }
                    </script>
                   </div> 
                 </div>
                 <p id="video_title">' . $this->video_array[Videos::COL_TITLE] . '</p>
                 <p id="video_uploaded_on"><strong>Uploaded on:</strong> ' . $date_uploaded . '</p>
                 <p id="video_author"><strong>Author:</strong> ' . ucwords($this->video_array[Videos::COL_PRESENTER_INFO]) . '</p>
                 <p id="video_tags"><strong>Tags: </strong><small>' . $this->video_array[Videos::COL_TAGS] . '</small></p>
                 <h2>Description</h2>
                 <div id="video_desc">' . $this->video_array[Videos::COL_DESCRIPTION] . '</div>   
            ';
        }else
            $content .= '<p id="no_video">Sorry! No Video</p>';
        $content .= '</div>';
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
        $this->video_array = $this->db_video->selectVideoId($action);
        $this->getTemplate();
        if (null === $this->user_id)
            $this->setOuterNavigation('watch');
        else{
            $role_id = $this->loggedin_data_obj->getData(Definitions::ROLE_ID);
            $this->setInnerNavigation($role_id, 'watch');
        }
        $this->setFooterNavigation();
        $css='<link rel="stylesheet" href="/css/video_watch.css" type="text/css" />';
        $js='<script type="text/javascript" src="' . JS . 'swfobject.js"></script>';
        $this->setHeader("EduVideo.com | Watch Video", $meta="", $css=$css, $js);
        $content ='
        <div id="watch_video">
          <h1>Now watching</h1>
          ' . $this->_getContent() . '
        </div>
                  ';
        $this->template = str_replace("{{content}}", $content, $this->template);
        $this->displayContent();
    }
} 