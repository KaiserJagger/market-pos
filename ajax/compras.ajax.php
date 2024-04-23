<?php

$array_datos_proveedor = [];
$array_datos_comprobante = [];


parse_str($_POST['proveedor'], $array_datos_proveedor);
// parse_str($_POST['comprobante'], $array_datos_comprobante);

var_dump($array_datos_proveedor["iptRucProveedor"]);
// var_dump(parse_str($_POST['datos_proveedor'], $array_datos));

// for($i = 0;$i < count($_POST["arr_detalle_compra"]);$i++){
//     var_dump($_POST["arr_detalle_compra"][$i]["codigo_producto"]);
// }