<?php
/**
 * Controller der für eigene root-komponenten verwendet werden kann.
 * für selenium-tests.
 * Url: /vps/vpctest/Vpc_Basic_Text_Root/url
 */
class Vpc_TestController extends Vps_Controller_Action
{
    public function indexAction()
    {
        Zend_Registry::get('config')->debug->componentCache->disable = true;

        Vps_Component_Data_Root::setComponentClass($this->_getParam('root'));
        $root = Vps_Component_Data_Root::getInstance();
        $data = $root->getPageByPath($this->_getParam('url'));
        if (!$data) {
            throw new Vps_ComponentNotFoundException("blub");
        }
        $root->setCurrentPage($data);
        $data->getComponent()->sendContent();

        $this->_helper->viewRenderer->setNoRender(true);
    }

    protected function _getResourceName()
    {
        return 'vps_test';
    }
}
