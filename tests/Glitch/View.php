<?php
class Glitch_ViewTest extends PHPUnit_Framework_TestCase
{
    public function testRendererInterface()
    {
        $this->markTestSkipped('ZF2 is not available during Glitch tests.');
        $this->assertTrue(Glitch_View instanceof Zend\View\Renderer\RendererInterface);
    }
}
