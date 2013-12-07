<?php
abstract class View {
    protected $template;
    protected $db_categories;
    protected $categories_array;

    abstract protected function displayContent();
    abstract protected function getTemplate();
    protected function setFooterNavigation(){
        $footer_nav = '
           <div id="footer">
            <ul>
                <li><a href="/privacy">Privacy</a></li>
                <li><a href="/tos">Term and Conditions</a></li>
            </ul>
           </div>
        ';
        $this->template = str_replace('{{footer}}', $footer_nav, $this->template);
    }

    protected function setHeader($title='', $meta='', $css='', $js=''){
        $header = '
            <head>
                <title>' . $title . '</title>
                <meta charset="utf-8" />
                <meta name="viewport" content="width-device-width, initial-scale=1.0">
                ' . $meta . '
                <link rel="stylesheet"  type="text/css" href="http://fonts.googleapis.com/css?family=Questrial|Aclonica|ABeeZee" />
                <link rel="stylesheet" href="/css/bodyStyle.css" type="text/css" />
                ' . $css . '
                <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
                ' . $js . '
            </head>';
        $this->template = str_replace('{{header}}', $header, $this->template);
    }

    protected function setInnerNavigation($role_id, $page=''){
        $this->categories_array = $this->db_categories->selectCategories();
        $cat='';
        if (count($this->categories_array) > 0){
            $cat = '<ul id="inner_nav">';
            foreach ($this->categories_array as $key=>$value){
               $cat .= '<li><a href="/categories/' . urlencode($value[VideoCategories::COL_NAME]) . '">' .
               ucwords($value[VideoCategories::COL_NAME]) . '</a></li>' . PHP_EOL;
            }
            $cat .= '</ul>';
        }

        // array('page'=>array('link', 'content', 'other attributes'))
        if ($role_id == '1'){ //admin
            $pages = array('home'=>array('/', 'Home', ''),
                           'admin'=>array('/admin', 'Admin
                                                        <ul id="inner_nav">
                                                            <li><a href="/admin/categories">Manage Categories</a></li>
                                                            <li><a href="/admin/questions">Security Questions</a></li>
                                                            <li><a href="/admin/videos">Manage Videos</a></li>
                                                            <li><a href="/admin/users">Manage Users</a></li>
                                                        </ul>', ''),                           
                           'categories'=>array('/categories', 'Categories ' . $cat, ''),
                           'upload'=>array('/upload', 'Upload', ''),
                           'profile'=>array('/profile', 'My Profile', ''),
                           ''=>array('', '<div id="search_li"><form method="get" action="/search/">
                                           <input type="text" name="video_search" id="video_search" placeholder="Search Videos" />
                                           <button type="submit" id="video_search_btn">Search</button>
                                          </form></div>', ''),
                           'logout'=>array('/logout', 'Log Out', 'id="logout"')
            );            
        }else if ($role_id == '3'){
            $pages = array('home'=>array('/', 'Home', ''),
                           'categories'=>array('/categories', 'Categories ' . $cat, ''),
                           'upload'=>array('/upload', 'Upload', ''),
                           'profile'=>array('/profile', 'My Profile', ''),
                           ''=>array('', '<div id="search_li"><form method="get" action="/search/">
                                           <input type="text" name="video_search" id="video_search" placeholder="Search Videos" />
                                           <button type="submit" id="video_search_submit">Search</button>
                                          </form></div>', ''),
                           'logout'=>array('/logout', 'Log Out', 'id="logout"')
            );            
        }

        $outer_nav = '<ul id="nav">';
        foreach ($pages as $key=>$value){
            if ($key == '')
                $outer_nav .= '<li>' . $value[1] . '</li>';
            else if ($key=='admin')
                $outer_nav .= '<li>' . $value[1] . '</li>';
            else if ($key=='categories')
                $outer_nav .= '<li>' . $value[1] . '</li>';            
            else{
                $outer_nav .= '<li><a href="' . $value[0] . '" ' . $value[2];
                if ($key == $page)
                    $outer_nav .= ' id="active_link"' ;
                $outer_nav .= '>' . $value[1] . '</a></li>';
            }
        }
        $outer_nav .= '</ul>';
        $this->template = str_replace('{{nav}}', $outer_nav, $this->template);        
    }

    protected function setOuterNavigation($page=''){
        $this->categories_array = $this->db_categories->selectCategories();
        $cat='';
        if (count($this->categories_array) > 0){
            $cat = '<ul id="inner_nav">';
            foreach ($this->categories_array as $key=>$value){
               $cat .= '<li><a href="/categories/' . urlencode($value[VideoCategories::COL_NAME]) . '">' .
               ucwords($value[VideoCategories::COL_NAME]) . '</a></li>' . PHP_EOL;
            }
            $cat .= '</ul>';
        }
        
        // array('page'=>array('link', 'content', 'other attributes'))
        $pages = array('home'=>array('/', 'Home', ''),
                       'signup'=>array('/signup', 'Sign Up', ''),
                       'categories'=>array('/categories', 'Categories ' . $cat, ''),
                       'upload'=>array('/upload', 'Upload', ''),
                       ''=>array('', '<div id="search_li"><form method="get" action="/search/">
                                       <input type="text" name="video_search" id="video_search" placeholder="Search Videos" />
                                       <button type="submit" id="video_search_btn">Search</button>
                                      </form></div>', ''),
                       'login'=>array('/login', "Login", 'id="login"')
        );
        $outer_nav = '<ul id="nav">';
        foreach ($pages as $key=>$value){
            if ($key == "")
                $outer_nav .= '<li>' . $value[1] . '</li>';
            else if ($key=="categories"){
                $outer_nav .= '<li>' . $value[1] . '</li>';
            }
            else{
                $outer_nav .= '<li><a href="' . $value[0] . '" ' . $value[2];
                if ($key == $page)
                    $outer_nav .= ' id="active_link"' ;
                $outer_nav .= '>' . $value[1] . '</a></li>';
            }
        }
        $outer_nav .= '</ul>';
        $this->template = str_replace('{{nav}}', $outer_nav, $this->template);
    }
}