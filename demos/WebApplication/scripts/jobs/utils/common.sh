#!/usr/bin/env bash

# Common utility functions
#
# This source file is proprietary and protected by international
# copyright and trade secret laws. No part of this source file may
# be reproduced, copied, adapted, modified, distributed, transferred,
# translated, disclosed, displayed or otherwise used by anyone in any
# form or by any means without the express written authorization of
# 4worx software innovators BV (www.4worx.com)
#
# @category    Mainflow
# @author      4worx <info@4worx.com>
# @copyright   2010, 4worx
# @version     $Id: common.sh 8024 2010-10-21 11:42:34Z tpater $

# Notify administrator of failure by sending an email
notifyOfFailure()
{
    path=$(getSendmail)
    if [ $? != 0 ]; then
        echo "No sendmail found"
    else
        # Wait Wut!?
        to="mainflow@4worx.com"

        # Figure out who called this function, e.g. "warmup-lister.sh"
        caller=$( basename $0 )

        headers="to:$to\nsubject:$caller terminated abnormally"
        message="$caller terminated abnormally at `date` on `hostname`\n\nMessage: $1"

        echo -e "$headers\n\n$message" | $path -t
    fi

    echo -e $1
}

# Determine the sendmail path
getSendmail()
{
    # First get preferred setting, if any; suppress errors
    path=`which sendmail 2>/dev/null`;
    if [ ${#path} != 0 ]; then
        echo $path
    elif [ -f /usr/sbin/sendmail ]; then
        echo "/usr/sbin/sendmail"
    else
        exit 1
    fi
}

# Determine the PHP executable to use; bail out on failure
getPhpExecutable()
{
    if [ -f /usr/local/zend/bin/php ]; then
        echo "/usr/local/zend/bin/php"
    elif [ -f /usr/bin/php ]; then
        echo "/usr/bin/php"
    else
        notifyOfFailure "Failed to find PHP executable"
        exit 1
    fi
}

# Go to the specified directory; bail out on failure
changeDir()
{
    cd $1 ||
    {
        notifyOfFailure "Failed to change to directory $1"
        exit 1
    }
}
