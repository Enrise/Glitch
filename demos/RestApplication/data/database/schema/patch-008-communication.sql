
CREATE TABLE comms_mediums (
  `name` varchar(20) CHARACTER SET ascii NOT NULL,
  description varchar(48) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE comms_medium_parts (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `medium` varchar(20) CHARACTER SET ascii NOT NULL,
  `name` varchar(20) CHARACTER SET ascii NOT NULL,
  description varchar(48) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY `medium` (`medium`,`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;


CREATE TABLE comms_templates (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  description varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY id (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;


CREATE TABLE comms_templates_parts (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  template bigint(20) unsigned NOT NULL,
  mediumPart bigint(20) unsigned NOT NULL,
  contents text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY template_2 (template,mediumPart),
  KEY mediumPart (mediumPart),
  KEY template (template)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;


ALTER TABLE `comms_medium_parts`
  ADD CONSTRAINT comms_medium_parts_ibfk_1 FOREIGN KEY (`medium`) REFERENCES comms_mediums (`name`);


ALTER TABLE `comms_templates_parts`
  ADD CONSTRAINT comms_templates_parts_ibfk_1 FOREIGN KEY (template) REFERENCES comms_templates (id),
  ADD CONSTRAINT comms_templates_parts_ibfk_2 FOREIGN KEY (mediumPart) REFERENCES comms_medium_parts (id);


ALTER TABLE comms_templates ADD name VARCHAR( 50 ) CHARACTER SET ascii
    COLLATE ascii_general_ci NOT NULL AFTER id;
    
ALTER TABLE comms_templates ADD UNIQUE (name );

SET foreign_key_checks=1;
