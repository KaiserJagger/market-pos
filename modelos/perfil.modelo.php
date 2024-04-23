<?php

require_once "conexion.php";

class PerfilModelo{

    static public function mdlObtenerPerfiles(){

        $stmt = Conexion::conectar()->prepare("select p.id_perfil,
                                                        p.descripcion,
                                                        p.estado,
                                                        date(p.fecha_creacion) as fecha_creacion,
                                                        p.fecha_actualizacion,
                                                        ' ' as opciones
                                                from perfiles p
                                                order by p.id_perfil");

        $stmt -> execute();

        return $stmt->fetchAll();

    }
}