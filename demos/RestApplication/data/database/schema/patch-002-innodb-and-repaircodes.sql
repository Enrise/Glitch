-- Move all tables to InnoDB engine

ALTER TABLE `dt_defects` ENGINE = InnoDB;
ALTER TABLE `dt_elements` ENGINE = InnoDB;
ALTER TABLE `dt_join` ENGINE = InnoDB;
ALTER TABLE `dt_locations` ENGINE = InnoDB;

ALTER TABLE `obj_cities` ENGINE = InnoDB;
ALTER TABLE `obj_objects` ENGINE = InnoDB;
ALTER TABLE `obj_postals` ENGINE = InnoDB;
ALTER TABLE `obj_streets` ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `rep_codes` (
  `code` varchar(10) NOT NULL,
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
