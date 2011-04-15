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
 * @subpackage  Action_Helper
 * @author      4worx <info@4worx.com>
 * @copyright   2010, 4worx
 * @version     $Id$
 */

/**
 * Action helper with utility methods for handling responses
 *
 * @category    Glitch
 * @package     Glitch_Controller
 * @subpackage  Action_Helper
 */
class Glitch_Controller_Action_Helper_Response extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Returns the current content type
     *
     * @return string
     */
    public function getContentType()
    {
        $response = $this->getResponse();

        // First, inspect the raw headers
        foreach ($response->getRawHeaders() as $header)
        {
            if (strncasecmp($header, 'content-type', 12) == 0)
            {
                return trim(substr($header, 13));
            }
        }

        // Assume the content type is HTML, even if not set
        $type = 'text/html';

        // Second, inspect the regular headers
        foreach ($response->getHeaders() as $header)
        {
            // The regular headers allow for multiple content types - use the last one set
            if (strcasecmp($header['name'], 'content-type') == 0)
            {
                $type = $header['value'];
            }
        }

        return $type;
    }

    /**
     * Tests whether the application is generating HTML
     *
     * @return boolean
     */
    public function isHtml()
    {
        return (strncasecmp($this->getContentType(), 'text/html', 9) == 0);
    }

    /**
     * Tests whether the application is generating JSON
     *
     * @return boolean
     */
    public function isJson()
    {
        return (strncasecmp($this->getContentType(), 'application/json', 16) == 0);
    }
}