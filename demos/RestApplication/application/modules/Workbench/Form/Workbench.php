<?php
/**
 * IDM
 *
 * This source file is proprietary and protected by international
 * copyright and trade secret laws. No part of this source file may
 * be reproduced, copied, adapted, modified, distributed, transferred,
 * translated, disclosed, displayed or otherwise used by anyone in any
 * form or by any means without the express written authorization of
 * 4worx software innovators BV (www.4worx.com)
 *
 * @category    IDM
 * @package     Workbench_Form
 * @author      4worx <info@4worx.com>
 * @copyright   2010, 4worx
 * @version     $Id: Workbench.php 12818 2011-03-22 13:12:56Z jthijssen $
 */

/**
 * Workbench form
 *
 * @category    IDM
 * @package     Workbench_Form
 */
class Workbench_Form_Workbench extends Zend_Form
{
    /**#@+
     * Constants for workbench form
     *
     * @var string
     */
    const WEBSERVICE = 'Form';
    const OAUTH = 'OAuth';
    const REST_URI = 'rest_uri';
    const METHOD = 'method';
    const VERSION = 'version';
    const REQUEST_HEADER = 'request_header';
    const REQUEST_BODY = 'request_body';
    const RESPONSE_HEADER = 'response_header';
    const RESPONSE_BODY = 'response_body';
    const BODY = 'body';
    const FORMAT = 'format';
    const METHOD_GET = 'get';
    const METHOD_POST = 'post';
    const METHOD_PUT = 'put';
    const METHOD_DELETE = 'delete';
    const METHOD_OPTIONS = 'options';
    const QUERY_STRING = 'query_string';
    const SERVICE_ID = 'service_id';
    const CONSUMER_KEY = 'consumer_key';
    const CONSUMER_SECRET = 'consumer_secret';
    /**#@-*/

    /**
     * Container for the response value
     *
     * @var mixed
     */
    private $_response;

    /**
     * Initializes the form
     *
     * @return void
     */
    public function init()
    {

        $settings = Glitch_Registry::getConfig();

         $this->setName('workbench');

         $service = new Zend_Form_Element_Select(self::WEBSERVICE);
         $service->setLabel('Forms');

         $restUri = new Zend_Form_Element_Text(self::REST_URI);
         $restUri->setLabel('Rest URL')->setValue('http://' . $_SERVER['HTTP_HOST']);

         $consumer_key = new Zend_Form_Element_Text(self::CONSUMER_KEY);
         $consumer_key->setLabel('Consumer Key');

         $consumer_secret = new Zend_Form_Element_Text(self::CONSUMER_SECRET);
         $consumer_secret->setLabel('Consumer Secret');

         $method = new Zend_Form_Element_Select(self::METHOD);
         $method->setLabel('Method')
                ->setRequired(true);

         $format = new Zend_Form_Element_Select(self::FORMAT);
         $format->setLabel('Format');

         $version = new Zend_Form_Element_Select(self::VERSION);
         $version->setLabel('Version');

        $version->addMultiOption('1.0', '1.0');
                 //->setRequired(true);

        $oAuth = new Zend_Form_Element_Checkbox(self::OAUTH);
        $oAuth->setLabel('OAuth')
              ->setAttrib('id', 'oauth');

         $body = new Zend_Form_Element_Hidden(self::BODY);

         $responseHeader = new Zend_Form_Element_Textarea(self::RESPONSE_HEADER);
         $responseHeader->setLabel('Response headers')
                        ->setAttrib('rows', '')
                        ->setAttrib('cols', '')
                        ->setAttrib('readonly', true);

         $responseBody = new Zend_Form_Element_Textarea(self::RESPONSE_BODY);
         $responseBody->setLabel('Response body')
                      ->setAttrib('rows', '')
                      ->setAttrib('cols', '')
                      ->setAttrib('readonly', true);


         $requestHeader = new Zend_Form_Element_Textarea(self::REQUEST_HEADER);
         $requestHeader->setLabel('Request headers')
                        ->setAttrib('rows', '')
                        ->setAttrib('cols', '')
                        ->setAttrib('readonly', true);

         $requestBody = new Zend_Form_Element_Textarea(self::REQUEST_BODY);
         $requestBody->setLabel('Request body')
                      ->setAttrib('rows', '')
                      ->setAttrib('cols', '')
                      ->setAttrib('readonly', true);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Send request')
               ->removeDecorator('Label');

        $openDiv = new Zend_Form_Element_Button('button');
        $openDiv->setLabel('More information')
               ->setAttrib('onclick', 'ShowHide()')
               ->removeDecorator('Label');

        $cleanRestUrl = new Zend_Form_Element_Button('cleanButton');
        $cleanRestUrl->setLabel('Clean URI')
               ->setAttrib('onclick', 'cleanURI()')
               ->removeDecorator('Label');


        $elements = array($service, $method, $format, $version,$oAuth, $consumer_key, $consumer_secret, $restUri, $body, $responseHeader,
                          /*$responseBody, */ $requestHeader, $requestBody, $submit,$cleanRestUrl,$openDiv);
        $this->addElements($elements);

        $div = new Mainflow_Form_Element_Div(self::RESPONSE_BODY);
        $this->addElement($div);

        $this->addDisplayGroup(
            array(
                self::WEBSERVICE,
                self::METHOD,
                self::FORMAT,
                self::VERSION,
                self::OAUTH,
                self::CONSUMER_KEY,
                self::CONSUMER_SECRET
            ),
            'top',
            array('class' => 'top')
        );

        $this->addDisplayGroup(
            array(
                self::REST_URI,
                'cleanButton',
                'button',
                'submit'
            ),
            'top2',
            array('class' => 'top2')
        );

        $this->addDisplayGroup(
            array(self::BODY),
            'leftcol',
            array('class' => 'leftcol')
        );

        $this->addDisplayGroup(
            array(self::RESPONSE_BODY),
            'center',
            array('class' => 'center')
        );

        $this->addDisplayGroup(
            array(
                self::REQUEST_HEADER,
                self::REQUEST_BODY,
                self::RESPONSE_HEADER),
            'rightcol',
            array('class' => 'rightcol')
        );

        $this->addDisplayGroup(
            array('submit'),
            'bottom',
            array('class' => 'bottom')
        );
    }

    /**
     * Handle the form when submitted
     *
     * @param Zend_Controller_Request_Abstract$request
     * @param array $extra
     * @return Workbench_Form_Workbench
     */
    public function process(Zend_Controller_Request_Abstract $request, array $extra, $view)
    {


        $useOAuth = $request->getPost(self::OAUTH);

        $this->populateWebservices($request->getPost(self::VERSION));
        $this->populateMethods();
        $this->populateFormats();
        $this->populateVersions($request->getParam(self::WEBSERVICE));
        $this->populateBody($request);

        $rest_uri = $request->getPost(self::REST_URI);
        if (empty($rest_uri)) {
            return;
        }

        // Only check if form is valid when submit was pressed
        if (!$request->isPost() || strlen($request->getPost('submit')) == 0) {
            return $this;
        }

        $formData = $request->getPost();

        $formDataTmp = $formData;
        unset($formDataTmp['subform']);
        if (!$this->isValid($formDataTmp)) {

            $this->getElement(self::RESPONSE_HEADER)->setValue('');
            $this->getElement(self::RESPONSE_BODY)->setValue('');

            return $this;
        }


        $values = $this->getValidValues($formData);

        $client = new Workbench_Service_Consumer();

        $resultSet = new Workbench_Model_Webservice_Resultset();
        $entities = $resultSet->fetchAll();

        // Determine the uri
        //$uri = $values[self::WEBSERVICE];
        //$uri = $entities[$values[self::WEBSERVICE]]['uri'];
        /*
        $uri = $entities[$values[self::REST_URI]]['uri'];
        if (strlen($values[self::SERVICE_ID]) > 0) {
            $uri .= '/' . urlencode($values[self::SERVICE_ID]);
        }
        */
        $settings = Glitch_Registry::getConfig();
        $client->setUri($values['rest_uri']);


        /*
        // Check for incoming query string
        $queryString = $values[self::QUERY_STRING];
        if (strlen($queryString) > 0) {
            $client->setUri($uri . "?" . $queryString);
        }
        */

        // Determine the method
        $method = $values[self::METHOD];
        $methods = array(
            self::METHOD_POST => Zend_Http_Client::POST,
            self::METHOD_PUT => Zend_Http_Client::PUT,
            self::METHOD_DELETE => Zend_Http_Client::DELETE,
            self::METHOD_GET => Zend_Http_Client::GET,
            self::METHOD_OPTIONS => Zend_Http_Client::OPTIONS
        );
        if (!isset($methods[$method])) {
            $method = self::METHOD_GET; // Fallback
        }
        $method = $methods[$method];

        $client->setMethod($method);
        //$client->setAcceptHeader($values[self::FORMAT], $values[self::VERSION]);

        $client->setAcceptHeader($values[self::FORMAT], '1.0');

        // Set post params if there is a subform
        if (isset($values['subform']) &&
           (self::METHOD_POST === $values[self::METHOD] ||
            self::METHOD_PUT === $values[self::METHOD])) {
            foreach ($values['subform'] as $subform) {
                foreach ($subform as $key => $value) {
                    // When etag is found, we need to add it to the header, not to the post body
                    if ($key == "etag") {
                        $client->setHeaders('If-Match', $value);
                        continue;
                    }

                    if($key == 'plain_xml') {
                        $client->setRawData($value);
                    }

                    // @todo Temp fix: remove empty fields, in order to update only non-empty fields
                    if (strlen($value) != 0 && $key != 'plain_xml') {
                        $client->setParameterPost($key, $value);
                    }
                }
            }
        }

        $response = false;
        try {
            $response = $client->request(null, $request);
            $header = $response->getHeadersAsString();
            $body = $response->getBody();
        } catch (Zend_Http_Client_Exception $e) {
            $header = 'An error occurred: ' . $e->getMessage();
            $body = $header;
        }

        // Turn parameters into a string for pretty display
        $requestBody = '';
        foreach ($client->getParametersPost() as $key => $value) {
            $requestBody .= $key . ': ' . $value . PHP_EOL;
        }

        // Set the request and response values in the form
        $this->getElement(self::REQUEST_HEADER)->setValue($client->getHeadersAsString());
        $this->getElement(self::REQUEST_BODY)->setValue(trim($requestBody));
        $this->getElement(self::RESPONSE_HEADER)->setValue($header);

        $body = htmlentities($body, ENT_NOQUOTES);

        $body = preg_replace(
            '~href="((?:[a-z]+://)?(?:[a-z\d-]+)+[a-z]{2,6}(?::\d+)?(?(?=/)(?:/(?:[\w().\~-]+|%[a-f\d]{2})*)+' .
            '(?:\?(?:[][@!$&\'()*+,;=\w.\~-]+|%[a-f\d]{2})*)?(?:#(?:[][@!$&\'()*+,;=\w.\~-]+|%[a-f\d]{2})*)?))' .
            '(.*?)\"~i',
            'href="<a href="javascript:setRestUrl(\'${1}${2}\');">${1}${2}</a>"',
            $body
        );

        $this->getElement(self::RESPONSE_BODY)->setValue(nl2br($body));

        $this->_setResponse($response);

        $this->getElement('request_header')->setValue(print_r($client->getHeaders(), true));
        $this->getElement('request_body')->setValue(print_r($client->getParametersPost(), true));

        return $this;
    }

    /**
     * Populate the webservices dropdown
     *
     * @param string $version
     * @return Zend_Form_Element_Select
     */
    public function populateWebservices($version = null)
    {
        $model = new Workbench_Model_Webservice_Resultset();

        $service = $this->getElement(self::WEBSERVICE);
        $service->addMultiOption('', 'Choose...');

        if (null !== $version && strlen($version) > 0) {
            $webservices = $model->fetchWebservicesByVersion($version);
        } else {
            $webservices = $model->fetchAll();
        }
        foreach ($webservices as $key => $value) {
            $service->addMultiOption($key, $value['label']);
        }
        return $service;
    }

    /**
     * Populate the methods dropdown
     *
     * @return Zend_Form_Element_Select
     */
    public function populateMethods()
    {
        $method = $this->getElement(self::METHOD);
        $method->addMultiOptions(
            array(
                self::METHOD_GET => 'GET',
                self::METHOD_POST => 'POST',
                self::METHOD_PUT => 'PUT',
                self::METHOD_DELETE => 'DELETE',
                self::METHOD_OPTIONS => 'OPTIONS'
            )
        );
        return $method;
    }

    /**
     * Populate the formats dropdown
     *
     * @return Zend_Form_Element_Select
     */
    public function populateFormats()
    {
        $format = $this->getElement(self::FORMAT);
        $format->addMultiOptions(
            array(
                'xml' => 'XML',
                'json' => 'JSON',
                'html' => 'HTML'
            )
        );
        return $format;
    }

    /**
     * Populate the versions dropdown
     *
     * @param string $webservice
     * @return Zend_Form_Element_Select
     */
    public function populateVersions($webservice = null)
    {
        $model = new Workbench_Model_Webservice_Resultset();

        $version = $this->getElement(self::VERSION);
        //$version->addMultiOption('', 'Choose...');

        if (null !== $webservice && strlen($webservice) > 0) {
            $versions = $model->fetchVersionsByWebservice($webservice);
        } else {
            $versions = $model->fetchAllVersions();
        }
        foreach ($versions as $versionNumber) {
            $version->addMultiOption($versionNumber, $versionNumber);
        }

        return $version;
    }

    /**
     * Populate the body
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function populateBody(Zend_Controller_Request_Abstract $request)
    {
        $method = $request->getPost('method');

        // Only show subform when method is POST
        if (null === $method || (self::METHOD_POST !== $method && self::METHOD_PUT !== $method)) {
            return;
        }

        $webservice = $request->getPost(self::WEBSERVICE);
        if (null === $webservice) {
            return;
        }

        $model = new Workbench_Model_Webservice_Resultset();
        $form = $model->findFormByWebservice($webservice);
        if (false === $form) {
            return;
        }

        $this->addSubForm($form, 'subform');
        $subform = $this->getSubForm('subform');

        // Populate the form if id is given
        $id = $request->getParam(self::SERVICE_ID);
        if (null !== $id && strlen($id) > 0) {
            $resultSet = new Workbench_Model_Webservice_Resultset();
            $entities = $resultSet->fetchAll();

            // Determine the uri
            //$uri = $entities[$request->getPost(self::WEBSERVICE)]['uri'] . '/' . urlencode($id);
            $uri = $request->getPost(self::REST_URI);
            // Get the data for the given id
            $client = new Workbench_Service_Consumer();
            $client->setUri($uri);
            $client->setMethod(Zend_Http_Client::GET);
            $client->setAcceptHeader('xml', $request->getPost(self::VERSION));

            $response = $client->request(null, $request);
            $xml = simplexml_load_string($response->getBody());
            $populate = array();

            // Get the XML key/values and populate the form
            foreach ($xml as $key => $value) {
                $populate[$key] = $value[0];
            }
            $subform->populate($populate);

            // Add etag hidden field to the subform
            $etag = new Zend_Form_Element_Hidden('etag');
            $etag->setValue($response->getHeader('ETAG'));
            $subform->addElement($etag);
        }

        $subform->removeDecorator('Form');
        $subElements = $subform->getElements();
        foreach ($subElements as $element) {
            // Forms may not filter or validate prematurely - that's up to the webservice!
            $element->clearFilters();
            $element->setRequired(false);
            $element->clearValidators();

            $element->setBelongsTo('subform');
        }

        $this->getDisplayGroup('leftcol')->removeElement(self::BODY);
        $this->getDisplayGroup('leftcol')->addElements($subElements);

        $submit = $request->getPost('submit');
        if (null !== $submit && strlen($submit) > 0) {
            $subform->clearDecorators();
        }
    }

    /**
     * Check is a form was posted, valid and had a succesful response
     *
     * @return bool
     */
    public function isSuccessful()
    {
        if (Glitch_Request::isPost() && !$this->isErrors() && $this->hasResponse()) {
            return (bool) $this->getResponse();
        }
        return false;
    }

    /**
     * Sets a response
     *
     * @param $response
     * @return void
     */
    protected function _setResponse($response)
    {
        $this->_response = $response;
    }

    /**
     * Get the response from the form process
     *
     * Either the number of rows affected or the id new inserted
     *
     * @return integer
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Check if the form contains a response other then null
     *
     * @return bool
     */
    public function hasResponse()
    {
        return (bool) $this->getResponse();
    }
}
