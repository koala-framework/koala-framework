<?php
class Kwc_Columns_Trl_Controller extends Kwc_Columns_Controller
{
    protected $_model = 'Kwc_Columns_Model';
    protected function _setModelData()
    {
        $c = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible' => true));
        $this->_model->setData($c->chained->componentClass, $c->chained->componentId);
    }
}
