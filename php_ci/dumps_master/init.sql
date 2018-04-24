CREATE TABLE `usuarios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `usuario` VARCHAR(10) NOT NULL,
  `pass` BLOB NOT NULL,
  `nombre` VARCHAR(254) NULL,
  `activo` TINYINT NOT NULL DEFAULT 1,
  `dbase` VARCHAR(254) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `usuario_UNIQUE` (`usuario` ASC))
ENGINE = InnoDB;

ALTER TABLE `usuarios` 
  ADD COLUMN `email` VARCHAR(254) NULL AFTER `nombre`,
  ADD COLUMN `fecha` DATETIME NULL AFTER `dbase`,
  ADD UNIQUE INDEX `email_UNIQUE` (`email` ASC);


# Create the first user
# - pass is created using php md5() function
# - pass = 'elemento'
INSERT INTO usuarios 
	(usuario, pass, nombre, email, dbase)
	VALUES ('elemento','af07e46299656b47b88f1164e50eacb5','Luis Lomeli','luis@example.com','db_abacont01');