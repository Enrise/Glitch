#!/usr/bin/env bash

# Example
#
# This source file is proprietary and protected by international
# copyright and trade secret laws. No part of this source file may
# be reproduced, copied, adapted, modified, distributed, transferred,
# translated, disclosed, displayed or otherwise used by anyone in any
# form or by any means without the express written authorization of
# 4worx software innovators BV (www.4worx.com)
#
# @category    IDM
# @author      4worx <info@4worx.com>
# @copyright   2010, 4worx
# @version     $Id:$

# Determine the paths to essential directories
jobsDir=$( (cd -P $(dirname $0) && pwd) )
rootDir=$( dirname $(dirname $jobsDir) )
scriptsDir="$rootDir/cli/"

# Load the common utility functions
source "$jobsDir/utils/common.sh"

# Check whether a valid mode was given
if [ $# != 1 ]; then
    notifyOfFailure "Please give the mode as argument (development, testing, qa, acceptance or production)"
    exit 1
fi

env=$1
php=$(getPhpExecutable)
if [ $? != 0 ]; then
    echo $php
    exit 1
fi
changeDir $scriptsDir

startDate=`date +%s`
echo "Flushing scoreboard..."

output=$($php index.php -r scripts.cron.example  -e $env)

if [ $? != 0 ]; then
    notifyOfFailure " flushing failed; reason: $output ..."
fi

# Back to original directory
changeDir $jobsDir

endDate=$((`date +%s` - $startDate))
echo "Finished cleanup in $endDate seconds"