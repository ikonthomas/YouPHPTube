-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user` VARCHAR(45) NOT NULL,
  `name` VARCHAR(45) NULL,
  `email` VARCHAR(45) NULL,
  `password` VARCHAR(45) NOT NULL,
  `created` DATETIME NOT NULL,
  `modified` DATETIME NOT NULL,
  `isAdmin` TINYINT(1) NOT NULL DEFAULT 0,
  `status` ENUM('a', 'i') NOT NULL DEFAULT 'a',
  `photoURL` VARCHAR(255) NULL,
  `lastLogin` DATETIME NULL,
  `recoverPass` VARCHAR(255) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `user_UNIQUE` (`user` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `categories`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `clean_name` VARCHAR(45) NOT NULL,
  `created` DATETIME NOT NULL DEFAULT now(),
  `modified` DATETIME NOT NULL DEFAULT now(),
  `iconClass` VARCHAR(45) NOT NULL DEFAULT 'fa fa-folder',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `clean_name_UNIQUE` (`clean_name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `videos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `videos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `clean_title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `views_count` INT NOT NULL DEFAULT 0,
  `status` ENUM('a', 'i', 'e', 'x', 'd') NOT NULL DEFAULT 'e' COMMENT 'a = active\ni = inactive\ne = encoding\nx = encoding error\nd = downloading',
  `created` DATETIME NOT NULL,
  `modified` DATETIME NOT NULL,
  `users_id` INT NOT NULL,
  `categories_id` INT NOT NULL,
  `filename` VARCHAR(255) NOT NULL,
  `duration` VARCHAR(15) NOT NULL,
  `type` ENUM('audio', 'video') NOT NULL DEFAULT 'video',
  PRIMARY KEY (`id`),
  INDEX `fk_videos_users_idx` (`users_id` ASC),
  INDEX `fk_videos_categories1_idx` (`categories_id` ASC),
  UNIQUE INDEX `clean_title_UNIQUE` (`clean_title` ASC),
  CONSTRAINT `fk_videos_users`
    FOREIGN KEY (`users_id`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_videos_categories1`
    FOREIGN KEY (`categories_id`)
    REFERENCES `categories` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `comments`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `comment` VARCHAR(255) NOT NULL,
  `videos_id` INT NOT NULL,
  `users_id` INT NOT NULL,
  `created` DATETIME NOT NULL,
  `modified` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_comments_videos1_idx` (`videos_id` ASC),
  INDEX `fk_comments_users1_idx` (`users_id` ASC),
  CONSTRAINT `fk_comments_videos1`
    FOREIGN KEY (`videos_id`)
    REFERENCES `videos` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_comments_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `configurations`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `configurations` (
  `id` INT NOT NULL,
  `video_resolution` VARCHAR(12) NOT NULL,
  `users_id` INT NOT NULL,
  `version` VARCHAR(10) NOT NULL,
  `webSiteTitle` VARCHAR(45) NOT NULL DEFAULT 'YouPHPTube',
  `language` VARCHAR(6) NOT NULL DEFAULT 'en',
  `contactEmail` VARCHAR(45) NOT NULL,
  `modified` DATETIME NOT NULL DEFAULT now(),
  `created` DATETIME NOT NULL DEFAULT now(),
  `authGoogle_id` VARCHAR(255) NULL,
  `authGoogle_key` VARCHAR(255) NULL,
  `authGoogle_enabled` TINYINT(1) NOT NULL DEFAULT 0,
  `authFacebook_id` VARCHAR(255) NULL,
  `authFacebook_key` VARCHAR(255) NULL,
  `authFacebook_enabled` TINYINT(1) NOT NULL DEFAULT 0,
  `authCanUploadVideos` TINYINT(1) NOT NULL DEFAULT 0,
  `authCanComment` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  INDEX `fk_configurations_users1_idx` (`users_id` ASC),
  CONSTRAINT `fk_configurations_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
