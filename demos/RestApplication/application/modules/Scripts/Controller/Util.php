<?php
/**
 * Controller for running utility scripts
 *
 */
class Scripts_Controller_Util extends Zend_Controller_Action
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

    /**
     * Test this by calling cli.php -r scripts.util.example -e <environment>
     *
     * @return void
     */
    public function exampleAction()
    {
        echo 'It works!';
    }
}