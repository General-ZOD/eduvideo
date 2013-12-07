<?php
class AdminCategoriesController {
    private $categories_model;
    private $loggedin_data;
    private $categories_view;
    private $registry;
    private $path;

    //$path is of array('', 'admin', 'module', ...)
    public function __construct($path, RegistryInterface $registry){
        $this->registry = $registry;
        $this->path = $path;
        $this->loggedin_data = $this->registry->get('logged_data');
        $user_id = $this->loggedin_data->getData(Definitions::USER_ID);
        if (null === $user_id){//store destination and redirect to login 
            $destination = implode("/", $path);
            $this->loggedin_data->setData(Definitions::DESTINATION);
            header("location: /login");
            exit;
        }
        $this->categories_view = $this->registry->get('admin_categories_view');
    }

    public function process(array $POST){
        if (isset($POST["create"])){
            $this->categories_model = $this->registry->get('admin_categories_model');
            $result = $this->categories_model->insertCategory($POST);
            $this->categories_view->setupContent('insert', $result);
        }else if (isset($POST["manage_categories_btn"])){
            $this->categories_model = $this->registry->get('admin_categories_model');
            $result = $this->categories_model->updateCategory($POST);
            $this->categories_view->setupContent('update', $result);            
        }else if (isset($POST['delete_categories_btn'])){
            $this->categories_model = $this->registry->get('admin_categories_model');
            $result = $this->categories_model->deleteCategory($POST);
            $this->categories_view->setupContent('delete', $result);            
        }else
            $this->categories_view->setupContent('');
    }
} 