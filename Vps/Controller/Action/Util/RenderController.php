<?php
class Vps_Controller_Action_Util_RenderController extends Vps_Controller_Action
{
    public function renderAction()
    {
        $url = $this->_getParam('url');
        if (!$url) throw new Vps_Exception_Client('Need URL.');
        $parsedUrl = parse_url($url);
        $_GET = array();
        if (isset($parsedUrl['query'])) {
            foreach (explode('&' , $parsedUrl['query']) as $get) {
                if (!$get) continue;
                $pos = strpos($get, '=');
                $_GET[substr($get, 0, $pos)] = substr($get, $pos+1);
            }
        }
        $c = Vps_Component_Data_Root::getInstance()->getPageByUrl($url, null);
        if (!$c) throw new Vps_Exception_Client('Could not find component');

        $process = $c->getRecursiveChildComponents(array(
            'page' => false,
            'flags' => array('processInput' => true)
        ));
        if (Vps_Component_Abstract::getFlag($c->componentClass, 'processInput')) {
            $process[] = $c;
        }
        $postData = array();
        foreach ($process as $i) {
            Vps_Benchmark::count('processInput', $i->componentId);
            if (method_exists($i->getComponent(), 'preProcessInput')) {
                $i->getComponent()->preProcessInput($postData);
            }
        }
        foreach ($process as $i) {
            if (method_exists($i->getComponent(), 'processInput')) {
                $i->getComponent()->processInput($postData);
            }
        }
        echo $c->render(!Vps_Registry::get('config')->debug->componentCache->disable);
        exit;
    }
}
