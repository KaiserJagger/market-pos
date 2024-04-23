<?php

class Conexion{
    
    static public function conectar(){
    try {
        $conn = new PDO("mysql:host=localhost;dbname=market-pos", "Nico", "0cinco0uno",array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        return $conn;
    }
    catch(PDOException $e){
        echo 'Fallo la conexion: '  . $e->getMessage();
    }
}
}