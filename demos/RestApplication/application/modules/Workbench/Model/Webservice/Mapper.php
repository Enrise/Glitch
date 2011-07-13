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
 * @package     Workbench_Model
 * @subpackage  Workbench_Model_Webservice
 * @author      4worx <info@4worx.com>
 * @copyright   2010, 4worx
 * @version     $Id: Mapper.php 12807 2011-03-21 16:14:41Z jthijssen $
 */

/**
 * Webservice mapper
 *
 * @category    Mainflow
 * @package     Workbench_Model
 * @subpackage  Workbench_Model_Webservice
 */
class Workbench_Model_Webservice_Mapper extends Mainflow_Model_DbMapperAbstract
{
    /**
     * Creates an entity
     *
     * @return void
     * @throws BadMethodCallException
     */
    protected function _create()
    {
        // There is no entity
        throw new BadMethodCallException();
    }

    /**
     * Create resultset
     *
     * @param array $data
     * @return Workbench_Model_CostCenter_Resultset
     */
    protected function _createResultSet($data)
    {
        return new Workbench_Model_Webservice_Resultset($data, $this);
    }

    /**
     * Fetch all webservices
     *
     * @return array
     */
    public function fetchAll()
    {
        $settings = Glitch_Registry::getConfig();
        $uri = "/";


        //print_r($uri);

        /*
         * Note: webservice array format and possibilities
         * unique_name => array(
         *    uri (required),
         *    label (required),
         *    versions (required) => array()
         *    form (optional)
         * )
         */
        /*
        $webservices = array(
            'excel/index' => array(
                'uri' => $uri . 'excel/index',
                'label' => 'Excel test',
                'versions' => array('1.0')
            ),
        );
        */
        $form = array(
            array(
                'label' => 'Form Workbench',
                'versions' => array('1.0'),
                'form' => new Workbench_Form_Language()
            ),
            array(
                'label'=>'XML',
                'versions' => array('1.0'),
                'form' => new Workbench_Form_Xml()
            )
        );

        return $form;
    }

    /**
     * Fetch all versions of the webservices
     *
     * @return array
     */
    public function fetchAllVersions()
    {
        $webservices = $this->fetchAll();
        $versions = array();
        foreach ($webservices as $webservice) {
            $versions = array_merge($versions, $webservice['versions']);
        }
        $versions = array_unique($versions);

        //Sort the versions from newest to oldest
        rsort($versions);

        return $versions;
    }

    /**
     * Fetch all versions by webservice
     *
     * @param string $webserviceUrl
     * @return array
     */
    public function fetchVersionsByWebservice($webserviceUrl)
    {
        $webservices = $this->fetchAll();
        if (!isset($webservices[$webserviceUrl]['versions'])) {
            throw new RuntimeException('No versions available for the webservice: ' . $webserviceUrl);
        }
        $versions = $webservices[$webserviceUrl]['versions'];

        rsort($versions);

        return $versions;
    }

    /**
     * Fetch all webservices by version
     *
     * @param string $version
     * @return array
     * @throws RuntimeException
     */
    public function fetchWebservicesByVersion($version)
    {
        $allWebservices = $this->fetchAll();
        $webservices = array();
        foreach ($allWebservices as $key => $value) {
            if (in_array($version, $value['versions'])) {
                $webservices[$key] = $value;
            }
        }
        if (0 === count($webservices)) {
            throw new RuntimeException('No webservices available for the version: ' . $version);
        }
        return $webservices;
    }

    /**
     * Finds the form for the specified webservice
     *
     * @param string $webserviceUrl
     * @return Zend_Form|false
     */
    public function findFormByWebservice($webserviceUrl)
    {
        $webservices = $this->fetchAll();
        if (!isset($webservices[$webserviceUrl]['form'])) {
            return false;
        }
        return $webservices[$webserviceUrl]['form'];
    }
}