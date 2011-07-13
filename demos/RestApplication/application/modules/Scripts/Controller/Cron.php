<?php
/**
 * IDM Wegener
 *
 * This source file is proprietary and protected by international
 * copyright and trade secret laws. No part of this source file may
 * be reproduced, copied, adapted, modified, distributed, transferred,
 * translated, disclosed, displayed or otherwise used by anyone in any
 * form or by any means without the express written authorization of
 * 4worx software innovators BV (www.4worx.com)
 *
 * @author      4worx <info@4worx.com>
 * @copyright   2010, 4worx
 * @version     $Id: Cron.php 8367 2010-10-30 06:57:03Z tpater $
 */

/**
 * Controller for running scripts on interval
 *
 */
class Scripts_Controller_Cron extends Zend_Controller_Action
{
    /**
     * Initializes the controller
     *
     * @return void
     * @throws Glitch_Exception
     */
    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        // Make sure we're being called as a CLI script
        if (php_sapi_name() != 'cli') {
            throw new Glitch_Exception('Bad request');
        }
    }

    /**
     * Disable method; not in use
     *
     * @return void
     * @throws Glitch_Exception
     */
    public function indexAction()
    {
        throw new Glitch_Exception('Bad action');
    }
}
