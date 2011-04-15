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
 * @subpackage  Resource
 * @author      4worx <info@4worx.com>
 * @copyright   2010, 4worx
 * @version     $Id$
 */

/**
 * Interface for resources that require module-specific options
 *
 * @category    Glitch
 * @package     Glitch_Application
 * @subpackage  Resource
 */
interface Glitch_Application_Resource_ModuleInterface
{
    /**
     * Sets module-specific options
     *
     * @param string $module
     * @return void
     */
    public function setModuleOptions($module);
}