<?php
interface RegistryInterface {
    public function get($name, array $param=null);

    public function getAll();

    public function remove($name);

    public function set($name, $value);
} 