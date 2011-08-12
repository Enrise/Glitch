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
 * Resource for setting view options
 *
 * @category    Glitch
 * @package     Glitch_Application
 * @subpackage  Resource
 */
class Glitch_Application_Resource_View extends Zend_Application_Resource_View implements Glitch_Application_Resource_ModuleInterface
{
    /**
     * Name of the view class
     *
     * This name may be overridden by extending classes in order to provide
     * a custom class. Set the new value in the child init().
     *
     * @var string
     */
    protected $_className = 'Glitch_View';

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return Zend_View
     */
    public function init()
    {
        return $this->getView();
    }

    /**
     * Retrieves the view object
     *
     * @return Zend_View
     * @throws Glitch_Application_Resource_Exception
     */
    public function getView()
    {
        if (null === $this->_view)
        {
            $options = $this->getOptions();

            $this->_view = new $this->_className($options);

            // Dynamic class loading; perform sanity check
            if (!$this->_view instanceof Zend_View_Interface)
            {
                throw new Glitch_Application_Resource_Exception('Class is not a valid view instance');
            }

            if (isset($options['doctype']))
            {
                // Explicitly set doctype; not done by Zend_View!
                $this->_view->doctype()->setDoctype(strtoupper($options['doctype']));
            }
            if (isset($options['encoding']))
            {
                $charset = $this->_view->escape($options['encoding']);
                $this->_view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=' . $charset);
            }

            // Always generate notice if undefined variables are being used
            $this->_view->strictVars(true);
        }
        return $this->_view;
    }

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

        // Performance: use absolute path, so that ZF doesn't need to resolve it, e.g.
        // "/some/path/modules/Default/View/"
        $path = GLITCH_MODULES_PATH
              . DIRECTORY_SEPARATOR
              . $module
              . DIRECTORY_SEPARATOR
              . Glitch_View::PATH_VIEW
              . DIRECTORY_SEPARATOR;

        // Use 'add' rather than 'set': other paths may have been set previously,
        // e.g. in the config; don't overwrite those
        $this->getView()->addBasePath($path, $module . '_' . Glitch_View::PATH_VIEW);
    }
}