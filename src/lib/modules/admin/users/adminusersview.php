<?php
class AdminUsersView extends View {
    private $registry;
    private $model;
    private $err_msg;
    private $user_id;
    private $loggedin_data_obj;
    private $model_data;
    private $roles_array;
    private $users_array;

    public function __construct(RegistryInterface $registry){
        $this->registry = $registry;
        $this->loggedin_data_obj = $this->registry->get('logged_data');
        $this->db_categories = $this->registry->get('db_categories');
        $this->db_users_obj = $this->registry->get(Definitions::DB_USERS);
        $this->err_msg = "";
        $this->users_array = [];
    }
    
    private function _getRoleName($role_id){
        $content='';
        foreach($this->roles_array as $key=>$value){
            if ($value[Roles::COL_ROLE_ID] == $role_id)
               return ucwords($value[Roles::COL_ROLE_NAME]);
        }
        return $content;        
    }
    
    private function _getTabs(){
        $header = '<h2 class="active">Manage Users</h2>';
        $manage_div = '<div id="manage_uploaded_video">' . $this->err_msg;           
        
        $user_content = $this->_getUsers();
        $content = '
          <div id="upload_videos_div">
            <h1>Admin Management - All Users</h1>
            <div id="upload_videos_header">
            ' . $header . '
            </div>' . $manage_div . '
               <table cellspacing="0" cellpadding="0" id="manage_uploaded_video_tbl">
                <tr><th id="manage_upload_movies_top_th" class="top_th" colspan="2">All Users - ' . count($this->users_array) .
                ' user(s) in the system</th></tr>';
        $content .= $user_content;
        $content .= '<tr><th id="manage_upload_movies_bottom_th" class="bottom_th" colspan="2">&nbsp;</th></tr>
               </table>              
            </div>
          </div>
        ';
        return $content;
    }   
    
    private function _getUsers(){
        if (count($this->users_array) == 0){
            $this->users_array = $this->db_users_obj->selectAllUsers();
            $this->roles_array = $this->db_users_obj->selectRoles();
        }
        
        if (count($this->users_array) > 0){
            $content = '';
            $counter = 0;
            foreach($this->users_array as $key=>$value){
                $email = $value[Users::COL_EMAIL];
                $id = $value[Users::COL_USER_ID];
                $dob = $value[Users::COL_DOB];
                $date_registered = $value[Users::COL_DATE_REGISTERED];
                $is_active = $value[Users::COL_IS_ACTIVE];
                $last_login = $value[Users::COL_LAST_LOGIN];
                $is_locked_out = $value[Users::COL_IS_LOCKED_OUT];
                $username = $value[Users::COL_USER_NAME];
                $role = $this->_getRoleName($value[Users::COL_ROLE_ID]);
                
                $title = ($username == '')? $email : $username;
                $dob = DateTime::createFromFormat('Y-m-d H:i:s' ,$dob)->format('F jS, Y');
                $is_active = ($is_active)? 'Active' : 'Not Active';
                $is_locked_out = ($is_locked_out)? 'Currently locked out' : 'Not locked out';
                $date_registered = DateTime::createFromFormat('Y-m-d H:i:s', $date_registered);
                $last_login = DateTime::createFromFormat('Y-m-d H:i:s' ,$last_login);
                
                $tr = ($counter %2 == 0)? '<tr class="even">' : '<tr>';
                
                $content .= '<form method="post">' . $tr . '
                              <td valign="top" width="100%">
                               <h3>' . $title . '</h3>
                               <p>' . $email . ', born on ' . $dob . '</p>
                               <p><strong>Role</strong> is <em>' . $role . '</em></p>                               
                               <p>' . $is_active . ', <em>' . $is_locked_out . '</em></p>
                               <p>Registered on <em>' . $date_registered->format('F jS, Y') .
                                ' at ' . $date_registered->format('h:i:s a') . ' CST</em></p>
                               <p>Last login on <em>' . $last_login->format('F jS, Y') . ' at ' . $last_login->format('h:i:s a')
                                . ' CST</em></p>
                               <p class="upload_details_p"> <input type="hidden" name="user_id" value="' . $id . '" /> 
                               </p>
                              </td>
                              <td></td>
                             </tr></form>';
                ++$counter;             
            }
            return $content;
        }else{
            return '<tr><td colspan="2">No Users Available</td></tr>';
        }
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
        $css='<link rel="stylesheet" href="/css/admin_users.css" type="text/css" />';
        $js = '';
        $this->setHeader($title="EduVideo.com | Users Management by Admin", $meta="", $css=$css, $js=$js);
        switch($action){
            case 'details': $this->_setDetailsContent($code); $content = $this->_getTabs(); break;    
            default: $content = $this->_getTabs();
        }        

        $this->template = str_replace("{{content}}", $content, $this->template);
        $this->displayContent();       
    }    
} 