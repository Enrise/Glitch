<?php
class Glitch_Controller_Response_Renderer
{
    protected static $_rendererHelperBroker;

    public function renderResponse($response, $vars, $controller, $request)
    {
        // Move the requested output format to the response
        if(($format = $request->getParam('format')) != null) {
            $response->setOutputFormat($format);
        }

        if(!is_array($vars)) {
            $vars = array('data' => array());
        } elseif(!isset($vars['data'])) {
            $vars['data'] = array();
        }

        $filename = $this->_getRenderScriptName($response, $request, $controller);

        $includePaths = explode(PATH_SEPARATOR, '.' . PATH_SEPARATOR . get_include_path());
        $path = false;
        foreach($includePaths as $incpath) {
            if(file_exists($incpath . '/' . $filename)) {
                $path = $incpath . '/' . $filename;
                break;
            }
        }

        if($path === false) {
            if($response->hasSubResponseRenderer()) {
                throw new Glitch_Controller_Exception(
                    'A SubResponseRenderer was set but could not be located. '
                   .'Looked for "'.$filename.'" in: ' . get_include_path()
                );
            }

            $filename = 'Glitch/Controller/Response/Renderer/'
                      . ucfirst($response->getOutputFormat()) . '.php';
        }

        return static::renderFile($filename, $vars, $response);
    }

    protected function _getRenderScriptName(
                            $response,
                            Zend_Controller_Request_Abstract $request,
                            $controller)
    {
        $filename = ucfirst($request->getModuleName()) . '/View/Script/'
                  . implode('/', Glitch_Controller_Dispatcher_Rest::getClassElements($request)) . '/'
                  . ucfirst($request->getActionName()) . '.';

        if($response->hasSubResponseRenderer()) {
            $filename .= $response->getSubResponseRenderer() . '.';
        }

        return $filename . $response->getOutputFormat() . '.phtml';
    }


    public static function renderFile($file, $vars, $response = null)
    {
        $func = function($_vars, $_filename) {
            extract($_vars);
            unset($_vars);
            return include $_filename;
        };

        $vars['helper'] = static::getRendererHelperBroker();
        $vars['responseObject'] = $response;
        $vars = array_merge($vars, $vars['helper']->getShortCuts());

        ob_start();
        $func($vars, $file);

        return ob_get_clean();
    }


    public static function getRendererHelperBroker()
    {
        if (null == static::$_rendererHelperBroker) {
            static::$_rendererHelperBroker = new Glitch_Controller_Response_Renderer_HelperBroker();
        }

        return static::$_rendererHelperBroker;
    }

    public static function setRendererHelperBroker($broker)
    {
        static::$_rendererHelperBroker = $broker;
    }
}
