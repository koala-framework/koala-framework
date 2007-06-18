<?php
class Vps_Controller_Action_Assets extends Vps_Controller_Action
{
    public function jsAction()
    {
        $config = Zend_Registry::get('config');
        $dep = new Vps_Assets_JavaScriptDependencies($config->asset->js);

        $dep->addDependencies(new Zend_Config_Ini('../application/config.ini', 'dependencies'));
        $body = $dep->getPackedAll();
//        $body = $dep->getContentsAll();

        $this->_helper->viewRenderer->setNoRender();
        $this->getResponse()
                ->setHeader('Content-Type', 'text/javascript')
                ->setBody($body);
    }
}

