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
 * @package     Glitch_Controller
 * @subpackage  Action
 * @author      4worx <info@4worx.com>
 * @copyright   2010, 4worx
 * @version     $Id$
 */

/**
 * Overrided Glitch_Controller_Action_Hmvc for enabling controllers to follow the RESTful principle.
 *
 * The HMVC structure makes it possible for REST calls to have a hierarchical url which will follow the path
 *
 * @category    Glitch
 * @package     Glitch_Controller
 * @subpackage  Action
 * @author      dpapadogiannakis@4worx.com
 */
abstract class Glitch_Controller_Action_Hmvc extends Zend_Controller_Action
{
    /**
     * The controller param value for easy access
     *
     * @var mixed
     */
    protected $_controllerParam;

    /**
     * The delimiter used by the dispatcher to scan for controller_subcontroller_etc
     * Also used to fix the controllerName for view script rendering.
     *
     * @var string
     */
    protected $_pathDelimiter;

    /**
     * Initialize the HMVC structure
     *
     * Checks for passthrough param, if set calls {{@link _passthrough}}
     *
     * @return null
     */
    public function init()
    {
        $this->_pathDelimiter = $this->getFrontController()->getDispatcher()->getPathDelimiter();
        $this->_setControllerParam();
        $this->_fixControllerName();
        if ($this->_getParam('passthrough'))
        {
            $this->_passthrough();
        }
        parent::init();
    }

    /**
     * Take actions based on if the passthrough param was true
     *
     * Throws Exception if this call is not valid {{@link _isValid}}
     * Disables the viewrenderer by default
     *
     * @throws Exception
     * @return null
     */
    protected function _passthrough()
    {
        if (!$this->_isValid())
        {
            throw new Exception('Does not exist!');
        }
        $this->_helper->viewRenderer->setNoRender();
    }

    /**
     * Check if a certain call is valid
     *
     * @return bool
     */
    protected function _isValid()
    {
        return true;
    }

    /**
     * Fix the controller name for view script calls
     *
     * @return null
     */
    protected function _fixControllerName()
    {
        //Fix for view script to follow HMVC
        $this->getRequest()->setControllerName(
            str_replace($this->_pathDelimiter, DIRECTORY_SEPARATOR, $this->getRequest()->getControllerName())
        );
    }

    /**
     * Set the param for the called controller
     *
     * @return null
     */
    protected function _setControllerParam()
    {
        $controllers = (array) explode($this->_pathDelimiter, strtolower($this->_getParam('controller', '')));
        if (0 === count($controllers))
        {
            return;
        }
        $currentController = array_pop($controllers);
        if (empty($currentController))
        {
            return;
        }
        $this->_controllerParam = $this->_getParam($currentController);
    }
}