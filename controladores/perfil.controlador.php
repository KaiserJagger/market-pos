<?php

class PerfilControlador{

    static public function ctrObtenerPerfiles(){

        $modulos = PerfilModelo::mdlObtenerPerfiles();

        return $modulos;
    }

}