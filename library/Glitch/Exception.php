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
 * @package     Glitch
 * @author      4worx <info@4worx.com>
 * @copyright   2010, 4worx
 * @version     $Id$
 */

/**
 * Exception class that doesn't output a body to the client. For instance during
 * a 404 status code that has to be returned to the client.
 *
 * @category    Glitch
 * @package     Glitch_Exception
 */
class Glitch_Exception
    extends Zend_Exception
    implements Glitch_ExceptionInterface
{
}