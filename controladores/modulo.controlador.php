<?php

class ModuloControlador{

    static public function ctrObtenerModulos(){

        $modulos = ModuloModelo::mdlObtenerModulos();

        return $modulos;
    }

    static public function ctrObtenerModulosPorPerfil($id_perfil){

        $modulosPorPerfil = ModuloModelo::mdlObtenerModulosPorPerfil($id_perfil);

        return $modulosPorPerfil;

    }

     /*==============================================================
    SE USA PARA EL MODULO DE MANTENIMIENTO DE MODULOS
    ==============================================================*/
    static public function ctrObtenerModulosSistema(){

        $modulosSistema = ModuloModelo::mdlObtenerModulosSistema();

        return $modulosSistema;
    }

    /*==============================================================
    FNC PARA REORGANIZAR LOS MODULOS DEL SISTEMA
    ==============================================================*/
    static public function ctrReorganizarModulos($modulos_ordenados){

        $modulosOrdenados = ModuloModelo::mdlReorganizarModulos($modulos_ordenados);

        return $modulosOrdenados;
    }

    /*==============================================================
    FNC PARA REGISTRAR MODULOS
    ==============================================================*/
    static public function ctrRegistrarModulo($data_modulo){

        $registro_modulo = ModuloModelo::mdlRegistrarModulo($data_modulo);

        return $registro_modulo;
    }


}