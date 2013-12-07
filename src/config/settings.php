<?php
//define all the settings to be imported into the system here
$server='localhost';
$roles = array("administrator"=>"1", "technical"=>"2", "member"=>"3");
$db_credentials=array("admin"=>array("user"=>"administrator", "password"=>"vL15_?>4$+i0y#H|3j"),
                      "tech"=>array("user"=>"technical", "password"=>"i_2m_a_l33T*.d0-Not_cR055&"),
                      "user"=>array("user"=>"member", "password"=>"y0U_c2nN0t_Br3aK_7hI5"));
$database="vlis";

$parameters = array("access"=>array("categories"=>array_values($roles), "contact"=>array_values($roles),  "home"=>array_values($roles), "login"=> array_values($roles)),
                    "db_settings"=>array("server"=>$server, "db"=>$database, "db_cred"=>$db_credentials),
                    "login"=>array("max_attempts"=>5)
                   );