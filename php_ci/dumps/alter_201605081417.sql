ALTER TABLE `cuentas` 
	ADD COLUMN `usa_gastos` TINYINT NOT NULL DEFAULT 0 AFTER `activo`,
	ADD COLUMN `usa_ingresos` TINYINT NOT NULL DEFAULT 0 AFTER `usa_gastos`;
