<?php

class CategoriasControlador{

    static public function ctrListarCategorias(){
        
        $categorias = CategoriasModelo::mdlListarCategorias();

        return $categorias;
  
    }

    static public function ctrGuardarCategoria($accion, $idCategoria, $categoria, $medida){

        $guardarCategoria = CategoriasModelo::mdlGuardarCategoria($accion, $idCategoria, $categoria, $medida);

        return $guardarCategoria;
    }

}