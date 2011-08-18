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
 * @copyright   2009, 4worx
 * @version     $Id$
 */

/**
 * Concrete class for handling form elements that are using hashes
 *
 * @category    Glitch
 * @package     Glitch_Form
 * @subpackage  Element
 */
interface Glitch_Form_Element_Hash_Interface
{

    /**
     * Add validators to the hash element
     *
     * @return Glitch_Form_Element_Hash_Interface
     */
    public function initCsrfValidator();

    /**
     * Initialize the token for security
     *
     * @return void
     */
    public function initCsrfToken();

    /**
     * Add validators to the hash element
     *
     * @return Glitch_Form_Element_Hash
     */
    public function clear();

}