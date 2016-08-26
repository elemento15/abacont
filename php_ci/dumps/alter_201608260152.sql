ALTER TABLE `movimientos`
	ADD COLUMN `extraordinario` TINYINT NOT NULL DEFAULT '0' AFTER `observaciones`;
