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
 * @package     Glitch_View
 * @subpackage  Helper
 * @author      4worx <info@4worx.com>
 * @copyright   2010, 4worx
 * @version     $Id$
 */

/**
 * Helper for escaping a JavaScript value
 *
 * @category    Glitch
 * @package     Glitch_View
 * @subpackage  Helper
 */
class Glitch_View_Helper_EscapeJs extends Zend_View_Helper_Abstract
{
    /**
     * Returns the escaped JS value
     *
     * @param string $value
     * @return string
     */
    public function escapeJs($value)
    {
        $value = addslashes($value);
        $value = str_replace(array("\r\n", "\n"), '\n', $value);

        return $value;
    }
}