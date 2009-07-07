<?php
class Vps_Controller_Action_Trl_WebEditController extends Vps_Controller_Action_Trl_VpsEditController
{
    protected $_modelName = 'Vps_Trl_Model_Web';

    protected function _getLanguage()
    {
        $config = Zend_Registry::get('config');
        return $config->webCodeLanguage;
    }
}
