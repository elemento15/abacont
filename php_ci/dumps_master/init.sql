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

# Create the first user
# - pass is created using php md5() function
# - pass = 'elemento'
INSERT INTO usuarios 
	(usuario, pass, nombre, dbase)
	VALUES ('elemento','af07e46299656b47b88f1164e50eacb5','Luis Lomeli','db_abacont01');