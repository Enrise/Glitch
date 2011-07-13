-- Move all tables to InnoDB engine

ALTER TABLE `rep_tasks` ENGINE = InnoDB;
ALTER TABLE `rep_photos` ENGINE = InnoDB;
ALTER TABLE `rep_repairs` ENGINE = InnoDB;
ALTER TABLE `rep_observers` ENGINE = InnoDB;
