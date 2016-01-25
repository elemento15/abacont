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

