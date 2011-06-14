<?php


include '../tests/TestHelper.php';

class LocationTest extends Glitch_Test_PHPUnit_RestControllerTestCase
{
/*    public function testCollectionGetAction()
    {
         $response = $this->_testDispatch('GET','/decision/locations', 'xml', array(), 200,
                                          'decision', 'Decision_Controller_Location', 'collectionGetAction');
        $this->assertQueryCount('locations location', 32);
    }

    public function testResourceGetAction()
    {
        Glitch_Controller_Front::getInstance()->throwExceptions(false);
         $response = $this->_testDispatch('GET','/decision/location/5', 'xml', array(), 200,
                                          'decision', 'Decision_Controller_Location', 'resourceGetAction');
    }*/


/*public function resourceGetAction ()
    {
        $mapper = new Decision_Model_Location_Mapper();
        $location = $mapper->findByAid($this->getRequest()->getResource());

        $mapper = new Decision_Model_Join_Mapper();
        $led = $mapper->findByLED($location);
        if (! $led instanceof Decision_Model_Join_Entity) {
            // @TODO: Throw 404
            throw new Exception("404");
        }
        return array('data' => array('led' => $led));
    }*/


}
