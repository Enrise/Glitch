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
 * Overrided Zend_Controller_Action for enabling Controllers to be of type
 * snippet with the default context switched to snippet
 *
 * @category    Glitch
 * @package     Glitch_Controller
 * @subpackage  Action
 */
abstract class Glitch_Controller_Action_Snippet extends Zend_Controller_Action
{
	const CACHE_CONTROL_HEADER_VALUE = 'max-age=%d, public';
    const CACHE_DEFAULT_TTL = 300;

	/**
	 * The default content-type of snippets
	 *
	 * @var string
	 */
	protected $_contentType = 'text/html';

	/**
	 *
	 *
	 * @var Zend_Controller_Action_Helper_ContextSwitch
	 */
	private $_context;

    /**
     * Class constructor
     *
     * The request and response objects should be registered with the
     * controller, as should be any additional optional arguments; these will be
     * available via {@link getRequest()}, {@link getResponse()}, and
     * {@link getInvokeArgs()}, respectively.
     *
     * When overriding the constructor, please consider this usage as a best
     * practice and ensure that each is registered appropriately; the easiest
     * way to do so is to simply call parent::__construct($request, $response,
     * $invokeArgs).
     *
     * After the request, response, and invokeArgs are set, the
     * {@link $_helper helper broker} is initialized.
     *
     * When the broker is instantiated immediately a contextswitch is added
     * for defining the snippet context as the default context
     *
     * Finally, {@link init()} is called as the final action of
     * instantiation, and may be safely overridden to perform initialization
     * tasks; as a general rule, override {@link init()} instead of the
     * constructor to customize an action controller's instantiation.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @param array $invokeArgs Any additional invocation arguments
     * @return void
     */
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
    	$this->setRequest($request)
             ->setResponse($response)
             ->_setInvokeArgs($invokeArgs);
        $this->_helper = new Zend_Controller_Action_HelperBroker($this);

    	$this->_context = $this->_helper->contextSwitch();
    	if (!$this->_context->hasContext('snippet'))
    	{
            $this->_context->addContext('snippet', array(
                'suffix'=>'',
                'headers'=>array(
                    'Content-Type'=>$this->_contentType,
                    'Cache-Control'=>sprintf(self::CACHE_CONTROL_HEADER_VALUE, self::CACHE_DEFAULT_TTL)
                ),
            ));
    	}
        $this->_context->setDefaultContext('snippet');

        $this->init();
    }

    /**
     * After dispatching the action, set the correct headers
     *
     * @return void
     */
    public function postDispatch()
    {
        $headers = $this->_context->getHeaders('snippet');
        if (!empty($headers)) {
            $response = $this->getResponse();
            foreach ($headers as $header => $content) {
                $response->setHeader($header, $content);
            }
        }
    }

    /**
     * Set a specific Cache expire time for the current context
     *
     * @param integer $ttl
     * @return void
     */
    protected function _setCacheTtl($ttl = self::CACHE_DEFAULT_TTL)
    {
        $this->_context->setHeader('snippet', 'Cache-Control',
                                   sprintf(self::CACHE_CONTROL_HEADER_VALUE, intval($ttl)));
    }
}