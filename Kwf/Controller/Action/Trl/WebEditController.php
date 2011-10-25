<?php
class Kwf_Controller_Action_Trl_WebEditController extends Kwf_Controller_Action_Trl_KwfEditController
{
    protected $_modelName = 'Kwf_Trl_Model_Web';

    protected function _getLanguage()
    {
        $config = Zend_Registry::get('config');
        return $config->webCodeLanguage;
    }
}
