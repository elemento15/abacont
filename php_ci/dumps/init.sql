CREATE TABLE `categorias` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  `tipo` ENUM('I','G') NOT NULL DEFAULT 'G',
  `activo` TINYINT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `nombre_UNIQUE` (`nombre` ASC))
ENGINE = InnoDB;


CREATE TABLE `subcategorias` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  `categoria_id` INT UNSIGNED NOT NULL,
  `activo` TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  INDEX `fk_subcategorias_1_idx` (`categoria_id` ASC),
  CONSTRAINT `fk_subcategorias_1`
    FOREIGN KEY (`categoria_id`)
    REFERENCES `categorias` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;


CREATE TABLE `cuentas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  `tipo` ENUM('C','D','E') NOT NULL DEFAULT 'E',
  `activo` TINYINT NOT NULL DEFAULT 1,
  `num_tarjeta` VARCHAR(20) NULL,
  `num_cuenta` VARCHAR(30) NULL,
  `saldo` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `observaciones` TEXT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `nombre_UNIQUE` (`nombre` ASC))
ENGINE = InnoDB;


CREATE TABLE `movimientos_cuentas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fecha` DATE NOT NULL,
  `cuenta_id` INT UNSIGNED NOT NULL,
  `tipo` ENUM('A','C') NOT NULL DEFAULT 'C',
  `cancelado` TINYINT NOT NULL DEFAULT 0,
  `concepto` VARCHAR(80) NOT NULL,
  `observaciones` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_fecha_cuenta_mov_cta` (`fecha` ASC, `cuenta_id` ASC),
  CONSTRAINT `fk_movimientos_cuentas_1`
    FOREIGN KEY (`cuenta_id`)
    REFERENCES `cuentas` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;


CREATE TABLE `movimientos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fecha` DATE NOT NULL,
  `tipo` ENUM('I','G') NOT NULL DEFAULT 'G',
  `movimiento_cuenta_id` INT UNSIGNED NOT NULL,
  `subcategoria_id` INT UNSIGNED NOT NULL,
  `cancelado` TINYINT NOT NULL DEFAULT 0,
  `observaciones` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_fecha_tipo_mov` (`fecha` ASC, `tipo` ASC),
  CONSTRAINT `fk_movimientos_1`
    FOREIGN KEY (`movimiento_cuenta_id`)
    REFERENCES `movimientos_cuentas` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_movimientos_2`
    FOREIGN KEY (`subcategoria_id`)
    REFERENCES `subcategorias` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;