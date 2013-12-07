<?php
class AdminCategoriesView extends View {
    private $registry;
    private $model;
    private $err_msg;
    private $content;
    private $user_id;
    private $loggedin_data_obj;
    private $model_data;

    public function __construct(RegistryInterface $registry){
        $this->registry = $registry;
        $this->loggedin_data_obj = $this->registry->get('logged_data');
        $this->db_categories = $this->registry->get('db_categories');
        $this->content = "";
        $this->err_msg = "";
    }

    protected function displayContent(){
        echo $this->template;
    }
    
    private function _getContent(){
        $content = '';
        if (count($this->categories_array) > 0){
            foreach ($this->categories_array as $key=>$value){
                $content .= '<form method="post" action="" name="update_category_form" class="update_category_form">
                             <tr onmouseover="this.style.background=\'#ccc\'" onmouseout="this.style.background=\'none\'">
                              <td><p class="each_category"><input type="text" name="updated_category_name" class="updated_category_name" value="' .
                                  ucwords($value[VideoCategories::COL_NAME]) . '" />
                                  <input type="hidden" name="updated_category_id" value="' . $value[VideoCategories::COL_VIDEO_CAT_ID] . '" /></p></td>
                              <td>
                                <button type="submit" name="manage_categories_btn" class="categories_details_btn">Update</button>
                                <button type="submit" name="delete_categories_btn" class="categories_delete_btn">Delete</button>
                                <button type="reset" name="manage_categories_btn" class="categories_reset_btn" style="display:none;">Cancel</button>
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
                  <h2 class="active" onClick="makeActive(this, \'create_categories\');">Create Categories</h2>    
                  <h2 onClick="makeActive(this, \'manage_categories\');">Manage Categories</h2>           
            ';
            $create_div = '<div id="create_categories">' . $this->err_msg;
            $manage_div = '<div id="manage_categories" style="display:none;">';
        }else{
            $header = '
                  <h2 onClick="makeActive(this, \'create_categories\');">Create Categories</h2>    
                  <h2 class="active" onClick="makeActive(this, \'manage_categories\');">Manage Categories</h2>           
            ';
            $create_div = '<div id="create_categories" style="display:none;">';
            $manage_div = '<div id="manage_categories">' . $this->err_msg;           
        }
        
        $content = '
          <div id="categories_div">
            <h1>Categories</h1>
            <div id="categories_header">
            ' . $header . '
            </div>

            ' . $create_div. '
             <form method="post" action="">
               <table cellspacing="0" cellpadding="0" id="create_categories_tbl">
                <tr><th id="create_categories_top_th" colspan="2">&nbsp;</th></tr>
                  <tr>
                    <td><strong>Category Name:</strong></td>
                    <td><input type="text" name="category_name" id="category_name" placeholder="Category" value="';
        $content .= (isset($this->model_data['category_name']))? $this->model_data['category_name'] : '';
        $content .= '" /> </td>
                  </tr>
                  <tr>
                    <td colspan="2" align="center"><button type="submit" name="create_category_btn" id="create_category_btn">Create</button> </td>
                  </tr>
                  <tr>
                    <input type="hidden" name="create" />
                    <th id="create_categories_bottom_th" colspan="2">&nbsp;</th>
                </tr>
               </table>
             </form>
            </div>
            
            ' . $manage_div . '
               <table cellspacing="0" cellpadding="0" id="manage_categories_tbl">
                <tr><th id="manage_categories_top_th" colspan="2">&nbsp;</th></tr>';
        $content .= $this->_getContent();
        $content .= '<tr><th id="manage_categories_bottom_th" colspan="2">&nbsp;</th></tr>
               </table>              
            </div>
            <script type="text/javascript">
              /* Please, fix me later
              $(".categories_details_btn").bind("click", function(){
              var btn = $(this).html().toLowerCase();
              if (btn == "edit"){
                 //$(this).html("Update");
                 var sibling_p = $(this).parent().siblings("td").children("p.each_category");
                 $(this).siblings(".categories_reset_btn").css("display", "inline");
                 sibling_p.html(\'<input type="text" name="category_name" id="category_name" value="\' + sibling_p.html() + \'" />\');
                 var parent = $(this).parent("td").prepend(\'<button type="submit" name="manage_categories_btn" class="categories_details_btn">Update</button>\');
                 $(this).remove();
              }
              });
              
              $(".categories_reset_btn").bind("click", function(){
              $(this).css("display", "none");
              var sibling_p = $(this).parent().siblings("td").children("p.each_category");
              var current_content = sibling_p.children("#category_name").val();
              sibling_p.html(current_content);
              }); */             
            </script>
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
                $this->err_msg='<p id="category_error">Internal System Error. Please, try again later</p>'; break;
            case 0: //success
                $this->err_msg='<p id="category_success">Category has been deleted</p>';
        }        
    }     
    
    private function _setInsertErrorContent($code){
        $this->model = $this->registry->get("admin_categories_model");
        $this->model_data = $this->model->getInputData();
        switch ($code){
            case 3: //duplicate category
                $this->err_msg='<p id="category_error">Category already exists</p>'; break;
            case 2: //empty category
                $this->err_msg='<p id="category_error">Category Name is empty</p>'; break;
            case 1: //error within the db
                $this->err_msg='<p id="category_error">Internal System Error. Please, try again later</p>'; break;
            case 0: //success
                $this->err_msg='<p id="category_success">Category has been added into the system</p>';
                $this->model_data['category_name']=''; break;
        }        
    }

    public function setupContent($action='', $code=null){
        //get the template, compose the message, put message in template, display the   
        $role_id = $this->loggedin_data_obj->getData(Definitions::ROLE_ID);
        $this->getTemplate();
        $this->setInnerNavigation($role_id, 'admin');
        $this->setFooterNavigation();
        $css='<link rel="stylesheet" href="/css/admin_categories.css" type="text/css" />';
        $js = '
              <script type="text/javascript"> 
               function makeActive(element, id_of_content){
                   if (!$(element).hasClass("active")){
                       $("div#categories_header h2").removeClass("active");
                       $("#create_categories, #manage_categories").css("display", "none");
                       $(element).addClass("active");
                       $("#" + id_of_content).css("display", "block");
                   }
               }
             </script>        
        ';
        $this->setHeader($title="EduVideo.com | Manage Categories", $meta="", $css=$css, $js=$js);
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
                $this->err_msg='<p id="category_error">Category already exists</p>'; break;
            case 2: //empty category
                $this->err_msg='<p id="category_error">Category Name is empty</p>'; break;
            case 1: //error within the db
                $this->err_msg='<p id="category_error">Internal System Error. Please, try again later</p>'; break;
            case 0: //success
                $this->err_msg='<p id="category_success">Category has been updated</p>';
        }        
    }    
} 