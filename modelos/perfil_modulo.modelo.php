<?php

require_once "conexion.php";

class PerfilModuloModelo{

    static public function mdlRegistrarPerfilModulo($array_idModulos, $idPerfil, $id_modulo_inicio){

        $total_registros = 0;

        if($idPerfil == 1){
            $stmt = Conexion::conectar()->prepare("delete from perfil_modulo where id_perfil = :id_perfil and id_modulo != 13");
        }else{
            $stmt = Conexion::conectar()->prepare("delete from perfil_modulo where id_perfil = :id_perfil");
        }

        $stmt -> bindParam(":id_perfil",$idPerfil,PDO::PARAM_INT);
        $stmt -> execute();

        foreach ($array_idModulos as $value) { 

            if($idPerfil == 1 && $value == 13){
                $total_registros = $total_registros + 0;
            }else{

                if($value == $id_modulo_inicio){
                    $vista_inicio = 1;
                }else{
                    $vista_inicio = 0;
                }

                $stmt = Conexion::conectar()->prepare("INSERT INTO perfil_modulo(id_perfil,
                                                                                id_modulo,
                                                                                vista_inicio,
                                                                                estado)
                                                                    values(:id_perfil,
                                                                            :id_modulo,
                                                                            :vista_inicio,
                                                                            1)");

                $stmt -> bindParam(":id_perfil",$idPerfil,PDO::PARAM_INT);
                $stmt -> bindParam(":id_modulo",$value,PDO::PARAM_INT);
                $stmt -> bindParam(":vista_inicio",$vista_inicio,PDO::PARAM_INT);

                if($stmt->execute()){
                    $total_registros = $total_registros + 1;
                }else{
                    $total_registros = 0;
                }

            }

        }

        return $total_registros;
    }
}