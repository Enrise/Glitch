#!/bin/sh

if [ ! -f /usr/local/enrise/bin/zfm ] ; then
 	echo "This script needs the ZFM application. Get it at the Enrise repository ( http://yum.enrise.com ) or ask your local system administrator."
	echo "See also: http://wiki.enrise.com/index.php/Zfm"
	exit;
fi

if [ -z $1 ] ; then
	echo "Usage: $0 <rootdirectory>"
	exit;
fi

/usr/local/enrise/bin/zfm $1 Communications_Model_Mediums comms_mediums
/usr/local/enrise/bin/zfm $1 Communications_Model_MediumParts comms_medium_parts
/usr/local/enrise/bin/zfm $1 Communications_Model_Templates comms_templates
/usr/local/enrise/bin/zfm $1 Communications_Model_TemplatesParts comms_templates_parts

/usr/local/enrise/bin/zfm $1 Repair_Model_Repair rep_repairs
/usr/local/enrise/bin/zfm $1 Repair_Model_Photo rep_photos
/usr/local/enrise/bin/zfm $1 Repair_Model_Observer rep_observers
/usr/local/enrise/bin/zfm $1 Repair_Model_Task rep_tasks
/usr/local/enrise/bin/zfm $1 Repair_Model_TaskLogComment rep_tasks_logcomments

/usr/local/enrise/bin/zfm $1 Object_Model_City obj_cities
/usr/local/enrise/bin/zfm $1 Object_Model_Object obj_objects
/usr/local/enrise/bin/zfm $1 Object_Model_Street obj_streets
/usr/local/enrise/bin/zfm $1 Object_Model_Postalcode obj_postals
/usr/local/enrise/bin/zfm $1 Object_Model_ObjectLogComment obj_tasks_logcomments

/usr/local/enrise/bin/zfm $1 Decision_Model_Defects dt_defects
/usr/local/enrise/bin/zfm $1 Decision_Model_Elements dt_elements
/usr/local/enrise/bin/zfm $1 Decision_Model_Join dt_join
/usr/local/enrise/bin/zfm $1 Decision_Model_Locations dt_locations

