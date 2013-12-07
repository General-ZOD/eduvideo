<?php
class Db {
    protected $db_obj;

    public function __construct(array $db_settings){
        try{
            $this->db_obj = new PDO('mysql:host=' . $db_settings['server'] . ';dbname=' . $db_settings['database'] . ';', $db_settings['username'],
                $db_settings['password'], array(PDO::ATTR_EMULATE_PREPARES=>false, PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));
        }catch(PDOException $e){
            echo $e;
            //throw new PDOException('Error connecting to the system');
        }
    }
} 