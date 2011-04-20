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
abstract class Glitch_Controller_Action_Rest extends Glitch_Controller_Action_Hmvc
{
    /**
     * Flag to see if we've already been dispatched
     * This will fix a problem with the layout name being set to something like main.xml.xml.phtml
     *
     * @var bool
     */
    protected static $_passed = false;

    /**
     * Initialize the RESTful controller logics
     */
    public function init()
    {
        //Context only needs to be registered once!
        if (!self::$_passed)
        {
            //Register contexts before parent::init because we need to make the ErrorHandler aware too!
            $formats = Glitch_Controller_Plugin_Rest::getFormats();
            if (0 < count($formats))
            {
                $context = $this->_helper->contextSwitch;
                $context->addActionContext('get', $formats)
                        ->addActionContext('put', $formats)
                        ->addActionContext('post', $formats)
                        ->addActionContext('delete', $formats)
                        ->initContext();
                //Make layout aware too!
                if (in_array($this->_getParam('format', null), array_filter($formats)))
                {
                    $layout = Zend_Layout::getMvcInstance();
                    $layout->setLayout($layout->getLayout() . '.' . $this->_getParam('format'));
                }
            }
            self::$_passed = true;
        }
        parent::init();
    }
}