<?php
class AdminQuestionsView extends View {
    private $registry;
    private $model;
    private $err_msg;
    private $content;
    private $user_id;
    private $loggedin_data_obj;
    private $model_data;
    private $db_question_obj;

    public function __construct(RegistryInterface $registry){
        $this->registry = $registry;
        $this->loggedin_data_obj = $this->registry->get('logged_data');
        $this->db_question_obj = $this->registry->get('db_questions');
        $this->db_categories = $this->registry->get('db_categories');
        $this->content = "";
        $this->err_msg = "";
    }

    protected function displayContent(){
        echo $this->template;
    }
    
    private function _getContent(){
        $questions_array = $this->db_question_obj->selectQuestions();
        $content = '';
        if (count($questions_array) > 0){
            foreach ($questions_array as $key=>$value){
                $content .= '<form method="post" action="" name="update_question_form" class="update_question_form">
                             <tr onmouseover="this.style.background=\'#ccc\'" onmouseout="this.style.background=\'none\'">
                              <td><p class="each_question"><input type="text" name="updated_question_name" class="updated_security_question" value="' .
                                  ucwords($value[SecurityQuestions::COL_QUESTION]) . '" />
                                  <input type="hidden" name="updated_question_id" value="' . $value[SecurityQuestions::COL_QUESTION_ID] . '" /></p></td>
                              <td>
                                <button type="submit" name="manage_questions_btn" class="manage_questions_btn">Update</button>
                                <button type="submit" name="delete_questions_btn" class="manage_questions_btn">Delete</button>
                                <button type="reset" name="manage_questions_btn" class="manage_questions_btn" style="display:none;">Cancel</button>
                              </td>
                             </tr>
                             </form>'; 
            }           
        }else{
            $content .= '<tr><td colspan="2">No Categories available</td></tr>';
        }
        return $content;
    }
    
    private function _getTabs($make_insert_default=true){
        if ($make_insert_default){
            $header = '
                  <h2 class="active" onClick="makeActive(this, \'create_questions\');">Create Questions</h2>    
                  <h2 onClick="makeActive(this, \'manage_questions\');">Manage Questions</h2>            
            ';
            $create_div = '<div id="create_questions">' . $this->err_msg;
            $manage_div = '<div id="manage_questions" style="display:none;">';
        }else{
            $header = '
                  <h2 onClick="makeActive(this, \'create_questions\');">Create Questions</h2>    
                  <h2 class="active" onClick="makeActive(this, \'manage_questions\');">Manage Questions</h2>            
            ';
            $create_div = '<div id="create_questions" style="display:none;">';
            $manage_div = '<div id="manage_questions">' . $this->err_msg;            
        }
        $content = '
          <div id="questions_div">
            <h1>Security Questions</h1>
            <div id="questions_header">' . $header . '
            </div>

            ' . $create_div . '
             <form method="post" action="">
               <table cellspacing="0" cellpadding="0" id="create_questions_tbl">
                <tr><th id="create_questions_top_th" colspan="2">&nbsp;</th></tr>
                  <tr>
                    <td><strong>Question:</strong></td>
                    <td><input type="text" name="security_question" id="security_question" placeholder="Security Question" value="';
        $content .= (isset($this->model_data['security_question']))? $this->model_data['security_question'] : '';
        $content .= '" /> </td>
                  </tr>
                  <tr>
                    <td colspan="2" align="center"><button type="submit" name="create_questions_btn" id="create_questions_btn">Create</button> </td>
                  </tr>
                  <tr>
                    <input type="hidden" name="create" />
                    <th id="create_questions_bottom_th" colspan="2">&nbsp;</th>
                </tr>
               </table>
             </form>
            </div>
            
            ' . $manage_div . '
               <table cellspacing="0" cellpadding="0" id="manage_questions_tbl">
                <tr><th id="manage_questions_top_th" colspan="2">&nbsp;</th></tr>';
        $content .= $this->_getContent();
        $content .= '<tr><th id="manage_questions_bottom_th" colspan="2">&nbsp;</th></tr>
               </table>              
            </div>
          </div>
        ';
        return $content;
    }

    protected function getTemplate(){
        $this->user_id = $this->loggedin_data_obj->getData(Definitions::USER_ID);
        $this->template = (file_exists(TEMPLATES . "loggedin.html"))? file_get_contents(TEMPLATES . "loggedin.html") : "";
    }
    
    private function _setDeleteErrorContent($code){
        switch ($code){
            case 1: //error within the db
                $this->err_msg='<p id="questions_error">Internal System Error. Please, try again later</p>'; break;
            case 0: //success
                $this->err_msg='<p id="questions_success">Question has been deleted</p>';
        }        
    }     
    
    private function _setInsertErrorContent($code){
        $this->model = $this->registry->get("admin_categories_model");
        $this->model_data = $this->model->getInputData();
        switch ($code){
            case 3: //duplicate category
                $this->err_msg='<p id="questions_error">Question already exists</p>'; break;
            case 2: //empty category
                $this->err_msg='<p id="questions_error">Question is empty</p>'; break;
            case 1: //error within the db
                $this->err_msg='<p id="questions_error">Internal System Error. Please, try again later</p>'; break;
            case 0: //success
                $this->err_msg='<p id="questions_success">Category has been added into the system</p>';
                $this->model_data['security_question']=''; break;
        }        
    }

    public function setupContent($action='', $code=null){
        //get the template, compose the message, put message in template, display the   
        $role_id = $this->loggedin_data_obj->getData(Definitions::ROLE_ID);
        $this->getTemplate();
        $this->setInnerNavigation($role_id, 'admin');
        $this->setFooterNavigation();
        $css='<link rel="stylesheet" href="/css/admin_questions.css" type="text/css" />';
        $js = '
              <script type="text/javascript"> 
               function makeActive(element, id_of_content){
                   if (!$(element).hasClass("active")){
                       $("div#questions_header h2").removeClass("active");
                       $("#create_questions, #manage_questions").css("display", "none");
                       $(element).addClass("active");
                       $("#" + id_of_content).css("display", "block");
                   }
               }
             </script>        
        ';
        $this->setHeader($title="EduVideo.com | Manage Security Questions", $meta="", $css=$css, $js=$js);
        switch($action){
            case 'insert': $this->_setInsertErrorContent($code); $content = $this->_getTabs(); break;
            case 'update': $this->_setUpdateErrorContent($code); $content = $this->_getTabs(false); break;
            case 'delete': $this->_setDeleteErrorContent($code); $content = $this->_getTabs(false); break;
            default: $content = $this->_getTabs();
        }        

        $this->template = str_replace("{{content}}", $content, $this->template);
        $this->displayContent();
    }
    
    private function _setUpdateErrorContent($code){
        switch ($code){
            case 3: //duplicate category
                $this->err_msg='<p id="questions_error">Question already exists</p>'; break;
            case 2: //empty category
                $this->err_msg='<p id="questions_error">Question is empty</p>'; break;
            case 1: //error within the db
                $this->err_msg='<p id="questions_error">Internal System Error. Please, try again later</p>'; break;
            case 0: //success
                $this->err_msg='<p id="questions_success">Question has been updated</p>';
        }        
    }    
} 