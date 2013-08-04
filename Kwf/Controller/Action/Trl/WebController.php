<?php
class Kwf_Controller_Action_Trl_WebController extends Kwf_Controller_Action_Trl_KwfController
{
    protected $_modelName = "Kwf_Trl_Model_Web";
    protected $_editDialog = array('controllerUrl'=>'/kwf/trl/web-edit',
                                   'width'=>600,
                                   'height'=>550);

    public function indexAction()
    {
        $config = array(
            'controllerUrl' => $this->getRequest()->getBaseUrl().$this->getRequest()->getPathInfo()
        );
        $this->view->ext('Kwf.Trl.Grid', $config);
    }

    protected function _getLanguage()
    {
        $config = Zend_Registry::get('config');
        return $config->webCodeLanguage;
    }
}
