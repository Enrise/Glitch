-- Changeing owner URI to owner ID

ALTER TABLE `rep_repairs` CHANGE `owner_uri` `owner_id` INT NOT NULL;
