<?php
class Registry implements RegistryInterface {
    protected $objects;

    public function __construct(){
        $this->objects = array();
    }

    public function __get($name){
    }
    public function __set($name, $value){
    }

    public function get($name, array $param=null){
        $name = strtolower($name);
        $new_obj = null;
        if (isset($this->objects[$name]))
            $new_obj = $this->objects[$name];
        else{
            $new_obj = Factory::getInstance($name, $param);
            if ($new_obj !== null)
                $this->objects[$name] = $new_obj;
        }
        return $new_obj;
    }

    public function getAll(){
        return $this->objects;
    }

    public function remove($name){
        $name = strtolower($name);
        if (!isset($this->objects[$name]))
            unset($this->objects[strtolower($name)]);
    }

    public function set($name, $value){
        $name = strtolower($name);
        if (!isset($this->objects[$name]))
            $this->objects[$name] = $value;
    }
} 