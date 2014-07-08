-- -----------------------------------------------------
-- Table `koldunschik4`.`index`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `index` (
  `idindex` INT(11) NOT NULL AUTO_INCREMENT ,
  `siteid` INT(11) NOT NULL ,
  `pageid` INT(11) NOT NULL ,
  `se` ENUM('google','yandex') NOT NULL ,
  `date` DATE NOT NULL ,
  PRIMARY KEY (`idindex`, `siteid`, `pageid`) ,
  UNIQUE INDEX `idcrawler_UNIQUE` (`idindex` ASC) ,
  INDEX `fk_crawler_page` (`pageid` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `koldunschik4`.`link`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `link` (
  `idlink` INT(11) NOT NULL AUTO_INCREMENT ,
  `siteid` INT(11) NOT NULL ,
  `pageid` INT(11) NOT NULL ,
  `queryid` INT(11) NOT NULL ,
  `date` DATE NOT NULL ,
  PRIMARY KEY (`idlink`, `pageid`, `queryid`, `siteid`) ,
  UNIQUE INDEX `idlink_UNIQUE` (`idlink` ASC) ,
  INDEX `fk_link_page1` (`pageid` ASC) ,
  INDEX `fk_link_query1` (`queryid` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `koldunschik4`.`page`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `page` (
  `idpage` INT(11) NOT NULL AUTO_INCREMENT ,
  `siteid` INT(11) NOT NULL ,
  `title` VARCHAR(100) NOT NULL ,
  `relative` VARCHAR(500) NULL DEFAULT NULL ,
  `links` INT(2) NULL DEFAULT '0' ,
  `status` ENUM('OK','NEW','LINK','PROMO','DEL') NULL DEFAULT NULL ,
  PRIMARY KEY (`idpage`, `siteid`) ,
  UNIQUE INDEX `idpage_UNIQUE` (`idpage` ASC) ,
  INDEX `relative_INDEX` (`relative`(333) ASC) ,
  INDEX `fk_page_site1` (`siteid` ASC) ,
  INDEX `links_INDEX` (`links` ASC) ,
  FULLTEXT INDEX `title_FULLTEXT` (`title` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `koldunschik4`.`position`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `position` (
  `idposition` INT(11) NOT NULL AUTO_INCREMENT ,
  `siteid` INT(11) NOT NULL ,
  `pageid` INT(11) NOT NULL ,
  `queryid` INT(11) NOT NULL ,
  `number` INT(11) NOT NULL ,
  `date` DATE NOT NULL ,
  PRIMARY KEY (`idposition`, `siteid`, `queryid`, `pageid`) ,
  INDEX `fk_position_query1` (`queryid` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `koldunschik4`.`query`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `query` (
  `idquery` INT(11) NOT NULL AUTO_INCREMENT ,
  `siteid` INT(11) NOT NULL ,
  `pageid` INT(11) NOT NULL ,
  `name` VARCHAR(100) NOT NULL ,
  `se` ENUM('google','yandex') NOT NULL ,
  `status` ENUM('OK','WAIT','STOP','DEL') NOT NULL ,
  PRIMARY KEY (`idquery`, `pageid`, `siteid`) ,
  UNIQUE INDEX `idquery_UNIQUE` (`idquery` ASC) ,
  INDEX `fk_query_page` (`pageid` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `koldunschik4`.`setting`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `setting` (
  `idsetting` INT(11) NOT NULL AUTO_INCREMENT ,
  `siteid` INT(11) NOT NULL ,
  `googlerank` INT(11) NOT NULL ,
  `yandexrank` INT(11) NOT NULL ,
  `maxlinksday` INT(11) NOT NULL ,
  `minpageview` INT(11) NOT NULL ,
  `minqueryview` INT(11) NOT NULL ,
  `maxquerylength` INT(11) NOT NULL ,
  `linking` TINYINT(1) NOT NULL ,
  `maxquerylinks` INT(11) NOT NULL ,
  `maxpagelinks` INT(11) NOT NULL ,
  `multisite_linking` TINYINT(1) NOT NULL ,
  `multisite_maxquerylinks` INT(11) NOT NULL ,
  `querystatus` ENUM('OK','WAIT','DEL') NOT NULL ,
  `numberlinks` TINYINT(1) NOT NULL ,
  PRIMARY KEY (`idsetting`, `siteid`) ,
  UNIQUE INDEX `idsetting_UNIQUE` (`idsetting` ASC) ,
  INDEX `fk_setting_site` (`siteid` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `koldunschik4`.`site`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `site` (
  `idsite` INT(11) NOT NULL AUTO_INCREMENT ,
  `host` VARCHAR(100) NOT NULL ,
  PRIMARY KEY (`idsite`) ,
  UNIQUE INDEX `idsite_UNIQUE` (`idsite` ASC) ,
  INDEX `host_INDEX` (`host` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `koldunschik4`.`user`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `user` (
  `iduser` INT(11) NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(255) NOT NULL ,
  `password` VARCHAR(255) NOT NULL ,
  `role` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`iduser`) ,
  UNIQUE INDEX `iduser_UNIQUE` (`iduser` ASC) ,
  UNIQUE INDEX `username_UNIQUE` (`username` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `koldunschik4`.`view`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `view` (
  `idview` INT(11) NOT NULL AUTO_INCREMENT ,
  `siteid` INT(11) NOT NULL ,
  `pageid` INT(11) NOT NULL ,
  `date` DATE NOT NULL ,
  PRIMARY KEY (`idview`, `pageid`, `siteid`) ,
  INDEX `fk_view_page1` (`pageid` ASC) ,
  INDEX `pageid_INDEX` (`pageid` ASC, `date` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

INSERT INTO `user` (`iduser`, `username`, `password`, `role`) VALUES
(1, 'admin', 'admin', 'admin');
