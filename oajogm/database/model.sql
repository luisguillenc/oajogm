SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `oajogm` DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci ;
USE `oajogm` ;

-- -----------------------------------------------------
-- Table `oajogm`.`network_resources`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `oajogm`.`network_resources` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `created` DATETIME NOT NULL ,
  `created_by` VARCHAR(45) NOT NULL ,
  `updated` DATETIME NOT NULL ,
  `updated_by` VARCHAR(45) NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `desc` TEXT NULL ,
  `type` VARCHAR(45) NOT NULL ,
  `ip` VARCHAR(45) NULL ,
  `network_addr` VARCHAR(45) NULL ,
  `network_mask` VARCHAR(45) NULL ,
  `begin_ip` VARCHAR(45) NULL ,
  `end_ip` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `oajogm`.`access_profile`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `oajogm`.`access_profile` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `created` DATETIME NOT NULL ,
  `created_by` VARCHAR(45) NOT NULL ,
  `updated` DATETIME NOT NULL ,
  `updated_by` VARCHAR(45) NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `desc` TEXT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `oajogm`.`profile_resources`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `oajogm`.`profile_resources` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `profile_id` INT NOT NULL ,
  `resource_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `uq_profile_resource` (`profile_id` ASC, `resource_id` ASC) ,
  INDEX `fk_resource_id_idx` (`resource_id` ASC) ,
  INDEX `fk_profile_id_idx` (`profile_id` ASC) ,
  CONSTRAINT `fk_profile_id`
    FOREIGN KEY (`profile_id` )
    REFERENCES `oajogm`.`access_profile` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_resource_id`
    FOREIGN KEY (`resource_id` )
    REFERENCES `oajogm`.`network_resources` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `oajogm`.`access_client`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `oajogm`.`access_client` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `created` DATETIME NOT NULL ,
  `created_by` VARCHAR(45) NOT NULL ,
  `updated` DATETIME NOT NULL ,
  `updated_by` VARCHAR(45) NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `desc` TEXT NULL ,
  `vpn_ip` VARCHAR(45) NOT NULL ,
  `profile_id` INT NOT NULL ,
  `locked` TINYINT(1) NOT NULL ,
  `iptables_log` TINYINT(1) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) ,
  INDEX `fk_access_client_profile_idx` (`profile_id` ASC) ,
  UNIQUE INDEX `vpn_ip_UNIQUE` (`vpn_ip` ASC) ,
  CONSTRAINT `fk_access_client_profile`
    FOREIGN KEY (`profile_id` )
    REFERENCES `oajogm`.`access_profile` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `oajogm`.`connection_log`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `oajogm`.`connection_log` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `timestamp` TIMESTAMP NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `action` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `oajogm`.`vpn_pool`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `oajogm`.`vpn_pool` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `netaddr` VARCHAR(45) NOT NULL ,
  `netmask` VARCHAR(45) NOT NULL ,
  `ipaddr` VARCHAR(45) NOT NULL ,
  `last` TINYINT(1) NOT NULL DEFAULT false ,
  `reserved` TINYINT(1) NOT NULL DEFAULT false ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `ip_UNIQUE` (`ipaddr` ASC, `netmask` ASC, `netaddr` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `oajogm`.`audit_log`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `oajogm`.`audit_log` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `timestamp` TIMESTAMP NOT NULL ,
  `username` VARCHAR(45) NOT NULL ,
  `action` VARCHAR(255) NOT NULL ,
  `data` TEXT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

USE `oajogm` ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
