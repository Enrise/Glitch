<?php

class Glitch_Controller_Response_RendererTest
    extends PHPUnit_Framework_TestCase
{

    public function testXml()
    {
        $file = 'Glitch/Controller/Response/Renderer/Xml.php';
        $vars = array('foobar' => array('foo' => true, 'bar' => false,
                                        'baz' => null, 'john' => 'doe'));
        $response = new Glitch_Controller_Response_Rest();
        $xml = $this->renderFile($file, array('data' => $vars), $response);
        $this->assertEquals($this->_expectedXml, $xml);
    }

    public function testJson()
    {
        $file = 'Glitch/Controller/Response/Renderer/Json.php';
        $vars = array('foobar' => array('foo' => true, 'bar' => false,
                                        'baz' => null, 'john' => 'doe'));
        $response = new Glitch_Controller_Response_Rest();
        $json = $this->renderFile($file, array('data' => $vars), $response);

        $this->assertEquals(
            '{"foobar":{"foo":true,"bar":false,"baz":null,"john":"doe"}}',
            $json
        );
    }

    public function testHtml()
    {
        $file = 'Glitch/Controller/Response/Renderer/Html.php';
        $vars = array('foobar' => array('foo' => true, 'bar' => false,
                                        'baz' => null, 'john' => 'doe'));
        $response = new Glitch_Controller_Response_Rest();
        $html = $this->renderFile($file, array('data' => $vars), $response);

        $this->assertEquals($this->_expectedHtml, $html);
    }

    private function renderFile($file, $vars, $response)
    {
        $func = function($_vars, $_filename, $responseObject) {
            extract($_vars);
            ob_start();
            include $_filename;
            return ob_get_clean();
        };

        return $func($vars, $file, $response);
    }


        private $_expectedXml = <<<'EOD'
<?xml version="1.0"?>
<zend-config xmlns:zf="http://framework.zend.com/xml/zend-config-xml/1.0/">
  <foobar>
    <foo>1</foo>
    <bar></bar>
    <baz></baz>
    <john>doe</john>
  </foobar>
</zend-config>

EOD;

    private $_expectedHtml = <<<'EOD'
array(1) {
  ["foobar"]=>
  array(4) {
    ["foo"]=>
    bool(true)
    ["bar"]=>
    bool(false)
    ["baz"]=>
    NULL
    ["john"]=>
    string(3) "doe"
  }
}

EOD;

}
