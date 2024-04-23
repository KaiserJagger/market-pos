<?php

require_once "../controladores/perfil.controlador.php";
require_once "../modelos/perfil.modelo.php";

class AjaxPerfiles{

    public function ajaxObtenerPerfiles(){

        $perfiles = PerfilControlador::ctrObtenerPerfiles();

        echo json_encode($perfiles);
    }
   
}


if(isset($_POST['accion']) && $_POST['accion'] == 1){

    $perfiles = new AjaxPerfiles;    
    $perfiles->ajaxObtenerPerfiles();

}
