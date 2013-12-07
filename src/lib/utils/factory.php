<?php

class Factory {
    private static $registry;
    private static $parameters;
    private function __construct(){}

    public static function getRegistry(RegistryInterface $registry){
        self::$registry = $registry;
    }

    public static function getInstance($word, array $param=null){
        $obj=null;
        switch($word){
            case 'access': $obj = new AccessModel(self::$parameters['access'], self::$registry); break;
            case 'admin_cat_controller': $obj = new AdminCategoriesController($param, self::$registry); break;
            case 'admin_categories_model': $obj = new AdminCategoriesModel(self::$registry); break;
            case 'admin_categories_view': $obj = new AdminCategoriesView(self::$registry); break;
            case 'admin_questions_controller': $obj = new AdminQuestionsController($param, self::$registry); break;
            case 'admin_questions_model': $obj = new AdminQuestionsModel(self::$registry); break;                
            case 'admin_questions_view': $obj = new AdminQuestionsView(self::$registry); break;
            case 'admin_users_controller': $obj = new AdminUsersController($param, self::$registry); break;
            case 'admin_users_model': $obj = new AdminUsersModel(self::$registry); break;    
            case 'admin_users_view': $obj = new AdminUsersView(self::$registry); break;                  
            case 'admin_video_controller': $obj = new AdminVideoController($param, self::$registry); break;
            case 'admin_video_model': $obj = new AdminVideoModel(self::$registry); break;    
            case 'admin_video_view': $obj = new AdminVideoView(self::$registry); break;    
            case 'categories_view': $obj = new CategoriesView(self::$registry); break;    
            case 'db_categories': $obj = new DbCategories(self::$parameters['db_settings'], self::$registry); break;   
            case 'db_login': $obj = new DbLogin(self::$parameters['db_settings'], self::$registry); break;
            case 'db_profile': $obj = new DbProfile(self::$parameters['db_settings'], self::$registry); break;    
            case 'db_questions': $obj = new DbQuestion(self::$parameters['db_settings'], self::$registry); break;                 
            case 'db_signup': $obj = new DbSignup(self::$parameters['db_settings'], self::$registry); break;
            case 'db_signup_confirm': $obj = new DbSignupConfirm(self::$parameters['db_settings'], self::$registry); break;
            case Definitions::DB_USERS: $obj = new DbProfile(self::$parameters['db_settings'], self::$registry); break;     
            case Definitions::DB_VIDEOS: $obj = new DbVideo(self::$parameters['db_settings'], self::$registry); break;    
            case Definitions::DB_VIDEOS_UPLOAD : $obj = new DbVideoUpload(self::$parameters['db_settings'], self::$registry); break;    
            case 'home_view': $obj = new HomeView(self::$registry); break;
            case 'logged_data': $obj = new LoggedInDataModel('session'); break;
            case 'login_controller': $obj = new LoginController($word, self::$registry); break;
            case 'login_model': $obj =  new LoginModel(self::$parameters['login'], self::$registry); break;
            case 'login_view': $obj = new LoginView(self::$registry); break;
            case 'logout_view': $obj = new LogoutView(self::$registry); break;
            case 'profile_view': $obj = new ProfileView(self::$registry); break;
            case 'signup_mail': $obj = new SignupMail(self::$registry); break;
            case 'signup_model': $obj = new SignupModel(self::$registry); break;
            case 'signup_view': $obj = new SignupView(self::$registry); break;
            case 'signupconfirm_model': $obj =  new SignupConfirmModel(self::$registry); break;
            case 'signupconfirm_view': $obj = new SignupConfirmView(self::$registry); break;
            case 'video_conversion': $obj = new VideoConversionModel(); break;
            case 'video_search_model': $obj =  new VideoSearchModel(self::$registry); break;    
            case 'video_search_view': $obj = new VideoSearchView(self::$registry); break;    
            case 'video_upload_model': $obj =  new VideoUploadModel(self::$registry); break;    
            case 'video_upload_view': $obj = new VideoUploadView(self::$registry); break;
            case 'watch_video_view': $obj = new WatchVideoView(self::$registry); break;    
        }
        return $obj;
    }

    public static function getParameters(array $parameters){
        self::$parameters = $parameters;
    }
} 