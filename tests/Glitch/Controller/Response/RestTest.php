<?php

class Glitch_Controller_Response_RestTest
    extends PHPUnit_Framework_TestCase
{
    
    public function testOutputFormat()
    {
        $response = new Glitch_Controller_Response_Rest();
        
        $this->assertEquals('xml', $response->getOutputFormat());
        
        $response->setOutputFormat('json');
        $this->assertEquals('json', $response->getOutputFormat());
        
        $response->setOutputFormat('foobar');
        $this->assertEquals('foobar', $response->getOutputFormat());
        
        $response->setOutputFormat();
        $this->assertEquals('xml', $response->getOutputFormat());
    }
    
    public function testSubResponseRenderer()
    {
        $response = new Glitch_Controller_Response_Rest();
        
        $this->assertEquals('', $response->getSubResponseRenderer());
        $this->assertFalse($response->hasSubResponseRenderer());
        
        $this->assertEquals($response, $response->setSubResponseRenderer('name'));
        $this->assertEquals('name', $response->getSubResponseRenderer());
        $this->assertTrue($response->hasSubResponseRenderer());
        
        $this->assertEquals($response, $response->setSubResponseRenderer());
        $this->assertEquals('', $response->getSubResponseRenderer());
        $this->assertFalse($response->hasSubResponseRenderer());
    }
    
    public function testRenderBody()
    {
        $response = new Glitch_Controller_Response_Rest();
        $this->assertTrue($response->renderBody());

        $response->renderBody(false);
        $response->setBody('foobarroo');
        
        ob_start();
        $this->assertNull($response->outputBody());
        $this->assertEmpty(ob_get_clean());
        
        $response->renderBody(true);
        $response->setBody('f00b4rr00');
        
        ob_start();
        $this->assertNull($response->outputBody());
        $this->assertEquals('f00b4rr00', ob_get_clean());
    }
    
    public function testInstances()
    {
        $response = new Glitch_Controller_Response_RestTestCase();
        $this->assertInstanceOf('Glitch_Controller_Response_Rest', $response);
        $this->assertInstanceOf('Zend_Controller_Response_Abstract', $response);
    }
}
