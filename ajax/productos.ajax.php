<?php

require_once "../controladores/productos.controlador.php";
require_once "../modelos/productos.modelo.php";

require_once "../vendor/autoload.php";

class ajaxProductos{

    public $fileProductos;

    public $codigo_producto;
    public $id_categoria_producto;
    public $descripcion_producto;
    public $precio_compra_producto;
    public $precio_venta_producto;
    public $utilidad;
    public $stock_producto;
    public $minimo_stock_producto;
    public $ventas_producto;

    public $cantidad_a_comprar;

    public function ajaxCargaMasivaProductos(){

        $respuesta = ProductosControlador::ctrCargaMasivaProductos($this->fileProductos);

        echo json_encode($respuesta);
    }

    public function ajaxListarProductos(){
    
        $productos = ProductosControlador::ctrListarProductos();
    
        echo json_encode($productos);
    
    }

    public function ajaxRegistrarProducto($array_datos_producto, $imagen = null){
        
        $producto = ProductosControlador::ctrRegistrarProducto($array_datos_producto, $imagen);

        echo json_encode($producto);
    }

    public function ajaxAumentarStock(){

        $respuesta = ProductosControlador::ctrAumentarStock($_POST["codigo_producto"], $_POST["nuevoStock"]);

        echo json_encode($respuesta);
    }

    public function ajaxDisminuirStock(){

        $respuesta = ProductosControlador::ctrDisminuirStock($_POST["codigo_producto"], $_POST["nuevoStock"]);

        echo json_encode($respuesta);
    }

    
    public function ajaxActualizarProducto($data){
        
        $table = "productos";
        $id = $_POST["codigo_producto"];
        $nameId = "codigo_producto";

        $respuesta = ProductosControlador::ctrActualizarProducto($table, $data, $id, $nameId);

        echo json_encode($respuesta);
    }

    public function ajaxEliminarProducto(){

        $table = "productos"; 
        $id = $_POST["codigo_producto"]; 
        $nameId = "codigo_producto";

        $respuesta = ProductosControlador::ctrEliminarProducto($table, $id, $nameId);

        echo json_encode($respuesta);
    }

    /*===================================================================
    LISTAR NOMBRE DE PRODUCTOS PARA INPUT DE AUTO COMPLETADO
    ====================================================================*/
    public function ajaxListarNombreProductos(){

        $NombreProductos = ProductosControlador::ctrListarNombreProductos();

        echo json_encode($NombreProductos);
    }

    /*===================================================================
    BUSCAR PRODUCTO POR SU CODIGO DE BARRAS
    ====================================================================*/
    public function ajaxGetDatosProducto(){
        
        $producto = ProductosControlador::ctrGetDatosProducto($this->codigo_producto);

        echo json_encode($producto);
    }

    public function ajaxVerificaStockProducto(){

        $respuesta = ProductosControlador::ctrVerificaStockProducto($this->codigo_producto,$this->cantidad_a_comprar);
   
       echo json_encode($respuesta);
   }


   
}

if(isset($_POST['accion']) && $_POST['accion'] == 1){ // parametro para listar productos

    $productos = new ajaxProductos();
    $productos -> ajaxListarProductos();
    
}else if(isset($_POST['accion']) && $_POST['accion'] == 2){ // parametro para registrar productos   

    $array_datos_producto = [];
    parse_str($_POST['detalle_producto'], $array_datos_producto);

    if(isset($_FILES["archivo"]["name"])){

        $imagen["ubicacionTemporal"] =  $_FILES["archivo"]["tmp_name"][0];

        //capturamos el nombre de la imagen
        $info = new SplFileInfo($_FILES["archivo"]["name"][0]);

        //generamos un nombre aleatorio y unico para la imagen
        $imagen["nuevoNombre"] = sprintf("%s_%d.%s", uniqid(), rand(100,999), $info->getExtension());

        $imagen["folder"] = '../vistas/assets/imagenes/productos/';

        $registrarProducto = new AjaxProductos();
        $registrarProducto -> ajaxRegistrarProducto($array_datos_producto, $imagen);

    }else{
        $registrarProducto = new AjaxProductos();
        $registrarProducto -> ajaxRegistrarProducto($array_datos_producto);
    }

    
    
}else if(isset($_POST['accion']) && $_POST['accion'] == 3){ // parametro para actualizar stock del producto

    $actualizarStock = new ajaxProductos();


    if($_POST['tipo_movimiento'] == 1){
        $actualizarStock -> ajaxAumentarStock();
    }else{
        $actualizarStock -> ajaxDisminuirStock();
    }
    


}else if(isset($_POST['accion']) && $_POST['accion'] == 4){ // ACCION PARA ACTUALIZAR UN PRODUCTO
 
    $actualizarProducto = new ajaxProductos();

    $data = array(
        "id_categoria_producto" => $_POST["id_categoria_producto"],
        "descripcion_producto" => $_POST["descripcion_producto"],
        "precio_compra_producto" => $_POST["precio_compra_producto"],
        "precio_venta_producto" => $_POST["precio_venta_producto"],
        "utilidad" => $_POST["utilidad"],
        "stock_producto" => $_POST["stock_producto"],
        "minimo_stock_producto" => $_POST["minimo_stock_producto"],
    );

    $actualizarProducto -> ajaxActualizarProducto($data);

}else if(isset($_POST['accion']) && $_POST['accion'] == 5){// ACCION PARA ELIMINAR UN PRODUCTO

    $eliminarProducto = new ajaxProductos();
    $eliminarProducto -> ajaxEliminarProducto();

}else if(isset($_POST["accion"]) && $_POST["accion"] == 6){  // TRAER LISTADO DE PRODUCTOS PARA EL AUTOCOMPLETE

    $nombreProductos = new AjaxProductos();
    $nombreProductos -> ajaxListarNombreProductos();

}else if(isset($_POST["accion"]) && $_POST["accion"] == 7){ // OBTENER DATOS DE UN PRODUCTO POR SU CODIGO

    $listaProducto = new AjaxProductos();

    $listaProducto -> codigo_producto = $_POST["codigo_producto"];
    
    $listaProducto -> ajaxGetDatosProducto();
	
}else if(isset($_POST["accion"]) && $_POST["accion"] == 8){ // VERIFICAR STOCK DEL PRODUCTO

    $verificaStock = new AjaxProductos();

    $verificaStock -> codigo_producto = $_POST["codigo_producto"];
    $verificaStock -> cantidad_a_comprar = $_POST["cantidad_a_comprar"];
    
    $verificaStock -> ajaxVerificaStockProducto();

}else if(isset($_FILES)){
    $archivo_productos = new ajaxProductos();
    $archivo_productos-> fileProductos = $_FILES['fileProductos'];
    $archivo_productos -> ajaxCargaMasivaProductos();
}