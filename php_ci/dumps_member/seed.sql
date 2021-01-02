/* Accounts */
INSERT INTO `cuentas` VALUES (1, 'EFECTIVO', 'E', 1, 1, 1, '', '', '', '', 0.00, 1, 'Cuenta para manejo diario de efectivo');

/* Clases */
INSERT INTO `categorias` VALUES (1, 'NOMINA', 'I', 1);
INSERT INTO `categorias` VALUES (2, 'OTROS INGRESOS', 'I', 1);
INSERT INTO `categorias` VALUES (3, 'AUTOMOVIL', 'G', 1);
INSERT INTO `categorias` VALUES (4, 'DESPENSA', 'G', 1);
INSERT INTO `categorias` VALUES (5, 'HOGAR', 'G', 1);
INSERT INTO `categorias` VALUES (6, 'OTROS GASTOS', 'G', 1);
INSERT INTO `categorias` VALUES (7, 'ROPA Y ACCESORIOS', 'G', 1);
INSERT INTO `categorias` VALUES (8, 'SALUD', 'G', 1);
INSERT INTO `categorias` VALUES (9, 'SERVICIOS', 'G', 1);
INSERT INTO `categorias` VALUES (10, 'TRANSPORTE', 'G', 1);

/* Subclases */
INSERT INTO `subcategorias` VALUES (1, 'Pago Quincenal', 1, 1);
INSERT INTO `subcategorias` VALUES (2, 'Aguinaldo', 1, 1);
INSERT INTO `subcategorias` VALUES (3, 'Bonos', 1, 1);
INSERT INTO `subcategorias` VALUES (4, 'Gasolina', 3, 1);
INSERT INTO `subcategorias` VALUES (5, 'Servicio y Mantenimiento', 3, 1);
INSERT INTO `subcategorias` VALUES (6, 'Supermercado', 4, 1);
INSERT INTO `subcategorias` VALUES (7, 'Artículos p/ Hogar', 5, 1);
INSERT INTO `subcategorias` VALUES (8, 'Gastos Varios', 6, 1);
INSERT INTO `subcategorias` VALUES (9, 'Ropa', 7, 1);
INSERT INTO `subcategorias` VALUES (10, 'Accesorios', 7, 1);
INSERT INTO `subcategorias` VALUES (11, 'Consulta', 8, 1);
INSERT INTO `subcategorias` VALUES (12, 'Medicinas', 8, 1);
INSERT INTO `subcategorias` VALUES (13, 'Agua (Jumapam)', 9, 1);
INSERT INTO `subcategorias` VALUES (14, 'Luz (CFE)', 9, 1);
INSERT INTO `subcategorias` VALUES (15, 'Celular (Telcel)', 9, 1);
INSERT INTO `subcategorias` VALUES (16, 'Teléfono (Telmex)', 9, 1);
INSERT INTO `subcategorias` VALUES (17, 'Uber y Taxis', 10, 1);
INSERT INTO `subcategorias` VALUES (18, 'Camiones', 10, 1);
