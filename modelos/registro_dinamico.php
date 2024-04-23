$datosPublicacion = array(
						"codigo_agente_publicacion" => $codigo_agente_publicacion,
						"id_tipo_publicacion" => $id_tipo_publicacion,
						"id_tipoInmueble_publicacion" => $id_tipoInmueble_publicacion,
						"id_subTipoInmueble_publicacion" => $id_subTipoInmueble_publicacion,
						"titulo_publicacion" => $titulo_publicacion,
						);
						
/*===================================================================
METODO PARA REGISTRAR PUBLICACIONES
====================================================================*/
static public function mdlRegistrarCategoria($data,$table){

	$columns = "(";
	$params= "(";

	foreach ($data as $key => $value) {
		
		$columns .= $key.",";
		$params .= ":".$key.",";
		
	}

	$columns = substr($columns, 0, -1);
	$params = substr($params, 0, -1);

	$columns .= ")";
	$params .= ")";

	$link = Conexion::conectar();
	$stmt = $link->prepare("INSERT INTO $table $columns VALUES $params");


	foreach ($data as $key => $value) {
		
		$stmt->bindParam(":".$key, $data[$key], PDO::PARAM_STR);
		
	}

	if($stmt->execute()){

		$return = "Se registró la publicación";

		return $return;

	}else{

		return Conexion::conectar()->errorInfo();
	
	}

}