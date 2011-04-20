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
 * Resource for setting layout options
 *
 * @category    Glitch
 * @package     Glitch_Application
 * @subpackage  Resource
 */
class Glitch_Application_Resource_Layout extends Zend_Application_Resource_Layout implements Glitch_Application_Resource_ModuleInterface
{
    /**
     * Default name of the layout script: "main.phtml"
     *
     * @var string
     */
    protected $_defaultScriptName = 'main';

    /**
     * Sets module-specific options
     *
     * This method is called automatically by the Modules controller plugin
     *
     * @param string $module
     * @return void
     */
    public function setModuleOptions($module)
    {
        // Format $module if unformatted, e.g. "default" --> "Default"
        $dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();
        $module = $dispatcher->formatModuleName($module);

        // Performance: use absolute path, so that ZF doesn't need to resolve it.
        // Layouts are always located inside the current module's dir
        $path = GLITCH_MODULES_PATH
              . DIRECTORY_SEPARATOR
              . $module
              . DIRECTORY_SEPARATOR
              . 'Layout';

        $options = array(
            'layoutPath' => $path,
            'layout' => $this->_defaultScriptName
        );

        // Merge defaults with user-defined options from the config
        $options = array_merge($options, $this->getOptions());

        // Passing $options will not erase other options - good!
        $this->getLayout()->setOptions($options);
    }
}