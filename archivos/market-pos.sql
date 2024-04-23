-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-12-2022 a las 16:59:18
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 8.0.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `market-pos`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_ActualizarDetalleVenta` (IN `p_codigo_producto` VARCHAR(20), IN `p_cantidad` FLOAT, IN `p_id` INT)   BEGIN

 declare v_nro_boleta varchar(20);
 declare v_total_venta float;

/*
ACTUALIZAR EL STOCK DEL PRODUCTO QUE SEA MODIFICADO
......
.....
.......
*/

/*
ACTULIZAR CODIGO, CANTIDAD Y TOTAL DEL ITEM MODIFICADO
*/

 UPDATE venta_detalle 
 SET codigo_producto = p_codigo_producto, 
 cantidad = p_cantidad, 
 total_venta = (p_cantidad * (select precio_venta_producto from productos where codigo_producto = p_codigo_producto))
 WHERE id = p_id;
 
 set v_nro_boleta = (select nro_boleta from venta_detalle where id = p_id);
 set v_total_venta = (select sum(total_venta) from venta_detalle where nro_boleta = v_nro_boleta);
 
 update venta_cabecera
   set total_venta = v_total_venta
 where nro_boleta = v_nro_boleta;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_eliminar_venta` (IN `p_nro_boleta` VARCHAR(8))   BEGIN

DECLARE v_codigo VARCHAR(20);
DECLARE v_cantidad FLOAT;
DECLARE done INT DEFAULT FALSE;

DECLARE cursor_i CURSOR FOR 
SELECT codigo_producto,cantidad 
FROM venta_detalle 
where CAST(nro_boleta AS CHAR CHARACTER SET utf8)  = CAST(p_nro_boleta AS CHAR CHARACTER SET utf8) ;

DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

OPEN cursor_i;
read_loop: LOOP
FETCH cursor_i INTO v_codigo, v_cantidad;

	IF done THEN
	  LEAVE read_loop;
	END IF;
    
    UPDATE PRODUCTOS 
       SET stock_producto = stock_producto + v_cantidad
    WHERE CAST(codigo_producto AS CHAR CHARACTER SET utf8) = CAST(v_codigo AS CHAR CHARACTER SET utf8);
    
END LOOP;
CLOSE cursor_i;

DELETE FROM VENTA_DETALLE WHERE CAST(nro_boleta AS CHAR CHARACTER SET utf8) = CAST(p_nro_boleta AS CHAR CHARACTER SET utf8) ;
DELETE FROM VENTA_CABECERA WHERE CAST(nro_boleta AS CHAR CHARACTER SET utf8)  = CAST(p_nro_boleta AS CHAR CHARACTER SET utf8) ;

SELECT 'Se eliminó correctamente la venta';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_ListarCategorias` ()   BEGIN
select * from categorias;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_ListarProductos` ()   SELECT   '' as detalles,
		codigo_producto,
		id_categoria_producto,
		nombre_categoria,
		descripcion_producto,
		ROUND(precio_compra_producto,2) as precio_compra_producto,
		ROUND(precio_venta_producto,2) as precio_venta_producto,
        ROUND(precio_mayor_producto,2) as precio_mayor_producto,
        ROUND(precio_oferta_producto,2) as precio_oferta_producto,
		case when c.aplica_peso = 1 then concat(stock_producto,' Kg(s)')
			else concat(stock_producto,' Und(s)') end as stock_producto,
		case when c.aplica_peso = 1 then concat(minimo_stock_producto,' Kg(s)')
			else concat(minimo_stock_producto,' Und(s)') end as minimo_stock_producto,
		case when c.aplica_peso = 1 then concat(ventas_producto,' Kg(s)') 
			else concat(ventas_producto,' Und(s)') end as ventas_producto,
		ROUND(costo_total_producto,2) as costo_total_producto,
		fecha_creacion_producto,
		fecha_actualizacion_producto,
		'' as acciones
	FROM productos p INNER JOIN categorias c on p.id_categoria_producto = c.id_categoria 
	order by p.codigo_producto desc$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_ListarProductosMasVendidos` ()  NO SQL BEGIN

select  p.codigo_producto,
		p.descripcion_producto,
        sum(vd.cantidad) as cantidad,
        sum(Round(vd.total_venta,2)) as total_venta
from venta_detalle vd inner join productos p on vd.codigo_producto = p.codigo_producto
group by p.codigo_producto,
		p.descripcion_producto
order by  sum(Round(vd.total_venta,2)) DESC
limit 10;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_ListarProductosPocoStock` ()  NO SQL BEGIN
select p.codigo_producto,
		p.descripcion_producto,
        p.stock_producto,
        p.minimo_stock_producto
from productos p
where p.stock_producto <= p.minimo_stock_producto
order by p.stock_producto asc;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_ObtenerDatosDashboard` ()  NO SQL BEGIN
  DECLARE totalProductos int;
  DECLARE totalCompras float;
  DECLARE totalVentas float;
  DECLARE ganancias float;
  DECLARE productosPocoStock int;
  DECLARE ventasHoy float;

  SET totalProductos = (SELECT
      COUNT(*)
    FROM productos p);
  SET totalCompras = (SELECT
      SUM(p.costo_total_producto)
    FROM productos p);
  /*set totalVentas = (select sum(vc.total_venta) from venta_cabecera vc where EXTRACT(MONTH FROM vc.fecha_venta) = EXTRACT(MONTH FROM curdate()) and EXTRACT(YEAR FROM vc.fecha_venta) = EXTRACT(YEAR FROM curdate()));*/
  SET totalVentas = (SELECT
      SUM(vc.total_venta)
    FROM venta_cabecera vc);
  /*set ganancias = (select sum(vd.total_venta - (p.precio_compra_producto * vd.cantidad)) 
  					from venta_detalle vd inner join productos p on vd.codigo_producto = p.codigo_producto
                   where EXTRACT(MONTH FROM vd.fecha_venta) = EXTRACT(MONTH FROM curdate()) 
                   and EXTRACT(YEAR FROM vd.fecha_venta) = EXTRACT(YEAR FROM curdate()));*/
  SET ganancias = (SELECT
      SUM(vd.cantidad * vd.precio_unitario_venta) - SUM(vd.cantidad * vd.costo_unitario_venta)
    FROM venta_detalle VD);
  SET productosPocoStock = (SELECT
      COUNT(1)
    FROM productos p
    WHERE p.stock_producto <= p.minimo_stock_producto);
  SET ventasHoy = (SELECT
      SUM(vc.total_venta)
    FROM venta_cabecera vc
    WHERE DATE(vc.fecha_venta) = CURDATE());

  SELECT
    IFNULL(totalProductos, 0) AS totalProductos,
    IFNULL(CONCAT('S./ ', FORMAT(totalCompras, 2)), 0) AS totalCompras,
    IFNULL(CONCAT('S./ ', FORMAT(totalVentas, 2)), 0) AS totalVentas,
    IFNULL(CONCAT('S./ ', FORMAT(ganancias, 2)), 0) AS ganancias,
    IFNULL(productosPocoStock, 0) AS productosPocoStock,
    IFNULL(CONCAT('S./ ', FORMAT(ventasHoy, 2)), 0) AS ventasHoy;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_obtenerNroBoleta` ()  NO SQL select serie_boleta,
		IFNULL(LPAD(max(c.nro_correlativo_venta)+1,8,'0'),'00000001') nro_venta 
from empresa c$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_ObtenerVentasMesActual` ()  NO SQL BEGIN
SELECT date(vc.fecha_venta) as fecha_venta,
		sum(round(vc.total_venta,2)) as total_venta,
        (SELECT sum(round(vc1.total_venta,2))
			FROM venta_cabecera vc1
		where date(vc1.fecha_venta) >= date(last_day(now() - INTERVAL 2 month) + INTERVAL 1 day)
		and date(vc1.fecha_venta) <= last_day(last_day(now() - INTERVAL 2 month) + INTERVAL 1 day)
        and date(vc1.fecha_venta) = DATE_ADD(vc.fecha_venta, INTERVAL -1 MONTH)
		group by date(vc1.fecha_venta)) as total_venta_ant
FROM venta_cabecera vc
where date(vc.fecha_venta) >= date(last_day(now() - INTERVAL 1 month) + INTERVAL 1 day)
and date(vc.fecha_venta) <= last_day(date(CURRENT_DATE))
group by date(vc.fecha_venta);

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_ObtenerVentasMesAnterior` ()  NO SQL BEGIN
SELECT date(vc.fecha_venta) as fecha_venta,
		sum(round(vc.total_venta,2)) as total_venta,
        sum(round(vc.total_venta,2)) as total_venta_ant
FROM venta_cabecera vc
where date(vc.fecha_venta) >= date(last_day(now() - INTERVAL 2 month) + INTERVAL 1 day)
and date(vc.fecha_venta) <= last_day(last_day(now() - INTERVAL 2 month) + INTERVAL 1 day)
group by date(vc.fecha_venta);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_registrar_kardex_bono` (IN `p_codigo_producto` VARCHAR(20), IN `p_concepto` VARCHAR(100), IN `p_nuevo_stock` FLOAT)   BEGIN

	declare v_unidades_ex float;
	declare v_costo_unitario_ex float;    
	declare v_costo_total_ex float;
    
    declare v_unidades_in float;
	declare v_costo_unitario_in float;    
	declare v_costo_total_in float;
    
	/*OBTENEMOS LAS ULTIMAS EXISTENCIAS DEL PRODUCTO*/
    
    SELECT k.ex_costo_unitario , k.ex_unidades, k.ex_costo_total
    into v_costo_unitario_ex, v_unidades_ex, v_costo_total_ex
    FROM KARDEX K
    WHERE K.CODIGO_PRODUCTO = p_codigo_producto
    ORDER BY ID DESC
    LIMIT 1;
    
    /*SETEAMOS LOS VALORES PARA EL REGISTRO DE INGRESO*/
    SET v_unidades_in = p_nuevo_stock;
    SET v_costo_unitario_in = 0;
    SET v_costo_total_in = v_unidades_in * v_costo_unitario_in;
    
    /*SETEAMOS LAS EXISTENCIAS ACTUALES*/
    SET v_unidades_ex = ROUND(v_unidades_in,2);    
    SET v_costo_total_ex = ROUND(v_costo_total_ex + v_costo_total_in,2);
    
    IF(v_costo_total_ex > 0) THEN
		SET v_costo_unitario_ex = ROUND(v_costo_total_ex/v_unidades_ex,2);
	else
		SET v_costo_unitario_ex = ROUND(0,2);
    END IF;
    
        
	INSERT INTO KARDEX(codigo_producto,
						fecha,
                        concepto,
                        comprobante,
                        in_unidades,
                        in_costo_unitario,
                        in_costo_total,
                        ex_unidades,
                        ex_costo_unitario,
                        ex_costo_total)
				VALUES(p_codigo_producto,
						curdate(),
                        p_concepto,
                        '',
                        v_unidades_in,
                        v_costo_unitario_in,
                        v_costo_total_in,
                        v_unidades_ex,
                        v_costo_unitario_ex,
                        v_costo_total_ex);

	/*ACTUALIZAMOS EL STOCK, EL NRO DE VENTAS DEL PRODUCTO*/
	UPDATE PRODUCTOS 
	SET stock_producto = v_unidades_ex, 
        precio_compra_producto = v_costo_unitario_ex,
        costo_total_producto = v_costo_total_ex
	WHERE codigo_producto = p_codigo_producto ;                      

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_registrar_kardex_existencias` (IN `p_codigo_producto` VARCHAR(25), IN `p_concepto` VARCHAR(100), IN `p_comprobante` VARCHAR(100), IN `p_unidades` FLOAT, IN `p_costo_unitario` FLOAT, IN `p_costo_total` FLOAT)   BEGIN
  INSERT INTO KARDEX (codigo_producto, fecha, concepto, comprobante, ex_unidades, ex_costo_unitario, ex_costo_total)
    VALUES (p_codigo_producto, CURDATE(), p_concepto, p_comprobante, p_unidades, p_costo_unitario, p_costo_total);

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_registrar_kardex_vencido` (IN `p_codigo_producto` VARCHAR(20), IN `p_concepto` VARCHAR(100), IN `p_nuevo_stock` FLOAT)   BEGIN

	declare v_unidades_ex float;
	declare v_costo_unitario_ex float;    
	declare v_costo_total_ex float;
    
    declare v_unidades_out float;
	declare v_costo_unitario_out float;    
	declare v_costo_total_out float;
    
	/*OBTENEMOS LAS ULTIMAS EXISTENCIAS DEL PRODUCTO*/    
    SELECT k.ex_costo_unitario , k.ex_unidades, k.ex_costo_total
    into v_costo_unitario_ex, v_unidades_ex, v_costo_total_ex
    FROM KARDEX K
    WHERE K.CODIGO_PRODUCTO = p_codigo_producto
    ORDER BY ID DESC
    LIMIT 1;
    
    /*SETEAMOS LOS VALORES PARA EL REGISTRO DE SALIDA*/
    SET v_unidades_out = p_nuevo_stock;
    SET v_costo_unitario_out = 0;
    SET v_costo_total_out = v_unidades_out * v_costo_unitario_out;
    
    /*SETEAMOS LAS EXISTENCIAS ACTUALES*/
    SET v_unidades_ex = ROUND(v_unidades_out,2);    
    SET v_costo_total_ex = ROUND(v_costo_total_ex - v_costo_total_out,2);
    
    IF(v_costo_total_ex > 0) THEN
		SET v_costo_unitario_ex = ROUND(v_costo_total_ex/v_unidades_ex,2);
	else
		SET v_costo_unitario_ex = ROUND(0,2);
    END IF;
    
        
	INSERT INTO KARDEX(codigo_producto,
						fecha,
                        concepto,
                        comprobante,
                        out_unidades,
                        out_costo_unitario,
                        out_costo_total,
                        ex_unidades,
                        ex_costo_unitario,
                        ex_costo_total)
				VALUES(p_codigo_producto,
						curdate(),
                        p_concepto,
                        '',
                        v_unidades_out,
                        v_costo_unitario_out,
                        v_costo_total_out,
                        v_unidades_ex,
                        v_costo_unitario_ex,
                        v_costo_total_ex);

	/*ACTUALIZAMOS EL STOCK, EL NRO DE VENTAS DEL PRODUCTO*/
	UPDATE PRODUCTOS 
	SET stock_producto = v_unidades_ex, 
        precio_compra_producto = v_costo_unitario_ex,
        costo_total_producto = v_costo_total_ex
	WHERE codigo_producto = p_codigo_producto ;                      

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_registrar_kardex_venta` (IN `p_codigo_producto` VARCHAR(20), IN `p_fecha` DATE, IN `p_concepto` VARCHAR(100), IN `p_comprobante` VARCHAR(100), IN `p_unidades` FLOAT)   BEGIN

	declare v_unidades_ex float;
	declare v_costo_unitario_ex float;    
	declare v_costo_total_ex float;
    
    declare v_unidades_out float;
	declare v_costo_unitario_out float;    
	declare v_costo_total_out float;
    

	/*OBTENEMOS LAS ULTIMAS EXISTENCIAS DEL PRODUCTO*/
    
    SELECT k.ex_costo_unitario , k.ex_unidades, k.ex_costo_total
    into v_costo_unitario_ex, v_unidades_ex, v_costo_total_ex
    FROM KARDEX K
    WHERE K.CODIGO_PRODUCTO = p_codigo_producto
    ORDER BY ID DESC
    LIMIT 1;
    
    /*SETEAMOS LOS VALORES PARA EL REGISTRO DE SALIDA*/
    SET v_unidades_out = p_unidades;
    SET v_costo_unitario_out = v_costo_unitario_ex;
    SET v_costo_total_out = p_unidades * v_costo_unitario_ex;
    
    /*SETEAMOS LAS EXISTENCIAS ACTUALES*/
    SET v_unidades_ex = ROUND(v_unidades_ex - v_unidades_out,2);    
    SET v_costo_total_ex = ROUND(v_costo_total_ex -  v_costo_total_out,2);
    
    IF(v_costo_total_ex > 0) THEN
		SET v_costo_unitario_ex = ROUND(v_costo_total_ex/v_unidades_ex,2);
	else
		SET v_costo_unitario_ex = ROUND(0,2);
    END IF;
    
        
	INSERT INTO KARDEX(codigo_producto,
						fecha,
                        concepto,
                        comprobante,
                        out_unidades,
                        out_costo_unitario,
                        out_costo_total,
                        ex_unidades,
                        ex_costo_unitario,
                        ex_costo_total)
				VALUES(p_codigo_producto,
						p_fecha,
                        p_concepto,
                        p_comprobante,
                        v_unidades_out,
                        v_costo_unitario_out,
                        v_costo_total_out,
                        v_unidades_ex,
                        v_costo_unitario_ex,
                        v_costo_total_ex);

	/*ACTUALIZAMOS EL STOCK, EL NRO DE VENTAS DEL PRODUCTO*/
	UPDATE PRODUCTOS 
	SET stock_producto = v_unidades_ex, 
		ventas_producto = ventas_producto + v_unidades_out,
        precio_compra_producto = v_costo_unitario_ex,
        costo_total_producto = v_costo_total_ex
	WHERE codigo_producto = p_codigo_producto ;                      

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_registrar_venta_detalle` (IN `p_nro_boleta` VARCHAR(8), IN `p_codigo_producto` VARCHAR(20), IN `p_cantidad` FLOAT, IN `p_total_venta` FLOAT)   BEGIN
declare v_precio_compra float;
declare v_precio_venta float;

SELECT p.precio_compra_producto,p.precio_venta_producto
into v_precio_compra, v_precio_venta
FROM productos p
WHERE p.codigo_producto  = p_codigo_producto;
    
INSERT INTO venta_detalle(nro_boleta,codigo_producto, cantidad, costo_unitario_venta,precio_unitario_venta,total_venta, fecha_venta) 
VALUES(p_nro_boleta,p_codigo_producto,p_cantidad, v_precio_compra, v_precio_venta,p_total_venta,curdate());
                                                        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prc_top_ventas_categorias` ()   BEGIN

select cast(sum(vd.total_venta)  AS DECIMAL(8,2)) as y, c.nombre_categoria as label
    from venta_detalle vd inner join productos p on vd.codigo_producto = p.codigo_producto
                        inner join categorias c on c.id_categoria = p.id_categoria_producto
    group by c.nombre_categoria
    LIMIT 10;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `arqueo_caja`
--

CREATE TABLE `arqueo_caja` (
  `id` int(11) NOT NULL,
  `id_caja` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `monto_inicial` float DEFAULT NULL,
  `ingresos` float DEFAULT NULL,
  `devoluciones` float DEFAULT NULL,
  `gastos` float DEFAULT NULL,
  `monto_final` float DEFAULT NULL,
  `status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajas`
--

CREATE TABLE `cajas` (
  `id` int(11) NOT NULL,
  `numero_caja` int(11) NOT NULL,
  `nombre_caja` varchar(100) NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre_categoria` text DEFAULT NULL,
  `aplica_peso` int(11) NOT NULL,
  `fecha_creacion_categoria` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fecha_actualizacion_categoria` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre_categoria`, `aplica_peso`, `fecha_creacion_categoria`, `fecha_actualizacion_categoria`) VALUES
(3537, 'FRUTAS', 1, '2022-12-21 21:59:46', '2022-12-10'),
(3538, 'VERDURAS', 1, '2022-12-21 21:59:46', '2022-12-10'),
(3539, 'SNACK', 0, '2022-12-21 21:59:46', '2022-12-10'),
(3540, 'AVENA', 0, '2022-12-21 21:59:46', '2022-12-10'),
(3541, 'ENERGIZANTE', 0, '2022-12-21 21:59:46', '2022-12-10'),
(3542, 'JUGO', 0, '2022-12-21 21:59:46', '2022-12-10'),
(3543, 'REFRESCO', 0, '2022-12-21 21:59:46', '2022-12-10'),
(3544, 'MANTEQUILLA', 0, '2022-12-21 21:59:46', '2022-12-10'),
(3545, 'GASEOSA', 0, '2022-12-21 21:59:46', '2022-12-10'),
(3546, 'ACEITE', 0, '2022-12-21 21:59:46', '2022-12-10'),
(3547, 'YOGURT', 0, '2022-12-21 21:59:46', '2022-12-10'),
(3548, 'ARROZ', 0, '2022-12-21 21:59:46', '2022-12-10'),
(3549, 'LECHE', 0, '2022-12-21 21:59:46', '2022-12-10'),
(3550, 'PAPEL HIGIÉNICO', 0, '2022-12-21 21:59:46', '2022-12-10'),
(3551, 'ATÚN', 0, '2022-12-21 21:59:46', '2022-12-10'),
(3552, 'CHOCOLATE', 0, '2022-12-21 21:59:46', '2022-12-10'),
(3553, 'WAFER', 0, '2022-12-21 21:59:46', '2022-12-10'),
(3554, 'GOLOSINA', 0, '2022-12-21 21:59:46', '2022-12-10'),
(3555, 'GALLETAS', 0, '2022-12-21 21:59:46', '2022-12-10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id` int(11) NOT NULL,
  `id_proveedor` int(11) DEFAULT NULL,
  `id_tipo_comprobante` varchar(3) DEFAULT NULL,
  `serie_comprobante` varchar(10) DEFAULT NULL,
  `nro_comprobante` varchar(20) DEFAULT NULL,
  `fecha_comprobante` datetime DEFAULT NULL,
  `id_moneda_comprobante` int(11) DEFAULT NULL,
  `ope_exonerada` float DEFAULT NULL,
  `ope_inafecta` float DEFAULT NULL,
  `ope_gravada` float DEFAULT NULL,
  `igv` float DEFAULT NULL,
  `total_compra` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_compra`
--

CREATE TABLE `detalle_compra` (
  `id` int(11) NOT NULL,
  `id_compra` int(11) DEFAULT NULL,
  `codigo_producto` varchar(20) DEFAULT NULL,
  `cantidad` float DEFAULT NULL,
  `costo_unitario` float DEFAULT NULL,
  `descuento` float DEFAULT NULL,
  `subtotal` float DEFAULT NULL,
  `impuesto` float DEFAULT NULL,
  `total` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

CREATE TABLE `empresa` (
  `id_empresa` int(11) NOT NULL,
  `razon_social` text NOT NULL,
  `ruc` bigint(20) NOT NULL,
  `direccion` text NOT NULL,
  `marca` text NOT NULL,
  `serie_boleta` varchar(4) NOT NULL,
  `nro_correlativo_venta` varchar(8) NOT NULL,
  `email` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `empresa`
--

INSERT INTO `empresa` (`id_empresa`, `razon_social`, `ruc`, `direccion`, `marca`, `serie_boleta`, `nro_correlativo_venta`, `email`) VALUES
(1, 'Maga & Tito Market', 10467291241, 'Avenida Brasil 1347 - Jesus María', 'Maga & Tito Market', 'B001', '00000250', 'magaytito@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `kardex`
--

CREATE TABLE `kardex` (
  `id` int(11) NOT NULL,
  `codigo_producto` varchar(20) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `concepto` varchar(100) DEFAULT NULL,
  `comprobante` varchar(50) DEFAULT NULL,
  `in_unidades` float DEFAULT NULL,
  `in_costo_unitario` float DEFAULT NULL,
  `in_costo_total` float DEFAULT NULL,
  `out_unidades` float DEFAULT NULL,
  `out_costo_unitario` float DEFAULT NULL,
  `out_costo_total` float DEFAULT NULL,
  `ex_unidades` float DEFAULT NULL,
  `ex_costo_unitario` float DEFAULT NULL,
  `ex_costo_total` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `kardex`
--

INSERT INTO `kardex` (`id`, `codigo_producto`, `fecha`, `concepto`, `comprobante`, `in_unidades`, `in_costo_unitario`, `in_costo_total`, `out_unidades`, `out_costo_unitario`, `out_costo_total`, `ex_unidades`, `ex_costo_unitario`, `ex_costo_total`) VALUES
(5290, '7755139002890', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 24, 5.9, 141.6),
(5291, '7755139002903', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 23, 12.1, 278.3),
(5292, '7755139002904', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 29, 12.4, 359.6),
(5293, '7755139002870', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 26, 3.25, 84.5),
(5294, '7755139002880', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 23, 5.15, 118.45),
(5295, '7755139002902', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 29, 9.8, 284.2),
(5296, '7755139002898', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 27, 7.49, 202.23),
(5297, '7755139002899', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 26, 8, 208),
(5298, '7755139002901', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 26, 10, 260),
(5299, '7755139002810', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 21, 3.79, 79.59),
(5300, '7755139002878', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 25, 3.99, 99.75),
(5301, '7755139002838', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 27, 1.29, 34.83),
(5302, '7755139002839', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 27, 1, 27),
(5303, '7755139002848', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 25, 1.9, 47.5),
(5304, '7755139002863', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 27, 2.8, 75.6),
(5305, '7755139002864', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 20, 4.4, 88),
(5306, '7755139002865', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 23, 3.79, 87.17),
(5307, '7755139002866', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 26, 3.79, 98.54),
(5308, '7755139002867', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 24, 3.65, 87.6),
(5309, '7755139002868', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 20, 3.5, 70),
(5310, '7755139002871', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 27, 3.17, 85.59),
(5311, '7755139002877', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 30, 5.17, 155.1),
(5312, '7755139002879', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 28, 4.58, 128.24),
(5313, '7755139002881', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 22, 5, 110),
(5314, '7755139002882', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 27, 4.66, 125.82),
(5315, '7755139002883', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 23, 4.65, 106.95),
(5316, '7755139002884', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 21, 4.63, 97.23),
(5317, '7755139002885', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 27, 5.7, 153.9),
(5318, '7755139002887', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 27, 6.08, 164.16),
(5319, '7755139002888', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 22, 5.9, 129.8),
(5320, '7755139002889', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 28, 5.9, 165.2),
(5321, '7755139002891', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 29, 5.9, 171.1),
(5322, '7755139002892', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 21, 5.08, 106.68),
(5323, '7755139002893', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 29, 5.63, 163.27),
(5324, '7755139002895', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 29, 5.9, 171.1),
(5325, '7755139002896', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 27, 5.9, 159.3),
(5326, '7755139002897', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 22, 5.33, 117.26),
(5327, '7755139002900', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 21, 8.9, 186.9),
(5328, '7755139002886', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 21, 5.7, 119.7),
(5329, '7755139002809', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 21, 18.29, 384.09),
(5330, '7755139002874', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 28, 2.8, 78.4),
(5331, '7755139002830', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 20, 1, 20),
(5332, '7755139002869', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 21, 3.25, 68.25),
(5333, '7755139002872', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 30, 3.1, 93),
(5334, '7755139002876', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 21, 3.39, 71.19),
(5335, '7755139002852', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 20, 1.3, 26),
(5336, '7755139002853', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 28, 1.99, 55.72),
(5337, '7755139002840', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 29, 1, 29),
(5338, '7755139002894', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 23, 5.4, 124.2),
(5339, '7755139002814', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 25, 0.53, 13.25),
(5340, '7755139002831', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 23, 0.9, 20.7),
(5341, '7755139002832', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 25, 0.9, 22.5),
(5342, '7755139002835', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 30, 0.67, 20.1),
(5343, '7755139002846', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 22, 1.39, 30.58),
(5344, '7755139002847', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 30, 1.39, 41.7),
(5345, '7755139002850', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 21, 1.39, 29.19),
(5346, '7755139002851', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 25, 1.39, 34.75),
(5347, '7755139002854', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 21, 2.8, 58.8),
(5348, '7755139002855', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 22, 2.6, 57.2),
(5349, '7755139002856', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 24, 2.6, 62.4),
(5350, '7755139002857', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 24, 2.19, 52.56),
(5351, '7755139002861', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 28, 2.19, 61.32),
(5352, '7755139002811', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 25, 3.4, 85),
(5353, '7755139002812', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 28, 0.5, 14),
(5354, '7755139002833', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 24, 0.88, 21.12),
(5355, '7755139002837', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 24, 1.5, 36),
(5356, '7755139002815', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 29, 0.37, 10.73),
(5357, '7755139002817', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 21, 0.68, 14.28),
(5358, '7755139002822', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 24, 0.52, 12.48),
(5359, '7755139002823', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 20, 0.52, 10.4),
(5360, '7755139002824', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 23, 0.52, 11.96),
(5361, '7755139002826', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 27, 0.47, 12.69),
(5362, '7755139002827', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 24, 0.47, 11.28),
(5363, '7755139002828', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 29, 0.47, 13.63),
(5364, '7755139002842', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 29, 0.9, 26.1),
(5365, '7755139002818', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 24, 0.62, 14.88),
(5366, '7755139002836', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 22, 0.56, 12.32),
(5367, '7755139002825', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 25, 0.5, 12.5),
(5368, '7755139002849', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 28, 1.8, 50.4),
(5369, '7755139002875', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 22, 3.69, 81.18),
(5370, '7755139002860', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 27, 2.8, 75.6),
(5371, '7755139002813', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 22, 0.33, 7.26),
(5372, '7755139002816', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 20, 0.43, 8.6),
(5373, '7755139002829', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 29, 0.75, 21.75),
(5374, '7755139002819', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 28, 0.6, 16.8),
(5375, '7755139002834', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 21, 0.85, 17.85),
(5376, '7755139002841', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 26, 0.92, 23.92),
(5377, '7755139002843', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 23, 1.06, 24.38),
(5378, '7755139002844', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 26, 1.5, 39),
(5379, '7755139002845', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 21, 1.5, 31.5),
(5380, '7755139002858', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 23, 2.6, 59.8),
(5381, '7755139002859', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 21, 3, 63),
(5382, '7755139002862', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 26, 3.2, 83.2),
(5383, '7755139002873', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 25, 2.89, 72.25),
(5384, '7755139002820', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 21, 0.57, 11.97),
(5385, '7755139002821', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 22, 0.53, 11.66),
(5386, '7755139002869', '2022-12-10 00:00:00', 'VENTA', '00000218', NULL, NULL, NULL, 3, 3.25, 9.75, 18, 3.25, 58.5),
(5387, '7755139002818', '2022-12-10 00:00:00', 'VENTA', '00000219', NULL, NULL, NULL, 1, 0.62, 0.62, 23, 0.62, 14.26),
(5388, '7755139002902', '2022-12-10 00:00:00', 'VENTA', '00000219', NULL, NULL, NULL, 1, 9.8, 9.8, 28, 9.8, 274.4),
(5389, '7755139002830', '2022-12-10 00:00:00', 'VENTA', '00000219', NULL, NULL, NULL, 1, 1, 1, 19, 1, 19),
(5390, 'FR-3515456', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 10, 3.5, 35),
(5391, 'FR-3515456', '2022-12-10 00:00:00', 'VENTA', '00000220', NULL, NULL, NULL, 0.5, 3.5, 1.75, 9.5, 3.5, 33.25),
(5392, 'VR-545485', '2022-12-09 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 5, 2, 10),
(5393, 'FR-3515456', '2022-12-10 00:00:00', 'VENTA', '00000221', NULL, NULL, NULL, 0.25, 3.5, 0.875, 9.25, 3.5, 32.38),
(5394, '7755139002811', '2022-12-10 00:00:00', 'VENTA', '00000222', NULL, NULL, NULL, 1, 3.4, 3.4, 24, 3.4, 81.6),
(5395, '7755139002902', '2022-12-10 00:00:00', 'VENTA', '00000222', NULL, NULL, NULL, 1, 9.8, 9.8, 27, 9.8, 264.6),
(5396, 'FR-3515456', '2022-12-10 00:00:00', 'VENTA', '00000222', NULL, NULL, NULL, 1, 3.5, 3.5, 8.25, 3.5, 28.88),
(5397, '7755139002835', '2022-12-11 00:00:00', 'VENTA', '00000223', NULL, NULL, NULL, 3, 0.67, 2.01, 27, 0.67, 18.09),
(5398, 'FR-3515456', '2022-12-11 00:00:00', 'VENTA', '00000224', NULL, NULL, NULL, 1, 3.5, 3.5, 7.25, 3.5, 25.38),
(5399, '7755139002835', '2022-12-11 00:00:00', 'VENTA', '00000225', NULL, NULL, NULL, 1, 0.67, 0.67, 26, 0.67, 17.42),
(5400, 'FR-3515456', '2022-12-12 00:00:00', 'VENTA', '00000226', NULL, NULL, NULL, 1, 3.5, 3.5, 6.25, 3.5, 21.88),
(5401, '7755139002869', '2022-12-12 00:00:00', 'VENTA', '00000226', NULL, NULL, NULL, 1, 3.25, 3.25, 17, 3.25, 55.25),
(5402, '7755139002809', '2022-12-12 00:00:00', 'VENTA', '00000227', NULL, NULL, NULL, 5, 18.29, 91.45, 16, 18.29, 292.64),
(5403, '7755139002843', '2022-12-12 00:00:00', 'VENTA', '00000228', NULL, NULL, NULL, 10, 1.06, 10.6, 13, 1.06, 13.78),
(5404, 'FR-45468785', '2022-12-11 00:00:00', 'INVENTARIO INICIAL', '', NULL, NULL, NULL, NULL, NULL, NULL, 10, 3, 30),
(5405, 'FR-45468785', '2022-12-12 00:00:00', 'VENTA', '00000229', NULL, NULL, NULL, 0.25, 3, 0.75, 9.75, 3, 29.25),
(5406, '7755139002896', '2022-12-12 00:00:00', 'VENTA', '00000230', NULL, NULL, NULL, 1, 5.9, 5.9, 26, 5.9, 153.4),
(5407, '7755139002858', '2022-12-12 00:00:00', 'VENTA', '00000230', NULL, NULL, NULL, 1, 2.6, 2.6, 22, 2.6, 57.2),
(5408, '7755139002902', '2022-12-12 00:00:00', 'VENTA', '00000230', NULL, NULL, NULL, 1, 9.8, 9.8, 26, 9.8, 254.8),
(5409, '7755139002868', '2022-12-12 00:00:00', 'VENTA', '00000231', NULL, NULL, NULL, 3, 3.5, 10.5, 17, 3.5, 59.5),
(5410, '7755139002867', '2022-12-12 00:00:00', 'VENTA', '00000231', NULL, NULL, NULL, 1, 3.65, 3.65, 23, 3.65, 83.95),
(5411, '7755139002869', '2022-12-12 00:00:00', 'VENTA', '00000231', NULL, NULL, NULL, 1, 3.25, 3.25, 16, 3.25, 52),
(5412, '7755139002869', '2022-12-13 00:00:00', 'VENTA', '00000232', NULL, NULL, NULL, 1, 3.25, 3.25, 15, 3.25, 48.75),
(5413, '7755139002825', '2022-12-15 00:00:00', 'VENTA', '00000233', NULL, NULL, NULL, 1, 0.5, 0.5, 24, 0.5, 12),
(5414, '7755139002813', '2022-12-15 00:00:00', 'VENTA', '00000233', NULL, NULL, NULL, 1, 0.33, 0.33, 21, 0.33, 6.93),
(5415, '7755139002896', '2022-12-15 00:00:00', 'VENTA', '00000233', NULL, NULL, NULL, 1, 5.9, 5.9, 25, 5.9, 147.5),
(5416, '7755139002869', '2022-12-15 00:00:00', 'VENTA', '00000233', NULL, NULL, NULL, 1, 3.25, 3.25, 14, 3.25, 45.5),
(5417, '7755139002902', '2022-12-15 00:00:00', 'VENTA', '00000233', NULL, NULL, NULL, 1, 9.8, 9.8, 25, 9.8, 245),
(5418, '7755139002809', '2022-12-15 00:00:00', 'VENTA', '00000234', NULL, NULL, NULL, 1, 18.29, 18.29, 15, 18.29, 274.35),
(5419, '7755139002826', '2022-12-15 00:00:00', 'VENTA', '00000234', NULL, NULL, NULL, 1, 0.47, 0.47, 26, 0.47, 12.22),
(5420, '7755139002811', '2022-12-15 00:00:00', 'VENTA', '00000234', NULL, NULL, NULL, 1, 3.4, 3.4, 23, 3.4, 78.2),
(5421, '7755139002844', '2022-12-15 00:00:00', 'VENTA', '00000234', NULL, NULL, NULL, 1, 1.5, 1.5, 25, 1.5, 37.5),
(5422, '7755139002902', '2022-12-17 00:00:00', 'VENTA', '00000235', NULL, NULL, NULL, 1, 9.8, 9.8, 24, 9.8, 235.2),
(5423, '7755139002869', '2022-12-17 00:00:00', 'VENTA', '00000235', NULL, NULL, NULL, 1, 3.25, 3.25, 13, 3.25, 42.25),
(5424, '7755139002830', '2022-12-17 00:00:00', 'VENTA', '00000235', NULL, NULL, NULL, 1, 1, 1, 18, 1, 18),
(5425, '7755139002858', '2022-12-17 00:00:00', 'VENTA', '00000235', NULL, NULL, NULL, 1, 2.6, 2.6, 21, 2.6, 54.6),
(5426, 'VR-545485', '2022-12-17 00:00:00', 'VENTA', '00000235', NULL, NULL, NULL, 1, 2, 2, 4, 2, 8),
(5427, '7755139002844', '2022-12-17 00:00:00', 'VENTA', '00000236', NULL, NULL, NULL, 5, 1.5, 7.5, 20, 1.5, 30),
(5428, '7755139002831', '2022-12-17 00:00:00', 'VENTA', '00000236', NULL, NULL, NULL, 5, 0.9, 4.5, 18, 0.9, 16.2),
(5429, '7755139002869', '2022-12-17 00:00:00', 'VENTA', '00000237', NULL, NULL, NULL, 1, 3.25, 3.25, 12, 3.25, 39),
(5430, '7755139002811', '2022-12-17 00:00:00', 'VENTA', '00000237', NULL, NULL, NULL, 1, 3.4, 3.4, 22, 3.4, 74.8),
(5431, '7755139002859', '2022-12-17 00:00:00', 'VENTA', '00000238', NULL, NULL, NULL, 1, 3, 3, 20, 3, 60),
(5432, 'VR-545485', '2022-12-17 00:00:00', 'VENTA', '00000238', NULL, NULL, NULL, 0.25, 2, 0.5, 3.75, 2, 7.5),
(5433, '7755139002843', '2022-12-17 00:00:00', 'BONO / REGALO', '', 18, 0, 0, NULL, NULL, NULL, 18, 0.77, 13.78),
(5434, '7755139002889', '2022-12-20 00:00:00', 'VENTA', '00000239', NULL, NULL, NULL, 6, 5.9, 35.4, 22, 5.9, 129.8),
(5435, '7755139002869', '2022-12-20 00:00:00', 'VENTA', '00000239', NULL, NULL, NULL, 1, 3.25, 3.25, 11, 3.25, 35.75),
(5436, '7755139002862', '2022-12-20 00:00:00', 'VENTA', '00000239', NULL, NULL, NULL, 5, 3.2, 16, 21, 3.2, 67.2),
(5437, '7755139002837', '2022-12-20 00:00:00', 'VENTA', '00000239', NULL, NULL, NULL, 1, 1.5, 1.5, 23, 1.5, 34.5),
(5438, '7755139002809', '2022-12-21 00:00:00', 'VENTA', '00000240', NULL, NULL, NULL, 4, 18.29, 73.16, 11, 18.29, 201.19),
(5439, '7755139002896', '2022-12-21 00:00:00', 'VENTA', '00000241', NULL, NULL, NULL, 2, 5.9, 11.8, 23, 5.9, 135.7),
(5440, '7755139002814', '2022-12-22 00:00:00', 'VENTA', '00000242', NULL, NULL, NULL, 10, 0.53, 5.3, 15, 0.53, 7.95),
(5441, '7755139002836', '2022-12-22 00:00:00', 'VENTA', '00000242', NULL, NULL, NULL, 10, 0.56, 5.6, 12, 0.56, 6.72),
(5442, '7755139002827', '2022-12-22 00:00:00', 'VENTA', '00000242', NULL, NULL, NULL, 10, 0.47, 4.7, 14, 0.47, 6.58),
(5443, '7755139002815', '2022-12-22 00:00:00', 'VENTA', '00000242', NULL, NULL, NULL, 10, 0.37, 3.7, 19, 0.37, 7.03),
(5444, '7755139002813', '2022-12-22 00:00:00', 'VENTA', '00000242', NULL, NULL, NULL, 10, 0.33, 3.3, 11, 0.33, 3.63),
(5445, '7755139002900', '2022-12-22 00:00:00', 'VENTA', '00000243', NULL, NULL, NULL, 3, 8.9, 26.7, 18, 8.9, 160.2),
(5446, '7755139002809', '2022-12-22 00:00:00', 'VENTA', '00000243', NULL, NULL, NULL, 3, 18.29, 54.87, 8, 18.29, 146.32),
(5447, '7755139002902', '2022-12-22 00:00:00', 'VENTA', '00000243', NULL, NULL, NULL, 3, 9.8, 29.4, 21, 9.8, 205.8),
(5448, '7755139002855', '2022-12-22 00:00:00', 'VENTA', '00000244', NULL, NULL, NULL, 10, 2.6, 26, 12, 2.6, 31.2),
(5449, '7755139002809', '2022-12-22 00:00:00', 'BONO / REGALO', '', 11, 0, 0, NULL, NULL, NULL, 11, 13.3, 146.32),
(5450, '7755139002809', '2022-12-22 00:00:00', 'VENCIMIENTO', '', NULL, NULL, NULL, 8, 0, 0, 8, 18.29, 146.32),
(5451, '7755139002809', '2022-12-22 00:00:00', 'BONO / REGALO', '', 13, 0, 0, NULL, NULL, NULL, 13, 11.26, 146.32),
(5452, '7755139002809', '2022-12-22 00:00:00', 'VENCIMIENTO', '', NULL, NULL, NULL, 8, 0, 0, 8, 18.29, 146.32),
(5453, '7755139002809', '2022-12-22 00:00:00', 'BONO / REGALO', '', 12, 0, 0, NULL, NULL, NULL, 12, 12.19, 146.32),
(5454, '7755139002901', '2022-12-23 00:00:00', 'VENTA', '00000245', NULL, NULL, NULL, 15, 10, 150, 11, 10, 110),
(5455, '7755139002809', '2022-12-23 00:00:00', 'VENTA', '00000246', NULL, NULL, NULL, 7, 12.19, 85.33, 5, 12.2, 60.99),
(5456, '7755139002843', '2022-12-26 00:00:00', 'VENTA', '00000247', NULL, NULL, NULL, 10, 0.77, 7.7, 8, 0.76, 6.08),
(5457, '7755139002841', '2022-12-26 00:00:00', 'VENTA', '00000247', NULL, NULL, NULL, 10, 0.92, 9.2, 16, 0.92, 14.72),
(5458, '7755139002849', '2022-12-26 00:00:00', 'VENTA', '00000247', NULL, NULL, NULL, 10, 1.8, 18, 18, 1.8, 32.4),
(5459, '7755139002869', '2022-12-26 00:00:00', 'VENTA', '00000248', NULL, NULL, NULL, 1, 3.25, 3.25, 10, 3.25, 32.5),
(5460, '7755139002843', '2022-12-26 00:00:00', 'VENTA', '00000249', NULL, NULL, NULL, 1, 0.76, 0.76, 7, 0.76, 5.32),
(5461, 'VR-545485', '2022-12-29 00:00:00', 'VENTA', '00000250', NULL, NULL, NULL, 0.25, 2, 0.5, 3.5, 2, 7),
(5462, '7755139002855', '2022-12-29 00:00:00', 'VENTA', '00000250', NULL, NULL, NULL, 10, 2.6, 26, 2, 2.6, 5.2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `id` int(11) NOT NULL,
  `modulo` varchar(45) DEFAULT NULL,
  `padre_id` int(11) DEFAULT NULL,
  `vista` varchar(45) DEFAULT NULL,
  `icon_menu` varchar(45) DEFAULT NULL,
  `orden` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`id`, `modulo`, `padre_id`, `vista`, `icon_menu`, `orden`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Tablero Principal', 0, 'dashboard.php', 'fas fa-tachometer-alt', 0, NULL, NULL),
(2, 'Ventas', 0, '', 'fas fa-store-alt', 1, NULL, NULL),
(3, 'Punto de Venta', 2, 'ventas.php', 'far fa-circle', 2, NULL, NULL),
(4, 'Administrar Ventas', 2, 'administrar_ventas.php', 'far fa-circle', 3, NULL, NULL),
(5, 'Productos', 0, NULL, 'fas fa-cart-plus', 4, NULL, NULL),
(6, 'Inventario', 5, 'productos.php', 'far fa-circle', 5, NULL, NULL),
(7, 'Carga Masiva', 5, 'carga_masiva_productos.php', 'far fa-circle', 6, NULL, NULL),
(8, 'Categorías', 5, 'categorias.php', 'far fa-circle', 7, NULL, NULL),
(9, 'Compras', 0, 'compras.php', 'fas fa-dolly', 9, NULL, NULL),
(10, 'Reportes', 0, 'reportes.php', 'fas fa-chart-line', 10, NULL, NULL),
(11, 'Configuración', 0, 'configuracion.php', 'fas fa-cogs', 11, NULL, NULL),
(12, 'Usuarios', 0, 'usuarios.php', 'fas fa-users', 12, NULL, NULL),
(13, 'Roles y Perfiles', 0, 'modulos_perfiles.php', 'fas fa-tablet-alt', 13, NULL, NULL),
(15, 'Caja', 0, 'caja.php', 'fas fa-cash-register', 8, '2022-12-05 09:44:08', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `monedas`
--

CREATE TABLE `monedas` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perfiles`
--

CREATE TABLE `perfiles` (
  `id_perfil` int(11) NOT NULL,
  `descripcion` varchar(45) DEFAULT NULL,
  `estado` tinyint(4) DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `perfiles`
--

INSERT INTO `perfiles` (`id_perfil`, `descripcion`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Administrador', 1, NULL, NULL),
(2, 'Vendedor', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perfil_modulo`
--

CREATE TABLE `perfil_modulo` (
  `idperfil_modulo` int(11) NOT NULL,
  `id_perfil` int(11) DEFAULT NULL,
  `id_modulo` int(11) DEFAULT NULL,
  `vista_inicio` tinyint(4) DEFAULT NULL,
  `estado` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `perfil_modulo`
--

INSERT INTO `perfil_modulo` (`idperfil_modulo`, `id_perfil`, `id_modulo`, `vista_inicio`, `estado`) VALUES
(13, 1, 13, NULL, 1),
(79, 2, 1, 0, 1),
(80, 2, 3, 1, 1),
(81, 2, 2, 0, 1),
(82, 2, 4, 0, 1),
(83, 2, 10, 0, 1),
(84, 2, 15, 0, 1),
(97, 1, 1, 1, 1),
(98, 1, 3, 0, 1),
(99, 1, 2, 0, 1),
(100, 1, 4, 0, 1),
(101, 1, 6, 0, 1),
(102, 1, 5, 0, 1),
(103, 1, 7, 0, 1),
(104, 1, 8, 0, 1),
(105, 1, 9, 0, 1),
(106, 1, 10, 0, 1),
(107, 1, 11, 0, 1),
(108, 1, 12, 0, 1),
(109, 1, 15, 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `codigo_producto` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `id_categoria_producto` int(11) DEFAULT NULL,
  `descripcion_producto` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `precio_compra_producto` float NOT NULL,
  `precio_venta_producto` float NOT NULL,
  `precio_mayor_producto` float DEFAULT NULL,
  `precio_oferta_producto` float DEFAULT NULL,
  `stock_producto` float DEFAULT NULL,
  `minimo_stock_producto` float DEFAULT NULL,
  `ventas_producto` float DEFAULT NULL,
  `costo_total_producto` float DEFAULT NULL,
  `fecha_creacion_producto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fecha_actualizacion_producto` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`codigo_producto`, `id_categoria_producto`, `descripcion_producto`, `precio_compra_producto`, `precio_venta_producto`, `precio_mayor_producto`, `precio_oferta_producto`, `stock_producto`, `minimo_stock_producto`, `ventas_producto`, `costo_total_producto`, `fecha_creacion_producto`, `fecha_actualizacion_producto`) VALUES
('7755139002809', 3548, 'PAISANA EXTRA 5K', 12.2, 22.8625, 21.948, 21.3993, 5, 11, 20, 60.99, '2022-12-23 13:55:34', NULL),
('7755139002810', 3547, 'GLORIA FRESA 500ML', 3.79, 4.7375, 4.548, 4.4343, 21, 11, 0, 79.59, '2022-12-21 21:59:04', NULL),
('7755139002811', 3549, 'GLORIA EVAPORADA LIGTH 400G', 3.4, 4.25, 4.08, 3.978, 22, 15, 3, 74.8, '2022-12-21 21:59:04', NULL),
('7755139002812', 3555, 'SODA SAN JORGE 40G', 0.5, 0.625, 0.6, 0.585, 28, 18, 0, 14, '2022-12-21 21:59:04', NULL),
('7755139002813', 3555, 'VAINILLA FIELD 37G', 0.33, 0.4125, 0.396, 0.3861, 11, 12, 11, 3.63, '2022-12-22 01:44:24', NULL),
('7755139002814', 3555, 'MARGARITA', 0.53, 0.6625, 0.636, 0.6201, 15, 15, 10, 7.95, '2022-12-22 01:44:24', NULL),
('7755139002815', 3555, 'SODA FIELD 34G', 0.37, 0.4625, 0.444, 0.4329, 19, 19, 10, 7.03, '2022-12-22 01:44:24', NULL),
('7755139002816', 3555, 'RITZ ORIGINAL', 0.43, 0.5375, 0.516, 0.5031, 20, 10, 0, 8.6, '2022-12-21 21:59:04', NULL),
('7755139002817', 3555, 'RITZ QUESO 34G', 0.68, 0.85, 0.816, 0.7956, 21, 11, 0, 14.28, '2022-12-21 21:59:04', NULL),
('7755139002818', 3552, 'CHOCOBUM', 0.62, 0.775, 0.744, 0.7254, 23, 14, 1, 14.26, '2022-12-21 21:59:04', NULL),
('7755139002819', 3555, 'PICARAS', 0.6, 0.75, 0.72, 0.702, 28, 18, 0, 16.8, '2022-12-21 21:59:04', NULL),
('7755139002820', 3555, 'OREO ORIGINAL 36G', 0.57, 0.7125, 0.684, 0.6669, 21, 11, 0, 11.97, '2022-12-21 21:59:04', NULL),
('7755139002821', 3555, 'CLUB SOCIAL 26G', 0.53, 0.6625, 0.636, 0.6201, 22, 12, 0, 11.66, '2022-12-21 21:59:04', NULL),
('7755139002822', 3555, 'FRAC VANILLA 45.5G', 0.52, 0.65, 0.624, 0.6084, 24, 14, 0, 12.48, '2022-12-21 21:59:04', NULL),
('7755139002823', 3555, 'FRAC CHOCOLATE 45.5G', 0.52, 0.65, 0.624, 0.6084, 20, 10, 0, 10.4, '2022-12-21 21:59:04', NULL),
('7755139002824', 3555, 'FRAC CHASICA 45.5G', 0.52, 0.65, 0.624, 0.6084, 23, 13, 0, 11.96, '2022-12-21 21:59:04', NULL),
('7755139002825', 3552, 'TUYO 22G', 0.5, 0.625, 0.6, 0.585, 24, 15, 1, 12, '2022-12-21 21:59:04', NULL),
('7755139002826', 3555, 'GN RELLENITAS 36G CHOCOLATE', 0.47, 0.5875, 0.564, 0.5499, 26, 17, 1, 12.22, '2022-12-21 21:59:04', NULL),
('7755139002827', 3555, 'GN RELLENITAS 36G COCO', 0.47, 0.5875, 0.564, 0.5499, 14, 14, 10, 6.58, '2022-12-22 01:44:24', NULL),
('7755139002828', 3555, 'GN RELLENITAS 36G COCO', 0.47, 0.5875, 0.564, 0.5499, 29, 19, 0, 13.63, '2022-12-21 21:59:04', NULL),
('7755139002829', 3552, 'CANCUN', 0.75, 0.9375, 0.9, 0.8775, 29, 19, 0, 21.75, '2022-12-21 21:59:04', NULL),
('7755139002830', 3545, 'BIG COLA 400ML', 1, 1.25, 1.2, 1.17, 18, 10, 2, 18, '2022-12-21 21:59:04', NULL),
('7755139002831', 3543, 'ZUKO PIÑA', 0.9, 1.125, 1.08, 1.053, 18, 13, 5, 16.2, '2022-12-21 21:59:04', NULL),
('7755139002832', 3543, 'ZUKO DURAZNO', 0.9, 1.125, 1.08, 1.053, 25, 15, 0, 22.5, '2022-12-21 21:59:04', NULL),
('7755139002833', 3552, 'CHIN CHIN 32G', 0.88, 1.1, 1.056, 1.0296, 24, 14, 0, 21.12, '2022-12-21 21:59:04', NULL),
('7755139002834', 3555, 'MOROCHA 30G', 0.85, 1.0625, 1.02, 0.9945, 21, 11, 0, 17.85, '2022-12-21 21:59:04', NULL),
('7755139002835', 3543, 'ZUKO EMOLIENTE', 0.67, 0.8375, 0.804, 0.7839, 26, 20, 4, 17.42, '2022-12-21 21:59:04', NULL),
('7755139002836', 3555, 'CHOCO DONUTS', 0.56, 0.7, 0.672, 0.6552, 12, 12, 10, 6.72, '2022-12-22 01:44:24', NULL),
('7755139002837', 3545, 'PEPSI 355ML', 1.5, 1.875, 1.8, 1.755, 23, 14, 1, 34.5, '2022-12-21 21:59:04', NULL),
('7755139002838', 3540, 'QUAKER 120GR', 1.29, 1.6125, 1.548, 1.5093, 27, 17, 0, 34.83, '2022-12-21 21:59:04', NULL),
('7755139002839', 3542, 'PULP DURAZNO 315ML', 1, 1.25, 1.2, 1.17, 27, 17, 0, 27, '2022-12-21 21:59:04', NULL),
('7755139002840', 3555, 'MOROCHAS WAFER 37G', 1, 1.25, 1.2, 1.17, 29, 19, 0, 29, '2022-12-21 21:59:04', NULL),
('7755139002841', 3552, 'WAFER SUBLIME', 0.92, 1.15, 1.104, 1.0764, 16, 16, 10, 14.72, '2022-12-26 18:11:26', NULL),
('7755139002842', 3555, 'HONY BRAN 33G', 0.9, 1.125, 1.08, 1.053, 29, 19, 0, 26.1, '2022-12-21 21:59:04', NULL),
('7755139002843', 3552, 'SUBLIME CLÁSICO', 0.76, 1.325, 1.272, 1.2402, 7, 13, 21, 5.32, '2022-12-26 18:12:46', NULL),
('7755139002844', 3547, 'GLORIA FRESA 180ML', 1.5, 1.875, 1.8, 1.755, 20, 16, 6, 30, '2022-12-21 21:59:04', NULL),
('7755139002845', 3547, 'GLORIA DURAZNO 180ML', 1.5, 1.875, 1.8, 1.755, 21, 11, 0, 31.5, '2022-12-21 21:59:04', NULL),
('7755139002846', 3547, 'FRUTADO FRESA VASITO', 1.39, 1.7375, 1.668, 1.6263, 22, 12, 0, 30.58, '2022-12-21 21:59:04', NULL),
('7755139002847', 3547, 'FRUTADO DURAZNO VASITO', 1.39, 1.7375, 1.668, 1.6263, 30, 20, 0, 41.7, '2022-12-21 21:59:04', NULL),
('7755139002848', 3540, '3 OSITOS QUINUA', 1.9, 2.375, 2.28, 2.223, 25, 15, 0, 47.5, '2022-12-21 21:59:04', NULL),
('7755139002849', 3545, 'SEVEN UP 500ML', 1.8, 2.25, 2.16, 2.106, 18, 18, 10, 32.4, '2022-12-26 18:11:26', NULL),
('7755139002850', 3545, 'FANTA KOLA INGLESA 500ML', 1.39, 1.7375, 1.668, 1.6263, 21, 11, 0, 29.19, '2022-12-21 21:59:04', NULL),
('7755139002851', 3545, 'FANTA NARANJA 500ML', 1.39, 1.7375, 1.668, 1.6263, 25, 15, 0, 34.75, '2022-12-21 21:59:04', NULL),
('7755139002852', 3550, 'NOBLE PQ 2 UNID', 1.3, 1.625, 1.56, 1.521, 20, 10, 0, 26, '2022-12-21 21:59:04', NULL),
('7755139002853', 3550, 'SUAVE PQ 2 UNID', 1.99, 2.4875, 2.388, 2.3283, 28, 18, 0, 55.72, '2022-12-21 21:59:04', NULL),
('7755139002854', 3545, 'PEPSI 750ML', 2.8, 3.5, 3.36, 3.276, 21, 11, 0, 58.8, '2022-12-21 21:59:04', NULL),
('7755139002855', 3545, 'COCA COLA 600ML', 2.6, 3.25, 3.12, 3.042, 2, 12, 20, 5.2, '2022-12-29 13:07:56', NULL),
('7755139002856', 3545, 'INCA KOLA 600ML', 2.6, 3.25, 3.12, 3.042, 24, 14, 0, 62.4, '2022-12-21 21:59:04', NULL),
('7755139002857', 3550, 'ELITE MEGARROLLO', 2.19, 2.7375, 2.628, 2.5623, 24, 14, 0, 52.56, '2022-12-21 21:59:04', NULL),
('7755139002858', 3549, 'PURA VIDA 395G', 2.6, 3.25, 3.12, 3.042, 21, 13, 2, 54.6, '2022-12-21 21:59:04', NULL),
('7755139002859', 3549, 'IDEAL CREMOSITA 395G', 3, 3.75, 3.6, 3.51, 20, 11, 1, 60, '2022-12-21 21:59:04', NULL),
('7755139002860', 3549, 'IDEAL LIGHT 395G', 2.8, 3.5, 3.36, 3.276, 27, 17, 0, 75.6, '2022-12-21 21:59:04', NULL),
('7755139002861', 3547, 'FRESA 370ML LAIVE', 2.19, 2.7375, 2.628, 2.5623, 28, 18, 0, 61.32, '2022-12-21 21:59:04', NULL),
('7755139002862', 3549, 'GLORIA EVAPORADA ENTERA', 3.2, 4, 3.84, 3.744, 21, 16, 5, 67.2, '2022-12-21 21:59:04', NULL),
('7755139002863', 3549, 'LAIVE LIGTH CAJA 480ML', 2.8, 3.5, 3.36, 3.276, 27, 17, 0, 75.6, '2022-12-21 21:59:04', NULL),
('7755139002864', 3545, 'PEPSI 1.5L', 4.4, 5.5, 5.28, 5.148, 20, 10, 0, 88, '2022-12-21 21:59:04', NULL),
('7755139002865', 3547, 'GLORIA DURAZNO 500ML', 3.79, 4.7375, 4.548, 4.4343, 23, 13, 0, 87.17, '2022-12-21 21:59:04', NULL),
('7755139002866', 3547, 'GLORIA VAINILLA FRANCESA 500ML', 3.79, 4.7375, 4.548, 4.4343, 26, 16, 0, 98.54, '2022-12-21 21:59:04', NULL),
('7755139002867', 3547, 'GRIEGO GLORIA', 3.65, 4.5625, 4.38, 4.2705, 23, 14, 1, 83.95, '2022-12-21 21:59:04', NULL),
('7755139002868', 3545, 'SABOR ORO 1.7L', 3.5, 4.375, 4.2, 4.095, 17, 10, 3, 59.5, '2022-12-21 21:59:04', NULL),
('7755139002869', 3539, 'CANCHITA MANTEQUILLA', 3.25, 4.0625, 3.9, 3.8025, 10, 11, 11, 32.5, '2022-12-26 18:12:10', NULL),
('7755139002870', 3539, 'CANCHITA NATURAL', 3.25, 4.0625, 3.9, 3.8025, 26, 16, 0, 84.5, '2022-12-21 21:59:04', NULL),
('7755139002871', 3549, 'LAIVE SIN LACTOSA CAJA 480ML', 3.17, 3.9625, 3.804, 3.7089, 27, 17, 0, 85.59, '2022-12-21 21:59:04', NULL),
('7755139002872', 3548, 'VALLE NORTE 750G', 3.1, 3.875, 3.72, 3.627, 30, 20, 0, 93, '2022-12-21 21:59:04', NULL),
('7755139002873', 3547, 'BATTIMIX', 2.89, 3.6125, 3.468, 3.3813, 25, 15, 0, 72.25, '2022-12-21 21:59:04', NULL),
('7755139002874', 3539, 'PRINGLES PAPAS', 2.8, 3.5, 3.36, 3.276, 28, 18, 0, 78.4, '2022-12-21 21:59:04', NULL),
('7755139002875', 3548, 'COSTEÑO 750G', 3.69, 4.6125, 4.428, 4.3173, 22, 12, 0, 81.18, '2022-12-21 21:59:04', NULL),
('7755139002876', 3548, 'FARAON AMARILLO 1K', 3.39, 4.2375, 4.068, 3.9663, 21, 11, 0, 71.19, '2022-12-21 21:59:04', NULL),
('7755139002877', 3551, 'A1 TROZOS', 5.17, 6.4625, 6.204, 6.0489, 30, 20, 0, 155.1, '2022-12-21 21:59:04', NULL),
('7755139002878', 3550, 'NOVA PQ 2 UNID', 3.99, 4.9875, 4.788, 4.6683, 25, 15, 0, 99.75, '2022-12-21 21:59:04', NULL),
('7755139002879', 3550, 'SUAVE PQ 4 UNID', 4.58, 5.725, 5.496, 5.3586, 28, 18, 0, 128.24, '2022-12-21 21:59:04', NULL),
('7755139002880', 3551, 'FLORIDA TROZOS', 5.15, 6.4375, 6.18, 6.0255, 23, 13, 0, 118.45, '2022-12-21 21:59:04', NULL),
('7755139002881', 3550, 'PARACAS PQ 4 UNID', 5, 6.25, 6, 5.85, 22, 12, 0, 110, '2022-12-21 21:59:04', NULL),
('7755139002882', 3551, 'TROZOS DE ATÚN CAMPOMAR', 4.66, 5.825, 5.592, 5.4522, 27, 17, 0, 125.82, '2022-12-21 21:59:04', NULL),
('7755139002883', 3551, 'A1 FILETE', 4.65, 5.8125, 5.58, 5.4405, 23, 13, 0, 106.95, '2022-12-21 21:59:04', NULL),
('7755139002884', 3551, 'REAL TROZOS', 4.63, 5.7875, 5.556, 5.4171, 21, 11, 0, 97.23, '2022-12-21 21:59:04', NULL),
('7755139002885', 3547, 'DURAZNO 1L LAIVE', 5.7, 7.125, 6.84, 6.669, 27, 17, 0, 153.9, '2022-12-21 21:59:04', NULL),
('7755139002886', 3547, 'FRESA 1L LAIVE', 5.7, 7.125, 6.84, 6.669, 21, 11, 0, 119.7, '2022-12-21 21:59:04', NULL),
('7755139002887', 3551, 'A1 FILETE LIGTH', 6.08, 7.6, 7.296, 7.1136, 27, 17, 0, 164.16, '2022-12-21 21:59:04', NULL),
('7755139002888', 3547, 'LÚCUMA 1L GLORIA', 5.9, 7.375, 7.08, 6.903, 22, 12, 0, 129.8, '2022-12-21 21:59:04', NULL),
('7755139002889', 3547, 'FRESA 1L GLORIA', 5.9, 7.375, 7.08, 6.903, 22, 18, 6, 129.8, '2022-12-21 21:59:04', NULL),
('7755139002890', 3547, 'MILKITO FRESA 1L', 5.9, 7.375, 7.08, 6.903, 24, 14, 0, 141.6, '2022-12-21 21:59:04', NULL),
('7755139002891', 3547, 'GLORIA DURAZNO 1L', 5.9, 7.375, 7.08, 6.903, 29, 19, 0, 171.1, '2022-12-21 21:59:04', NULL),
('7755139002892', 3551, 'FILETE DE ATÚN CAMPOMAR', 5.08, 6.35, 6.096, 5.9436, 21, 11, 0, 106.68, '2022-12-21 21:59:04', NULL),
('7755139002893', 3551, 'FLORIDA FILETE LIGTH', 5.63, 7.0375, 6.756, 6.5871, 29, 19, 0, 163.27, '2022-12-21 21:59:04', NULL),
('7755139002894', 3551, 'FILETE DE ATÚN FLORIDA', 5.4, 6.75, 6.48, 6.318, 23, 13, 0, 124.2, '2022-12-21 21:59:04', NULL),
('7755139002895', 3545, 'INCA KOLA 1.5L', 5.9, 7.375, 7.08, 6.903, 29, 19, 0, 171.1, '2022-12-21 21:59:04', NULL),
('7755139002896', 3545, 'COCA COLA 1.5L', 5.9, 7.375, 7.08, 6.903, 23, 17, 4, 135.7, '2022-12-21 21:59:04', NULL),
('7755139002897', 3541, 'RED BULL 250ML', 5.33, 6.6625, 6.396, 6.2361, 22, 12, 0, 117.26, '2022-12-21 21:59:04', NULL),
('7755139002898', 3545, 'SPRITE 3L', 7.49, 9.3625, 8.988, 8.7633, 27, 17, 0, 202.23, '2022-12-21 21:59:04', NULL),
('7755139002899', 3545, 'PEPSI 3L', 8, 10, 9.6, 9.36, 26, 16, 0, 208, '2022-12-21 21:59:04', NULL),
('7755139002900', 3549, 'LAIVE 200GR', 8.9, 11.125, 10.68, 10.413, 18, 11, 3, 160.2, '2022-12-22 15:53:58', NULL),
('7755139002901', 3544, 'GLORIA POTE CON SAL', 10, 11.4875, 11.028, 10.7523, 11, 16, 15, 110, '2022-12-23 13:24:37', NULL),
('7755139002902', 3546, 'DELEITE 1L', 9.8, 12.25, 11.76, 11.466, 21, 19, 8, 205.8, '2022-12-22 15:53:58', NULL),
('7755139002903', 3546, 'SAO 1L', 12.1, 15.125, 14.52, 14.157, 23, 13, 0, 278.3, '2022-12-21 21:59:04', NULL),
('7755139002904', 3546, 'COCINERO 1L', 12.4, 15.5, 14.88, 14.508, 29, 19, 0, 359.6, '2022-12-21 21:59:04', NULL),
('FR-3515456', 3537, 'MANZANA DELICIA', 3.5, 4.7, 4.5, 4.2, 6.25, 2, NULL, 21.88, '2022-12-11 23:26:49', '2022-12-10'),
('FR-45468785', 3537, 'NARANJA', 3, 4.5, 4.3, 4.1, 9.75, 3, NULL, 29.25, '2022-12-21 21:59:04', '2022-12-12'),
('VR-545485', 3538, 'LECHUGA', 2, 3.5, 3.4, 3.2, 3.5, 1, NULL, 7, '2022-12-29 13:07:56', '2022-12-10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `ruc` varchar(45) DEFAULT NULL,
  `razon_social` varchar(100) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_comprobante`
--

CREATE TABLE `tipo_comprobante` (
  `id` varchar(3) NOT NULL,
  `descripcion` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre_usuario` varchar(100) DEFAULT NULL,
  `apellido_usuario` varchar(100) DEFAULT NULL,
  `usuario` varchar(100) DEFAULT NULL,
  `clave` text DEFAULT NULL,
  `id_perfil_usuario` int(11) DEFAULT NULL,
  `estado` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_usuario`, `apellido_usuario`, `usuario`, `clave`, `id_perfil_usuario`, `estado`) VALUES
(1, 'Tutoriales', 'PHPeru', 'tperu', '$2a$07$azybxcags23425sdg23sdeanQZqjaf6Birm2NvcYTNtJw24CsO5uq', 1, 1),
(2, 'Paolo', 'Guerrero', 'pguerrero', '$2a$07$azybxcags23425sdg23sdeanQZqjaf6Birm2NvcYTNtJw24CsO5uq', 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_cabecera`
--

CREATE TABLE `venta_cabecera` (
  `nro_boleta` varchar(8) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `subtotal` float NOT NULL,
  `igv` float NOT NULL,
  `total_venta` float DEFAULT NULL,
  `fecha_venta` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `venta_cabecera`
--

INSERT INTO `venta_cabecera` (`nro_boleta`, `descripcion`, `subtotal`, `igv`, `total_venta`, `fecha_venta`) VALUES
('00000218', 'Venta realizada con Nro Boleta: 00000218', 0, 0, 12.18, '2022-12-10 04:20:01'),
('00000219', 'Venta realizada con Nro Boleta: 00000219', 0, 0, 14.27, '2022-12-10 04:21:09'),
('00000220', 'Venta realizada con Nro Boleta: 00000220', 0, 0, 2.35, '2022-12-10 04:51:11'),
('00000221', 'Venta realizada con Nro Boleta: 00000221', 0, 0, 1.18, '2022-12-10 04:54:34'),
('00000222', 'Venta realizada con Nro Boleta: 00000222', 0, 0, 21.2, '2022-12-10 16:09:40'),
('00000223', 'Venta realizada con Nro Boleta: 00000223', 0, 0, 2.52, '2022-12-11 00:21:46'),
('00000224', 'Venta realizada con Nro Boleta: 00000224', 0, 0, 4.7, '2022-12-11 00:22:06'),
('00000225', 'Venta realizada con Nro Boleta: 00000225', 0, 0, 0.84, '2022-12-11 00:22:16'),
('00000226', 'Venta realizada con Nro Boleta: 00000226', 0, 0, 8.76, '2022-12-11 23:26:48'),
('00000227', 'Venta realizada con Nro Boleta: 00000227', 0, 0, 114.3, '2022-12-11 23:27:06'),
('00000228', 'Venta realizada con Nro Boleta: 00000228', 0, 0, 13.3, '2022-12-11 23:29:50'),
('00000229', 'Venta realizada con Nro Boleta: 00000229', 0, 0, 1.13, '2022-12-12 00:13:40'),
('00000230', 'Venta realizada con Nro Boleta: 00000230', 0, 0, 22.88, '2022-12-12 15:58:25'),
('00000231', 'Venta realizada con Nro Boleta: 00000231', 0, 0, 21.76, '2022-12-12 15:58:59'),
('00000232', 'Venta realizada con Nro Boleta: 00000232', 0, 0, 4.06, '2022-12-13 00:56:51'),
('00000233', 'Venta realizada con Nro Boleta: 00000233', 0, 0, 24.72, '2022-12-15 01:46:12'),
('00000234', 'Venta realizada con Nro Boleta: 00000234', 0, 0, 29.58, '2022-12-15 01:46:51'),
('00000235', 'Venta realizada con Nro Boleta: 00000235', 0, 0, 24.31, '2022-12-17 03:49:17'),
('00000236', 'Venta realizada con Nro Boleta: 00000236', 0, 0, 15, '2022-12-17 03:49:45'),
('00000237', 'Venta realizada con Nro Boleta: 00000237', 0, 0, 8.31, '2022-12-17 19:17:01'),
('00000238', 'Venta realizada con Nro Boleta: 00000238', 0, 0, 4.63, '2022-12-17 19:25:20'),
('00000239', 'Venta realizada con Nro Boleta: 00000239', 0, 0, 70.22, '2022-12-19 23:10:07'),
('00000240', 'Venta realizada con Nro Boleta: 00000240', 0, 0, 91.44, '2022-12-21 16:17:25'),
('00000241', 'Venta realizada con Nro Boleta: 00000241', 0, 0, 14.76, '2022-12-21 16:17:38'),
('00000242', 'Venta realizada con Nro Boleta: 00000242', 0, 0, 28.2, '2022-12-22 01:44:24'),
('00000243', 'Venta realizada con Nro Boleta: 00000243', 0, 0, 138.69, '2022-12-22 15:53:58'),
('00000244', 'Venta realizada con Nro Boleta: 00000244', 0, 0, 32.5, '2022-12-22 18:15:24'),
('00000245', 'Venta realizada con Nro Boleta: 00000245', 0, 0, 172.35, '2022-12-23 13:24:37'),
('00000246', 'Venta realizada con Nro Boleta: 00000246', 0, 0, 160.02, '2022-12-23 13:55:34'),
('00000247', 'Venta realizada con Nro Boleta: 00000247', 0, 0, 47.3, '2022-12-26 18:11:26'),
('00000248', 'Venta realizada con Nro Boleta: 00000248', 0, 0, 4.06, '2022-12-26 18:12:09'),
('00000249', 'Venta realizada con Nro Boleta: 00000249', 0, 0, 1.33, '2022-12-26 18:12:46'),
('00000250', 'Venta realizada con Nro Boleta: 00000250', 0, 0, 33.38, '2022-12-29 13:07:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_detalle`
--

CREATE TABLE `venta_detalle` (
  `id` int(11) NOT NULL,
  `nro_boleta` varchar(8) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `codigo_producto` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `cantidad` float NOT NULL,
  `costo_unitario_venta` float DEFAULT NULL,
  `precio_unitario_venta` float DEFAULT NULL,
  `total_venta` float NOT NULL,
  `fecha_venta` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `venta_detalle`
--

INSERT INTO `venta_detalle` (`id`, `nro_boleta`, `codigo_producto`, `cantidad`, `costo_unitario_venta`, `precio_unitario_venta`, `total_venta`, `fecha_venta`) VALUES
(73, '00000218', '7755139002869', 3, 3.25, 4.0625, 12.18, '2022-12-09'),
(74, '00000219', '7755139002818', 1, 0.62, 0.775, 0.77, '2022-12-09'),
(75, '00000219', '7755139002902', 1, 9.8, 12.25, 12.25, '2022-12-09'),
(76, '00000219', '7755139002830', 1, 1, 1.25, 1.25, '2022-12-09'),
(77, '00000220', 'FR-3515456', 0.5, 3.5, 4.7, 2.35, '2022-12-09'),
(78, '00000221', 'FR-3515456', 0.25, 3.5, 4.7, 1.18, '2022-12-09'),
(79, '00000222', '7755139002811', 1, 3.4, 4.25, 4.25, '2022-12-10'),
(80, '00000222', '7755139002902', 1, 9.8, 12.25, 12.25, '2022-12-10'),
(81, '00000222', 'FR-3515456', 1, 3.5, 4.7, 4.7, '2022-12-10'),
(82, '00000223', '7755139002835', 3, 0.67, 0.8375, 2.52, '2022-12-10'),
(83, '00000224', 'FR-3515456', 1, 3.5, 4.7, 4.7, '2022-12-10'),
(84, '00000225', '7755139002835', 1, 0.67, 0.8375, 0.84, '2022-12-10'),
(85, '00000226', 'FR-3515456', 1, 3.5, 4.7, 4.7, '2022-12-11'),
(86, '00000226', '7755139002869', 1, 3.25, 4.0625, 4.06, '2022-12-11'),
(87, '00000227', '7755139002809', 5, 18.29, 22.8625, 114.3, '2022-12-11'),
(88, '00000228', '7755139002843', 10, 1.06, 1.325, 13.3, '2022-12-11'),
(89, '00000229', 'FR-45468785', 0.25, 3, 4.5, 1.13, '2022-12-11'),
(90, '00000230', '7755139002896', 1, 5.9, 7.375, 7.38, '2022-12-12'),
(91, '00000230', '7755139002858', 1, 2.6, 3.25, 3.25, '2022-12-12'),
(92, '00000230', '7755139002902', 1, 9.8, 12.25, 12.25, '2022-12-12'),
(93, '00000231', '7755139002868', 3, 3.5, 4.375, 13.14, '2022-12-12'),
(94, '00000231', '7755139002867', 1, 3.65, 4.5625, 4.56, '2022-12-12'),
(95, '00000231', '7755139002869', 1, 3.25, 4.0625, 4.06, '2022-12-12'),
(96, '00000232', '7755139002869', 1, 3.25, 4.0625, 4.06, '2022-12-12'),
(97, '00000233', '7755139002825', 1, 0.5, 0.625, 0.62, '2022-12-14'),
(98, '00000233', '7755139002813', 1, 0.33, 0.4125, 0.41, '2022-12-14'),
(99, '00000233', '7755139002896', 1, 5.9, 7.375, 7.38, '2022-12-14'),
(100, '00000233', '7755139002869', 1, 3.25, 4.0625, 4.06, '2022-12-14'),
(101, '00000233', '7755139002902', 1, 9.8, 12.25, 12.25, '2022-12-14'),
(102, '00000234', '7755139002809', 1, 18.29, 22.8625, 22.86, '2022-12-14'),
(103, '00000234', '7755139002826', 1, 0.47, 0.5875, 0.59, '2022-12-14'),
(104, '00000234', '7755139002811', 1, 3.4, 4.25, 4.25, '2022-12-14'),
(105, '00000234', '7755139002844', 1, 1.5, 1.875, 1.88, '2022-12-14'),
(106, '00000235', '7755139002902', 1, 9.8, 12.25, 12.25, '2022-12-16'),
(107, '00000235', '7755139002869', 1, 3.25, 4.0625, 4.06, '2022-12-16'),
(108, '00000235', '7755139002830', 1, 1, 1.25, 1.25, '2022-12-16'),
(109, '00000235', '7755139002858', 1, 2.6, 3.25, 3.25, '2022-12-16'),
(110, '00000235', 'VR-545485', 1, 2, 3.5, 3.5, '2022-12-16'),
(111, '00000236', '7755139002844', 5, 1.5, 1.875, 9.4, '2022-12-16'),
(112, '00000236', '7755139002831', 5, 0.9, 1.125, 5.6, '2022-12-16'),
(113, '00000237', '7755139002869', 1, 3.25, 4.0625, 4.06, '2022-12-17'),
(114, '00000237', '7755139002811', 1, 3.4, 4.25, 4.25, '2022-12-17'),
(115, '00000238', '7755139002859', 1, 3, 3.75, 3.75, '2022-12-17'),
(116, '00000238', 'VR-545485', 0.25, 2, 3.5, 0.88, '2022-12-17'),
(117, '00000239', '7755139002889', 6, 5.9, 7.375, 44.28, '2022-12-19'),
(118, '00000239', '7755139002869', 1, 3.25, 4.0625, 4.06, '2022-12-19'),
(119, '00000239', '7755139002862', 5, 3.2, 4, 20, '2022-12-19'),
(120, '00000239', '7755139002837', 1, 1.5, 1.875, 1.88, '2022-12-19'),
(121, '00000240', '7755139002809', 4, 18.29, 22.8625, 91.44, '2022-12-21'),
(122, '00000241', '7755139002896', 2, 5.9, 7.375, 14.76, '2022-12-21'),
(123, '00000242', '7755139002814', 10, 0.53, 0.6625, 6.6, '2022-12-21'),
(124, '00000242', '7755139002836', 10, 0.56, 0.7, 7, '2022-12-21'),
(125, '00000242', '7755139002827', 10, 0.47, 0.5875, 5.9, '2022-12-21'),
(126, '00000242', '7755139002815', 10, 0.37, 0.4625, 4.6, '2022-12-21'),
(127, '00000242', '7755139002813', 10, 0.33, 0.4125, 4.1, '2022-12-21'),
(128, '00000243', '7755139002900', 3, 8.9, 11.125, 33.36, '2022-12-22'),
(129, '00000243', '7755139002809', 3, 18.29, 22.8625, 68.58, '2022-12-22'),
(130, '00000243', '7755139002902', 3, 9.8, 12.25, 36.75, '2022-12-22'),
(131, '00000244', '7755139002855', 10, 2.6, 3.25, 32.5, '2022-12-22'),
(132, '00000245', '7755139002901', 15, 10, 11.4875, 172.35, '2022-12-23'),
(133, '00000246', '7755139002809', 7, 12.19, 22.8625, 160.02, '2022-12-23'),
(134, '00000247', '7755139002843', 10, 0.77, 1.325, 13.3, '2022-12-26'),
(135, '00000247', '7755139002841', 10, 0.92, 1.15, 11.5, '2022-12-26'),
(136, '00000247', '7755139002849', 10, 1.8, 2.25, 22.5, '2022-12-26'),
(137, '00000248', '7755139002869', 1, 3.25, 4.0625, 4.06, '2022-12-26'),
(138, '00000249', '7755139002843', 1, 0.76, 1.325, 1.33, '2022-12-26'),
(139, '00000250', 'VR-545485', 0.25, 2, 3.5, 0.88, '2022-12-29'),
(140, '00000250', '7755139002855', 10, 2.6, 3.25, 32.5, '2022-12-29');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `arqueo_caja`
--
ALTER TABLE `arqueo_caja`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_id_caja_idx` (`id_caja`),
  ADD KEY `fk_id_usuario_idx` (`id_usuario`);

--
-- Indices de la tabla `cajas`
--
ALTER TABLE `cajas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_id_proveedor_idx` (`id_proveedor`),
  ADD KEY `fk_id_comprobante_idx` (`id_tipo_comprobante`),
  ADD KEY `fk_id_moneda_idx` (`id_moneda_comprobante`);

--
-- Indices de la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cod_producto_idx` (`codigo_producto`),
  ADD KEY `fk_id_compra_idx` (`id_compra`);

--
-- Indices de la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`id_empresa`);

--
-- Indices de la tabla `kardex`
--
ALTER TABLE `kardex`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_id_producto_idx` (`codigo_producto`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `monedas`
--
ALTER TABLE `monedas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `perfiles`
--
ALTER TABLE `perfiles`
  ADD PRIMARY KEY (`id_perfil`);

--
-- Indices de la tabla `perfil_modulo`
--
ALTER TABLE `perfil_modulo`
  ADD PRIMARY KEY (`idperfil_modulo`),
  ADD KEY `id_perfil` (`id_perfil`),
  ADD KEY `id_modulo` (`id_modulo`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`codigo_producto`),
  ADD UNIQUE KEY `codigo_producto_UNIQUE` (`codigo_producto`),
  ADD KEY `fk_id_categoria_idx` (`id_categoria_producto`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipo_comprobante`
--
ALTER TABLE `tipo_comprobante`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_perfil_usuario` (`id_perfil_usuario`);

--
-- Indices de la tabla `venta_cabecera`
--
ALTER TABLE `venta_cabecera`
  ADD PRIMARY KEY (`nro_boleta`);

--
-- Indices de la tabla `venta_detalle`
--
ALTER TABLE `venta_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_nro_boleta_idx` (`nro_boleta`),
  ADD KEY `fk_cod_producto_idx` (`codigo_producto`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `arqueo_caja`
--
ALTER TABLE `arqueo_caja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cajas`
--
ALTER TABLE `cajas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3556;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empresa`
--
ALTER TABLE `empresa`
  MODIFY `id_empresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `kardex`
--
ALTER TABLE `kardex`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5463;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `monedas`
--
ALTER TABLE `monedas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `perfiles`
--
ALTER TABLE `perfiles`
  MODIFY `id_perfil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `perfil_modulo`
--
ALTER TABLE `perfil_modulo`
  MODIFY `idperfil_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `venta_detalle`
--
ALTER TABLE `venta_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `arqueo_caja`
--
ALTER TABLE `arqueo_caja`
  ADD CONSTRAINT `fk_id_caja` FOREIGN KEY (`id_caja`) REFERENCES `cajas` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_id_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `fk_id_comprobante` FOREIGN KEY (`id_tipo_comprobante`) REFERENCES `tipo_comprobante` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_id_moneda` FOREIGN KEY (`id_moneda_comprobante`) REFERENCES `monedas` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_id_proveedor` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  ADD CONSTRAINT `fk_cod_producto` FOREIGN KEY (`codigo_producto`) REFERENCES `productos` (`codigo_producto`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_id_compra` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `kardex`
--
ALTER TABLE `kardex`
  ADD CONSTRAINT `fk_cod_producto_kardex` FOREIGN KEY (`codigo_producto`) REFERENCES `productos` (`codigo_producto`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `perfil_modulo`
--
ALTER TABLE `perfil_modulo`
  ADD CONSTRAINT `id_modulo` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `id_perfil` FOREIGN KEY (`id_perfil`) REFERENCES `perfiles` (`id_perfil`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_id_categoria` FOREIGN KEY (`id_categoria_producto`) REFERENCES `categorias` (`id_categoria`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_perfil_usuario`) REFERENCES `perfiles` (`id_perfil`);

--
-- Filtros para la tabla `venta_detalle`
--
ALTER TABLE `venta_detalle`
  ADD CONSTRAINT `fk_cod_producto_detalle` FOREIGN KEY (`codigo_producto`) REFERENCES `productos` (`codigo_producto`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_nro_boleta` FOREIGN KEY (`nro_boleta`) REFERENCES `venta_cabecera` (`nro_boleta`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
