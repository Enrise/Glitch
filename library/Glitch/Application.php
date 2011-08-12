<?php
/**
 * Glitch
 *
 * This source file is proprietary and protected by international
 * copyright and trade secret laws. No part of this source file may
 * be reproduced, copied, adapted, modified, distributed, transferred,
 * translated, disclosed, displayed or otherwise used by anyone in any
 * form or by any means without the express written authorization of
 * 4worx software innovators BV (www.4worx.com)
 *
 * @category    Glitch
 * @package     Glitch_Application
 * @author      4worx <info@4worx.com>
 * @copyright   2010, 4worx
 * @version     $Id$
 */

/**
 * Overrided Zend_Application to be able to unset resources from the
 * configuration before instantiating them
 *
 * @category    Glitch
 * @package     Glitch_Application
 */
class Glitch_Application extends Zend_Application
{
    /**
     * Unset a resource in the resources array from the configuration options
     *
     * @param  string $key
     * @return void
     */
    public function unsetResource($key)
    {
        if ($this->hasOption('resources'))
        {
            unset($this->_options['resources'][$key]);
        }
    }
}