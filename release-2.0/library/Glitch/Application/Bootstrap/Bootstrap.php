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
 * @subpackage  Bootstrap
 * @author      4worx <info@4worx.com>
 * @copyright   2010, 4worx
 * @version     $Id$
 */

 /**
  * Bootstrapper with common utility methods
  *
  * Extend this class in the application's Bootstrap.php when project-specific routines
  * are required for bootstrapping.
  *
  * @category   Glitch
  * @package    Glitch_Application
  * @subpackage Bootstrap
  */
class Glitch_Application_Bootstrap_Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Constructor
     *
     * @param  Zend_Application|Zend_Application_Bootstrap_Bootstrapper $application
     * @return void
     */
    public function __construct($application)
    {
        parent::__construct($application);

        // Force dispatcher initialization. It must be loaded as soon as possible,
        // before any other _init*() kicks in, as other resources may depend on it.
        $this->_initDispatcher();
    }

    /**
     * Initializes the dispatcher
     *
     * Do NOT put this code inside a dedicated resource class: the dispatcher
     * must be set as soon as possble, to prevent ZF from booting its own default
     * dispatcher. This method guarantees early invocation.
     *
     * @return Glitch_Controller_Dispatcher_Standard
     */
    protected function _initDispatcher()
    {
        $front = Zend_Controller_Front::getInstance();
        $dispatcher = $front->getDispatcher();

        if (!$dispatcher instanceof Glitch_Controller_Dispatcher_Standard)
        {
            $dispatcher = new Glitch_Controller_Dispatcher_Standard();
            $front->setDispatcher($dispatcher);
        }

        return $dispatcher;
    }
}