<?php

require_once "../controladores/modulo.controlador.php";
require_once "../modelos/modulo.modelo.php";

class AjaxModulos{

    public function ajaxObtenerModulos(){

        $modulos = ModuloControlador::ctrObtenerModulos();

        echo json_encode($modulos);
    }

    public function ajaxObtenerModulosPorPerfil($id_perfil){

        $modulosPerfil = ModuloControlador::ctrObtenerModulosPorPerfil($id_perfil);

        echo json_encode($modulosPerfil);
    }

    /*==============================================================
    SE USA PARA EL MODULO DE MANTENIMIENTO DE MODULOS
    ==============================================================*/
    public function ajaxObtenerModulosSistema(){

        $modulosSistema = ModuloControlador::ctrObtenerModulosSistema();

        echo json_encode($modulosSistema);
    }

    /*==============================================================
    FNC PARA REORGANIZAR LOS MODULOS DEL SISTEMA
    ==============================================================*/
    public function ajaxReorganizarModulos($modulos_ordenados){

        $modulosOrdenados = ModuloControlador::ctrReorganizarModulos($modulos_ordenados);

        echo json_encode($modulosOrdenados);
    }

    /*==============================================================
    FNC PARA REGISTRAR MODULOS
    ==============================================================*/
    public function ajaxRegistrarModulo($data_modulo){

        $registro_modulo = ModuloControlador::ctrRegistrarModulo($data_modulo);

        echo json_encode($registro_modulo);
    }


}


if(isset($_POST['accion']) && $_POST['accion'] == 1){ //
    $modulos = new AjaxModulos;
    $modulos->ajaxObtenerModulos();
}else if(isset($_POST['accion']) && $_POST['accion'] == 2){ //
    $modulosPerfil = new AjaxModulos();
    $modulosPerfil->ajaxObtenerModulosPorPerfil($_POST["id_perfil"]);
}
/* ===============================================================
SOLICITUD PARA OBTENER MODULOS DEL SISTEMA
================================================================*/
else if(isset($_POST['accion']) && $_POST['accion'] == 3){ //

    $modulosSistema = new AjaxModulos;
    $modulosSistema->ajaxObtenerModulosSistema();

}
/* ===============================================================
SOLICITUD PARA REORGANIZAR LOS MODULOS
================================================================*/
else if(isset($_POST['accion']) && $_POST['accion'] == 4){ 

    $organizar_modulos = new AjaxModulos;
    $organizar_modulos->ajaxReorganizarModulos($_POST["modulos"]);

}
/* ===============================================================
SOLICITUD PARA REGISTRO DE MODULOS
================================================================*/
else if(isset($_POST['accion']) && $_POST['accion'] == 5){ 

    $array_datos = [];

    parse_str($_POST['datos'], $array_datos);
 
    $registro_modulo = new AjaxModulos();
    $registro_modulo->ajaxRegistrarModulo($array_datos);

}