<?php
class Vpc_ListChildPages_Teaser_Trl_Controller extends Vpc_ListChildPages_Teaser_Controller
{
    protected $_model = 'Vpc_ListChildPages_Teaser_Trl_AdminModel';

    public function preDispatch()
    {
        parent::preDispatch();
        $this->_getModel()->setComponentId($this->_getParam('componentId'));
    }

    protected function _getSelect()
    {
        $ret = Vps_Controller_Action_Auto_Vpc_Grid::_getSelect();
        return $ret;
    }
}
