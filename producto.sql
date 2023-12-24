-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema producto
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema producto
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `producto` DEFAULT CHARACTER SET utf8 ;
USE `producto` ;

-- -----------------------------------------------------
-- Table `producto`.`fruta`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `producto`.`fruta` ;

CREATE TABLE IF NOT EXISTS `producto`.`fruta` (
  `id_fruta` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NULL,
  `precio` FLOAT NULL,
  `codigo` VARCHAR(45) NULL,
  `imagen` VARCHAR(45) NULL,
  PRIMARY KEY (`id_fruta`))
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `producto`.`fruta`
-- -----------------------------------------------------
START TRANSACTION;
USE `producto`;
INSERT INTO `producto`.`fruta` (`nombre`, `precio`, `codigo`, `imagen`) VALUES ('Manzana', '15', 'A01', 'imgs/manzana.jpg');
INSERT INTO `producto`.`fruta` (`nombre`, `precio`, `codigo`, `imagen`) VALUES ('Naranja', '18', 'A02', 'imgs/naranja.jpg');

COMMIT;