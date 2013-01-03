#!/bin/sh
#

if [ -z "$1" ] ; then
	dir=Glitch/
else
	dir=$1
fi

./../dev/submodule/Phpunit-3.6/phpunit.php \
    --testdox-html ../tests/coverage/testdox.html \
    --coverage-html ../tests/coverage \
    --configuration phpunit-local.xml \
    -d memory_limit=-1 \
    -d display_startup_errors=0 \
    --colors \
    $dir

#	--process-isolation \
