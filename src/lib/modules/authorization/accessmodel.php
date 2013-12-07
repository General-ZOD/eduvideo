<?php
/**
 *  array = array("authentication"=>array(role1, role2, role3))
 */
class AccessModel {
    private $available_access;
    private $registry;
    private $logged_in_data_obj;

    public function __construct(array $all_access, RegistryInterface $registry){
        $this->registry = $registry;
        $this->available_access = $all_access;
    }

    public function allowAccess($module){
        $this->logged_in_data_obj = $this->registry->get("logged_data");
        $role_id=null;
        if (isset($this->available_access[$module])){
            if (in_array($role_id, $this->available_access[$module])){
                return true;
            }
        }
        return false;
    }
} 