<?php

require_once "conexion.php";

use PhpOffice\PhpSpreadsheet\IOFactory;


class ProductosModelo
{

    /*===================================================================
    REALIZAR LA CARGA MASIVA DE PRODUCTOS MEDIANTE ARCHIVO EXCEL
    ====================================================================*/
    static public function mdlCargaMasivaProductos($fileProductos)
    {

        $nombreArchivo = $fileProductos['tmp_name'];

        $documento = IOFactory::load($nombreArchivo);

        $hojaCategorias = $documento->getSheet(1);
        $numeroFilasCategorias = $hojaCategorias->getHighestDataRow();

        $hojaProductos = $documento->getSheetByName("Productos");
        $numeroFilasProductos = $hojaProductos->getHighestDataRow();

        $categoriasRegistradas = 0;
        $productosRegistrados = 0;

        $stmt = Conexion::conectar()->prepare("delete from venta_detalle");
        $stmt->execute();

        $stmt = Conexion::conectar()->prepare("delete from venta_cabecera");
        $stmt->execute();

        $stmt = Conexion::conectar()->prepare("delete from kardex");
        $stmt->execute();

        $stmt = Conexion::conectar()->prepare("delete from productos");
        $stmt->execute();

        $stmt = Conexion::conectar()->prepare("delete from categorias");
        $stmt->execute();

        $stmt = Conexion::conectar()->prepare("delete from productos");
        $stmt->execute();

        //CICLO FOR PARA REGISTROS DE CATEGORIAS
        for ($i = 2; $i <= $numeroFilasCategorias; $i++) {

            $categoria = $hojaCategorias->getCellByColumnAndRow(1, $i);
            $aplica_peso = $hojaCategorias->getCellByColumnAndRow(2, $i);
            $fecha_actualizacion = date("Y-m-d");

            if (!empty($categoria)) {
                $stmt = Conexion::conectar()->prepare("INSERT INTO categorias(nombre_categoria,
                                                                                aplica_peso,
                                                                                fecha_actualizacion_categoria)
                                                                    values(:nombre_categoria,
                                                                            :aplica_peso,
                                                                            :fecha_actualizacion_categoria);");

                $stmt->bindParam(":nombre_categoria", $categoria, PDO::PARAM_STR);
                $stmt->bindParam(":aplica_peso", $aplica_peso, PDO::PARAM_STR);
                $stmt->bindParam(":fecha_actualizacion_categoria", $fecha_actualizacion, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    $categoriasRegistradas = $categoriasRegistradas + 1;
                } else {
                    $categoriasRegistradas = 0;
                }
            }
        }

        if ($categoriasRegistradas > 0) {

            //CICLO FOR PARA REGISTROS DE PRODUCTOS
            for ($i = 2; $i <= $numeroFilasProductos; $i++) {

                $codigo_producto = $hojaProductos->getCell("A" . $i);
                $id_categoria_producto = ProductosModelo::mdlBuscarIdCategoria($hojaProductos->getCell("B" . $i));
                $descripcion_producto = $hojaProductos->getCell("C" . $i);
                $precio_compra_producto = $hojaProductos->getCell("D" . $i);
                $precio_venta_producto = $hojaProductos->getCell("E" . $i)->getCalculatedValue();
                $precio_mayor_producto = $hojaProductos->getCell("F" . $i)->getCalculatedValue();
                $precio_oferta_producto = $hojaProductos->getCell("G" . $i)->getCalculatedValue();
                $stock_producto = $hojaProductos->getCell("H" . $i);
                $minimo_stock_producto = $hojaProductos->getCell("I" . $i);
                $ventas_producto = $hojaProductos->getCell("J" . $i);
                $costo_total_producto = $hojaProductos->getCell("K" . $i);

                if (!empty($codigo_producto) && strlen($codigo_producto) > 0) {

                    $stmt = Conexion::conectar()->prepare("INSERT INTO productos(codigo_producto,
                                                                                id_categoria_producto,
                                                                                descripcion_producto,
                                                                                precio_compra_producto,
                                                                                precio_venta_producto,
                                                                                precio_mayor_producto,
                                                                                precio_oferta_producto,
                                                                                stock_producto,
                                                                                minimo_stock_producto,
                                                                                ventas_producto,
                                                                                costo_total_producto)
                                                                        values(:codigo_producto,
                                                                                :id_categoria_producto,
                                                                                :descripcion_producto,
                                                                                :precio_compra_producto,
                                                                                :precio_venta_producto,
                                                                                :precio_mayor_producto,
                                                                                :precio_oferta_producto,
                                                                                :stock_producto,
                                                                                :minimo_stock_producto,
                                                                                :ventas_producto,
                                                                                :costo_total_producto);");

                    $stmt->bindParam(":codigo_producto", $codigo_producto, PDO::PARAM_STR);
                    $stmt->bindParam(":id_categoria_producto", $id_categoria_producto[0], PDO::PARAM_STR);
                    $stmt->bindParam(":descripcion_producto", $descripcion_producto, PDO::PARAM_STR);
                    $stmt->bindParam(":precio_compra_producto", $precio_compra_producto, PDO::PARAM_STR);
                    $stmt->bindParam(":precio_venta_producto", $precio_venta_producto, PDO::PARAM_STR);
                    $stmt->bindParam(":precio_mayor_producto", $precio_mayor_producto, PDO::PARAM_STR);
                    $stmt->bindParam(":precio_oferta_producto", $precio_oferta_producto, PDO::PARAM_STR);
                    $stmt->bindParam(":stock_producto", $stock_producto, PDO::PARAM_STR);
                    $stmt->bindParam(":minimo_stock_producto", $minimo_stock_producto, PDO::PARAM_STR);
                    $stmt->bindParam(":ventas_producto", $ventas_producto, PDO::PARAM_STR);
                    $stmt->bindParam(":costo_total_producto", $costo_total_producto, PDO::PARAM_STR);

                    if ($stmt->execute()) {

                        $productosRegistrados = $productosRegistrados + 1;

                        $concepto = 'INVENTARIO INICIAL';
                        $comprobante = '';

                        //REGISTRAMOS KARDEX - INVENTARIO INICIAL
                        $stmt = Conexion::conectar()->prepare("call prc_registrar_kardex_existencias(:p_codigo_producto,
                                                                                                    :p_concepto,
                                                                                                    :p_comprobante,
                                                                                                    :p_unidades,
                                                                                                    :p_costo_unitario,
                                                                                                    :p_costo_total);");

                        $stmt->bindParam(":p_codigo_producto", $codigo_producto, PDO::PARAM_STR);
                        $stmt->bindParam(":p_concepto", $concepto, PDO::PARAM_STR);
                        $stmt->bindParam(":p_comprobante", $comprobante, PDO::PARAM_STR);
                        $stmt->bindParam(":p_unidades", $stock_producto, PDO::PARAM_STR);
                        $stmt->bindParam(":p_costo_unitario", $precio_compra_producto, PDO::PARAM_STR);
                        $stmt->bindParam(":p_costo_total", $costo_total_producto, PDO::PARAM_STR);

                        $stmt->execute();
                    } else {
                        $productosRegistrados = 0;
                    }
                }
            }
        }


        $respuesta["totalCategorias"] = $categoriasRegistradas;
        $respuesta["totalProductos"] = $productosRegistrados;

        return $respuesta;
    }

    /*===================================================================
    BUSCAR EL ID DE UNA CATEGORIA POR EL NOMBRE DE LA CATEGORIA
    ====================================================================*/
    static public function mdlBuscarIdCategoria($nombreCategoria)
    {

        $stmt = Conexion::conectar()->prepare("select id_categoria from categorias where nombre_categoria = :nombreCategoria");
        $stmt->bindParam(":nombreCategoria", $nombreCategoria, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch();
    }

    /*===================================================================
    OBTENER LISTADO TOTAL DE PRODUCTOS PARA EL DATATABLE
    ====================================================================*/
    static public function mdlListarProductos()
    {

        $stmt = Conexion::conectar()->prepare('call prc_ListarProductos');

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /*===================================================================
    REGISTRAR PRODUCTOS UNO A UNO DESDE EL FORMULARIO DEL INVENTARIO
    ====================================================================*/
    static public function mdlRegistrarProducto($array_datos_producto, $imagen)
    {

        try {

            $fecha = date('Y-m-d');
            $costo_total_producto = $array_datos_producto["iptPrecioCompraReg"] * $array_datos_producto["iptStockReg"];

            $stmt = Conexion::conectar()->prepare("INSERT INTO PRODUCTOS(codigo_producto, 
                                                                        id_categoria_producto,
                                                                        imagen_producto,
                                                                        descripcion_producto, 
                                                                        precio_compra_producto, 
                                                                        precio_venta_producto, 
                                                                        precio_mayor_producto,
                                                                        precio_oferta_producto,
                                                                        stock_producto, 
                                                                        minimo_stock_producto, 
                                                                        costo_total_producto,
                                                                        fecha_creacion_producto,
                                                                        fecha_actualizacion_producto) 
                                                VALUES (:codigo_producto, 
                                                        :id_categoria_producto,
                                                        :imagen_producto,
                                                        :descripcion_producto, 
                                                        :precio_compra_producto, 
                                                        :precio_venta_producto, 
                                                        :precio_mayor_producto,
                                                        :precio_oferta_producto,
                                                        :stock_producto, 
                                                        :minimo_stock_producto, 
                                                        :costo_total_producto,
                                                        :fecha_creacion_producto,
                                                        :fecha_actualizacion_producto)");

            $stmt->bindParam(":codigo_producto", $array_datos_producto["iptCodigoReg"], PDO::PARAM_STR);
            $stmt->bindParam(":id_categoria_producto", $array_datos_producto["selCategoriaReg"], PDO::PARAM_STR);
            $stmt->bindParam(":imagen_producto", $imagen["nuevoNombre"], PDO::PARAM_STR);
            $stmt->bindParam(":descripcion_producto", $array_datos_producto["iptDescripcionReg"], PDO::PARAM_STR);
            $stmt->bindParam(":precio_compra_producto", $array_datos_producto["iptPrecioCompraReg"], PDO::PARAM_STR);
            $stmt->bindParam(":precio_venta_producto", $array_datos_producto["iptPrecioVentaReg"], PDO::PARAM_STR);
            $stmt->bindParam(":precio_mayor_producto", $array_datos_producto["iptPrecioVentaMayorReg"], PDO::PARAM_STR);
            $stmt->bindParam(":precio_oferta_producto", $array_datos_producto["iptPrecioVentaOfertaReg"], PDO::PARAM_STR);
            $stmt->bindParam(":stock_producto", $array_datos_producto["iptStockReg"], PDO::PARAM_STR);
            $stmt->bindParam(":minimo_stock_producto", $array_datos_producto["iptMinimoStockReg"], PDO::PARAM_STR);
            $stmt->bindParam(":costo_total_producto", $costo_total_producto, PDO::PARAM_STR);
            $stmt->bindParam(":fecha_creacion_producto", $fecha, PDO::PARAM_STR);
            $stmt->bindParam(":fecha_actualizacion_producto", $fecha, PDO::PARAM_STR);

            if ($stmt->execute()) {

               
                //GUARDAMOS LA IMAGEN EN LA CARPETA
                if($imagen){
                                
                    $guardarImagen = new ProductosModelo();

                    $guardarImagen->guardarImagen($imagen["folder"], $imagen["ubicacionTemporal"], $imagen["nuevoNombre"]);

                }

                $concepto = 'INVENTARIO INICIAL';
                $comprobante = '';

                //REGISTRAMOS KARDEX - INVENTARIO INICIAL
                $stmt = Conexion::conectar()->prepare("call prc_registrar_kardex_existencias(:p_codigo_producto,
                                                                                            :p_concepto,
                                                                                            :p_comprobante,
                                                                                            :p_unidades,
                                                                                            :p_costo_unitario,
                                                                                            :p_costo_total);");

                $stmt->bindParam(":p_codigo_producto", $array_datos_producto["iptCodigoReg"], PDO::PARAM_STR);
                $stmt->bindParam(":p_concepto", $concepto, PDO::PARAM_STR);
                $stmt->bindParam(":p_comprobante", $comprobante, PDO::PARAM_STR);
                $stmt->bindParam(":p_unidades", $array_datos_producto["iptStockReg"], PDO::PARAM_STR);
                $stmt->bindParam(":p_costo_unitario", $array_datos_producto["iptPrecioCompraReg"], PDO::PARAM_STR);
                $stmt->bindParam(":p_costo_total", $costo_total_producto, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    $resultado = "ok";
                } else {
                    $resultado = "error";
                }
            } else {
                $resultado = "error";
            }
        } catch (Exception $e) {
            $resultado = 'Excepción capturada: ' .  $e->getMessage() . "\n";
        }

        return $resultado;

        $stmt = null;
    }

    /*=============================================
    FUNCION GENERICA PARA ACTUALIZAR INFORMACION
    =============================================*/
    static public function mdlAumentarStock($codigo_producto, $nuevo_stock)
    {

        $concepto = 'BONO / REGALO';

        //REGISTRAMOS KARDEX - INVENTARIO INICIAL
        $stmt = Conexion::conectar()->prepare("call prc_registrar_kardex_bono(:p_codigo_producto,
                                                                              :p_concepto,
                                                                              :p_unidades);");

        $stmt->bindParam(":p_codigo_producto", $codigo_producto, PDO::PARAM_STR);
        $stmt->bindParam(":p_concepto", $concepto, PDO::PARAM_STR);
        $stmt->bindParam(":p_unidades", $nuevo_stock, PDO::PARAM_STR);

        if ($stmt->execute()) {

            return "ok";
        } else {

            return Conexion::conectar()->errorInfo();
        }
    }

    /*=============================================
    FUNCION GENERICA PARA ACTUALIZAR INFORMACION
    =============================================*/
    static public function mdlDisminuirStock($codigo_producto, $nuevo_stock)
    {

        $concepto = 'VENCIMIENTO';

        //REGISTRAMOS KARDEX - INVENTARIO INICIAL
        $stmt = Conexion::conectar()->prepare("call prc_registrar_kardex_vencido(:p_codigo_producto,
                                                                              :p_concepto,
                                                                              :p_unidades);");

        $stmt->bindParam(":p_codigo_producto", $codigo_producto, PDO::PARAM_STR);
        $stmt->bindParam(":p_concepto", $concepto, PDO::PARAM_STR);
        $stmt->bindParam(":p_unidades", $nuevo_stock, PDO::PARAM_STR);

        if ($stmt->execute()) {

            return "ok";
        } else {

            return Conexion::conectar()->errorInfo();
        }
    }

    /*=============================================
    FUNCION GENERICA PARA ACTUALIZAR INFORMACION
    =============================================*/
    static public function mdlActualizarInformacion($table, $data, $id, $nameId)
    {

        $set = "";

        foreach ($data as $key => $value) {

            $set .= $key . " = :" . $key . ",";
        }

        $set = substr($set, 0, -1);

        $stmt = Conexion::conectar()->prepare("UPDATE $table SET $set WHERE $nameId = :$nameId");

        foreach ($data as $key => $value) {

            $stmt->bindParam(":" . $key, $data[$key], PDO::PARAM_STR);
        }

        $stmt->bindParam(":" . $nameId, $id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            return "ok";
        } else {

            return Conexion::conectar()->errorInfo();
        }
    }

    /*=============================================
    FUNCION GENERICA PARA ELIMINAR INFORMACION
    =============================================*/
    static public function mdlEliminarInformacion($table, $id, $nameId)
    {

        $stmt = Conexion::conectar()->prepare("DELETE FROM $table WHERE $nameId = :$nameId");

        $stmt->bindParam(":" . $nameId, $id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            return "ok";;
        } else {

            return Conexion::conectar()->errorInfo();
        }
    }

    /*===================================================================
    LISTAR NOMBRE DE PRODUCTOS PARA INPUT DE AUTO COMPLETADO
    ====================================================================*/
    static public function mdlListarNombreProductos()
    {

        $stmt = Conexion::conectar()->prepare(
            "SELECT Concat(codigo_producto , ' / ' ,
                                                             c.nombre_categoria,' / ',
                                                             descripcion_producto, ' - S./ ' , 
                                                             p.precio_venta_producto, ' / Stock: ',
                                                             p.stock_producto)  as descripcion_producto
                                                FROM productos p inner join categorias c on p.id_categoria_producto = c.id_categoria"
        );

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /*===================================================================
    BUSCAR PRODUCTO POR SU CODIGO DE BARRAS
    ====================================================================*/
    static public function mdlGetDatosProducto($codigoProducto)
    {

        $stmt = Conexion::conectar()->prepare("SELECT   codigo_producto,
                                                        c.id_categoria,                                                        
                                                        c.nombre_categoria,
                                                        descripcion_producto,
                                                        '1' as cantidad,
                                                        CONCAT('S./ ',CONVERT(ROUND(precio_venta_producto,2), CHAR)) as precio_venta_producto,
                                                        CONCAT('S./ ',CONVERT(ROUND(1*precio_venta_producto,2), CHAR)) as total,
                                                        '' as acciones,
                                                        c.aplica_peso,
                                                        p.precio_mayor_producto,
													    p.precio_oferta_producto
                                                FROM productos p inner join categorias c on p.id_categoria_producto = c.id_categoria
                                            WHERE codigo_producto = :codigoProducto
                                                AND p.stock_producto > 0");

        $stmt->bindParam(":codigoProducto", $codigoProducto, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /*===================================================================
    VERIFICAR EL STOCK DE UN PRODUCTO
    ====================================================================*/
    static public function mdlVerificaStockProducto($codigo_producto, $cantidad_a_comprar)
    {

        $stmt = Conexion::conectar()->prepare("SELECT   count(*) as existe
                                                    FROM productos p 
                                                   WHERE p.codigo_producto = :codigo_producto
                                                     AND p.stock_producto >= :cantidad_a_comprar");

        $stmt->bindParam(":codigo_producto", $codigo_producto, PDO::PARAM_STR);
        $stmt->bindParam(":cantidad_a_comprar", $cantidad_a_comprar, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    
    public function guardarImagen($folder, $ubicacionTemporal, $nuevoNombre){
        file_put_contents(strtolower($folder.$nuevoNombre), file_get_contents($ubicacionTemporal));
    }


}
