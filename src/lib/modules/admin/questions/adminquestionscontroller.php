<?php
class AdminQuestionsController {
    private $questions_model;
    private $loggedin_data;
    private $questions_view;
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
            $this->loggedin_data->setData(Definitions::DESTINATION);
            header('location: /login');
            exit;
        }
        $this->questions_view = $this->registry->get('admin_questions_view');
    }

    public function process(array $POST){
        if (isset($POST['create'])){
            $this->questions_model = $this->registry->get('admin_questions_model');
            $result = $this->questions_model->insertQuestion($POST);
            $this->questions_view->setupContent('insert', $result);
        }else if (isset($POST["manage_questions_btn"])){
            $this->questions_model = $this->registry->get('admin_questions_model');
            $result = $this->questions_model->updateQuestion($POST);
            $this->questions_view->setupContent('update', $result);            
        }else if (isset($POST['delete_questions_btn'])){
            $this->questions_model = $this->registry->get('admin_questions_model');
            $result = $this->questions_model->deleteQuestion($POST);
            $this->questions_view->setupContent('delete', $result);            
        }else
            $this->questions_view->setupContent('');
    }
} 