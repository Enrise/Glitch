-- Initial decision tree structure

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE IF NOT EXISTS `dt_defects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aid` varchar(50) NOT NULL,
  `name_nl` varchar(50) NOT NULL,
  `name_en` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `dt_elements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aid` varchar(50) NOT NULL,
  `name_nl` varchar(50) NOT NULL,
  `name_en` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `dt_join` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `location_id` int(11) NOT NULL,
  `element_id` int(11) NOT NULL,
  `defect_id` int(11) NOT NULL,
  `flags` set('urgent','collective') NOT NULL,
  `etag` varchar(16) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `led_idx` (`location_id`,`element_id`,`defect_id`),
  KEY `element_id` (`element_id`),
  KEY `defect_id` (`defect_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `dt_locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aid` varchar(50) NOT NULL,
  `name_nl` varchar(50) NOT NULL,
  `name_en` varchar(50) NOT NULL,
  `etag` varchar(48) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `dt_join`
  ADD CONSTRAINT `dt_join_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `dt_locations` (`id`),
  ADD CONSTRAINT `dt_join_ibfk_2` FOREIGN KEY (`element_id`) REFERENCES `dt_elements` (`id`),
  ADD CONSTRAINT `dt_join_ibfk_3` FOREIGN KEY (`defect_id`) REFERENCES `dt_defects` (`id`);
  
  -- Initial object structure

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `restapi`
--

-- --------------------------------------------------------

--
-- Table structure for table `obj_cities`
--

CREATE TABLE IF NOT EXISTS `obj_cities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `obj_objects`
--

CREATE TABLE IF NOT EXISTS `obj_objects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `postal_id` int(11) NOT NULL,
  `housenumber` varchar(10) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `remarks` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `postal_id` (`postal_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `obj_postals`
--

CREATE TABLE IF NOT EXISTS `obj_postals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `postalcode` varchar(6) NOT NULL,
  `from` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `oddeven` enum('even','odd','both') NOT NULL,
  `street_id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `street_id` (`street_id`),
  KEY `city_id` (`city_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `obj_streets`
--

CREATE TABLE IF NOT EXISTS `obj_streets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- Initial object structure

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `restapi`
--

-- --------------------------------------------------------

--
-- Table structure for table `rep_observers`
--

CREATE TABLE IF NOT EXISTS `rep_observers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `repair_id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `gender` enum('m','f') NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(25) NOT NULL,
  `email_update` tinyint(1) NOT NULL,
  `text_update` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `rep_photos`
--

CREATE TABLE IF NOT EXISTS `rep_photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `path_location` varchar(200) NOT NULL,
  `filename` varchar(100) NOT NULL,
  `size` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Table structure for table `rep_repairs`
--

CREATE TABLE IF NOT EXISTS `rep_repairs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `object_uri` varchar(255) NOT NULL,
  `remarks` text NOT NULL,
  `appointment_uri` varchar(255) DEFAULT NULL,
  `code` varchar(10) NOT NULL,
  `owner_uri` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `rep_tasks`
--

CREATE TABLE IF NOT EXISTS `rep_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `repair_id` int(11) NOT NULL,
  `appointment_uri` varchar(255) DEFAULT NULL,
  `led_uri` varchar(255) NOT NULL,
  `title` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;
