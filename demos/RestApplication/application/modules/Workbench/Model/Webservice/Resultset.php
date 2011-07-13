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
 * @package     Workbench_Model
 * @subpackage  Workbench_Model_Webservice
 * @author      4worx <info@4worx.com>
 * @copyright   2010, 4worx
 * @version     $Id: Resultset.php 8888 2010-11-16 07:53:52Z sdvalk $
 */

/**
 * Webservice resultset
 *
 * @category    IDM
 * @package     Workbench_Model
 * @subpackage  Workbench_Model_Webservice
 */
class Workbench_Model_Webservice_Resultset extends Mainflow_Model_DomainResultSetAbstract
{
    /**
     * Fetch all webservices
     *
     * @return array
     */
    public function fetchAll()
    {
        return $this->getMapper()->fetchAll();
    }

    /**
     * Fetch all versions of the webservices
     *
     * @return array
     */
    public function fetchAllVersions()
    {
        return $this->getMapper()->fetchAllVersions();
    }

    /**
     * Fetch all versions by webservice
     *
     * @param array
     * @return array
     */
    public function fetchVersionsByWebservice($webserviceUrl)
    {
        return $this->getMapper()->fetchVersionsByWebservice($webserviceUrl);
    }

    /**
     * Fetch all webservices by version
     *
     * @param array
     */
    public function fetchWebservicesByVersion($version)
    {
        return $this->getMapper()->fetchWebservicesByVersion($version);
    }

    /**
     * Finds the form for the specified webservice
     *
     * @param string $webserviceUrl
     * @return array
     */
    public function findFormByWebservice($webserviceUrl)
    {
        return $this->getMapper()->findFormByWebservice($webserviceUrl);
    }
}