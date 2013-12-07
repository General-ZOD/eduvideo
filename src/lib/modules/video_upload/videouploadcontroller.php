<?php
class VideoUploadController {
    private $upload_model;
    private $loggedin_data;
    private $conversion_model;
    private $upload_view;
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
        $this->upload_view = $this->registry->get('video_upload_view');
    }

    public function process(array $POST){
        $this->upload_model = $this->registry->get('video_upload_model');
        if (isset($POST[Definitions::UPLOAD_VIDEO_BTN])){
            $result = $this->upload_model->checkVideoData($POST);
            switch($result){
                case VideoUploadModel::INVALID_CATEGORY : echo json_encode(['code'=>'2', 'message'=>'Invalid Category Selection']); break;
                case VideoUploadModel::NO_TITLE : echo json_encode(['code'=>'3', 'message'=>'Video Title is Required']); break;
                case VideoUploadModel::INVALID_VIDEO_TYPE : echo json_encode(['code'=>'4', 'message'=>'Only Videos are accepted']); break;
                case VideoUploadModel::SUCCESS : echo json_encode(['code'=>'0', 'message'=>'Video Upload in Progress. Please wait....']); break;
                default: echo json_encode(['code'=>'1', 'message'=>'System Error. Please, try again']);
            }exit;
        }else if (isset($POST['uploaded_complete'])){
            $result = $this->upload_model->encodeVideo();
            echo ($result === VideoUploadModel::SUCCESS)? json_encode(['code'=>'0', 'message'=>'Upload successful']) : json_encode(['code'=>'1', 'message'=>'System Error. Please, try again later']);
        }else if ($this->upload_model->checkIfVideoNameExist()){
            $result = $this->upload_model->mergeVideoPackets();
            echo ($result)? json_encode(['code'=>'0', 'message'=>'']) : json_encode(['code'=>'1', 'message'=>'System Error. Please, try again later']);
        }else if (isset($POST['delete_upload_video_btn'])){
            $result = $this->upload_model->deleteVideo($POST);
            $this->upload_view->setupContent('delete', $result);
        }else if (isset($POST['update_upload_video_btn'])){
            $result = $this->upload_model->updateVideoData($POST);
            $this->upload_view->setupContent('update', $result);
        }else if (isset($POST['upload_video_details_btn'])){
            $this->upload_view->setupContent('details', $POST['video_id']);
        }else
            $this->upload_view->setupContent('');
    }
} 