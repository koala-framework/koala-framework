<?php
class Kwc_ColumnsResponsive_Trl_Controller extends Kwc_ColumnsResponsive_Controller
{
    protected $_model = 'Kwc_ColumnsResponsive_Model';
    protected function _setModelData()
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('componentId'));
        $this->_model->setData($c->chained->componentClass, $c->chained->componentId);
    }
}
