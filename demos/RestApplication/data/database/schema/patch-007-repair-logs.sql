-- Repair & Objects: logcomments. Also: Replacing 'remarks' with 'description', 'reason' with 'cause' 
ALTER TABLE rep_tasks CHANGE remarks description TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE rep_tasks CHANGE reason cause VARCHAR( 100 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE obj_objects CHANGE remarks description TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

CREATE TABLE rep_tasks_logcomments (
    id SERIAL NOT NULL ,
    user_uri VARCHAR( 200 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL , 
    contents TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
    task_id INT NOT NULL ,
    date_posted DATETIME NOT NULL ,
    INDEX ( user_uri ), 
    INDEX ( task_id ) 
) ENGINE = InnoDB;

CREATE TABLE obj_tasks_logcomments (
    id SERIAL NOT NULL ,
    user_uri VARCHAR( 200 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL , 
    contents TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
    object_id INT NOT NULL ,
    date_posted DATETIME NOT NULL ,
    INDEX ( user_uri ), 
    INDEX ( object_id ) 
) ENGINE = InnoDB;
