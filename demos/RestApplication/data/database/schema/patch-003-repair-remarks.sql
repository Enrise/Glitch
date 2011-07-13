-- Moving remarks from repairs to tasks

ALTER TABLE `rep_repairs` DROP `remarks` ;
ALTER TABLE `rep_tasks` ADD `remarks` TEXT NOT NULL ;
