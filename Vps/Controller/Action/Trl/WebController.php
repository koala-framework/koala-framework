<?php
class Vps_Controller_Action_Trl_WebController extends Vps_Controller_Action_Trl_VpsController
{
    protected $_modelName = "Vps_Trl_Model_Web";
    protected $_editDialog = array('controllerUrl'=>'/vps/trl/web-edit',
                                   'width'=>600,
                                   'height'=>550);

    public function indexAction()
    {
        $config = array(
            'controllerUrl' => $this->getRequest()->getPathInfo()
        );
        $this->view->ext('Vps.Trl.Grid', $config);
    }

    protected function _getLanguage()
    {
        $config = Zend_Registry::get('config');
        return $config->webCodeLanguage;
    }
}