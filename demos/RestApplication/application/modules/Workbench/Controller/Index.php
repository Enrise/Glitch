<?php
/**
 * Mainflow
 *
 * This source file is proprietary and protected by international
 * copyright and trade secret laws. No part of this source file may
 * be reproduced, copied, adapted, modified, distributed, transferred,
 * translated, disclosed, displayed or otherwise used by anyone in any
 * form or by any means without the express written authorization of
 * 4worx software innovators BV (www.4worx.com)
 *
 * @category    Mainflow
 * @package     Workbench_Controller
 * @author      4worx <info@4worx.com>
 * @copyright   2010, 4worx
 * @version     $Id: Index.php 12807 2011-03-21 16:14:41Z jthijssen $
 */

/**
 * Index controller
 *
 * @category    Mainflow
 * @package     Workbench_Controller
 */
class Workbench_Controller_Index extends Zend_Controller_Action
{
    /**
     * Class constructor
     *
     * Override the default class constructor of Zend_Controller_Action to enable detection of
     * ajax context calls. If an ajax call is done, the format is automatically set based on
     * the type of request
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @param array $invokeArgs Any additional invocation arguments
     * @return void
     */
    public function __construct(Zend_Controller_Request_Abstract $request,
                                Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        // acceptance and production cannot access the workbench
        if (GLITCH_APP_ENV == "acceptance" || GLITCH_APP_ENV == "production") {
                header('HTTP/1.1 403 Forbidden');
                exit;
        }

        // Only set param if called in HTTP mode
        if (method_exists($request, 'isXmlHttpRequest') && $request->isXmlHttpRequest()) {
            $request->setParam('format', 'json');
        }
        parent::__construct($request, $response, $invokeArgs);
    }

    /**
     * Initializes the controller
     *
     * @return void
     */
    public function init()
    {
        $this->_helper->ajaxContext->addActionContext('index', 'json')
                                   ->setAutoJsonSerialization(false)
                                   ->initContext();
    }

    /**
     * Index action for the workbench
     *
     * @return void
     */
    public function indexAction()
    {
        $form = new Workbench_Form_Workbench();
        $form->process($this->getRequest(), array(), $this);
        $this->view->form = $form;
    }
}
