<?php
class AdminVideoController {
    private $videos_model;
    private $loggedin_data;
    private $videos_view;
    private $registry;
    private $path;

    //$path is of array('', 'admin', 'module', ...)
    public function __construct($path, RegistryInterface $registry){
        $this->registry = $registry;
        $this->path = $path;
        $this->loggedin_data = $this->registry->get('logged_data');
        $user_id = $this->loggedin_data->getData(Definitions::USER_ID);
        if (null === $user_id){//store destination and redirect to login 
            $destination = implode('/', $path);
            $this->loggedin_data->setData(Definitions::DESTINATION, $destination);
            header('location: /login');
            exit;
        }
        $this->videos_view = $this->registry->get('admin_video_view');
    }

    public function process(array $POST){
        $this->videos_model = $this->registry->get('admin_video_model');
        if (isset($POST['delete_upload_video_btn'])){
            $result = $this->videos_model->deleteVideo($POST);
            $this->videos_view->setupContent('delete', $result);
        }else if (isset($POST['video_details_btn']))
            $this->videos_view->setupContent('details', $POST['video_id']);
        else
            $this->videos_view->setupContent('');
    }
} 