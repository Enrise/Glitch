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
 * @package     Workbench_Service
 * @author      4worx <info@4worx.com>
 * @copyright   2010, 4worx
 * @version     $Id: Consumer.php 12807 2011-03-21 16:14:41Z jthijssen $
 */

/**
 * Simple HTTP client wrapper for querying the Mainflow webservices
 *
 * @category    Mainflow
 * @package     Workbench_Service
 */
class Workbench_Service_Consumer extends Zend_Http_Client
{
    /**
     * Sets the appropriate accept header
     *
     * @param string $format
     * @param string $version
     * @return void
     */
    public function setAcceptHeader($format = 'xml', $version = '1.0')
    {
        /*
        $header = sprintf(
            '%s+%s; version=%s', Workbench_Model_Webservice_Constants::HTTP_HEADER_APPLICATION_TYPE, $format, $version
        );
        */
        
        $header= Workbench_Model_Webservice_Constants::HTTP_HEADER_APPLICATION_TYPE . '+' . $format;
       

        $this->setHeaders('Accept', $header);
    }

    /**
     * Send the HTTP request and return an HTTP response object
     *
     * @param string $method
     * @return Zend_Http_Response
     */
    public function request($method = null, $req = null)
    {
        if (null === $method) {
            $method = $this->method;
        }

    
        // Get OAuth key and secret from the config
        $settings = Glitch_Registry::getSettings();
        
        $useOAuth = $req->getPost('OAuth');
        $key = $req->getPost(Workbench_Form_Workbench::CONSUMER_KEY);
        $secret = $req->getPost(Workbench_Form_Workbench::CONSUMER_SECRET);

        if (isset($useOAuth) && !empty($useOAuth) && !empty($key) && !empty($secret)) {

            $consumer = new OAuthConsumer(
                $key,
                $secret
            );
            $signMethod = new OauthSignatureMethod_HMAC_SHA1();
    
            $request = OAuthRequest::from_consumer_and_token(
                $consumer, null, $method, $this->getUri(), $this->getParametersPost()
            );
            $request->sign_request($signMethod, $consumer, null);
    
            $this->setHeaders(array($request->to_header('mainflow')));
        }
        
        return parent::request($method);
    }

    /**
     * Gets the request headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Gets the request POST parameters
     *
     * @return array
     */
    public function getParametersPost()
    {
        return $this->paramsPost;
    }

    /**
     * Gets all headers as a string
     *
     * @param string $linebreak
     * @return string
     * @throws RangeException
     */
    public function getHeadersAsString($linebreak = PHP_EOL)
    {
        $str = '';

        // Iterate over the headers and stringify them
        foreach ($this->getHeaders() as $key => $value) {
            if (is_string($value)) {
                $str .= "{$key}: {$value}{$linebreak}";
            } else if (is_array($value)) {
                if (count($value) != 2) {
                    // Shouldn't happen, but don't let it pass silently if it does
                    throw new RangeException();
                }
                $str .= "{$value[0]}: {$value[1]}{$linebreak}";
            }
        }

        return trim($str);
    }
}