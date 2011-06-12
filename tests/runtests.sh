#!/bin/sh
#

if [ -z "$1" ] ; then
	dir=Glitch/
else
	dir=$1
fi

phpunit \
	--configuration phpunit-local.xml \
	-d memory_limit=-1 \
	-d display_startup_errors=0 \
	$dir

#	--process-isolation \
