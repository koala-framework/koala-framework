<?php
class Kwc_List_ChildPages_Teaser_Trl_Controller extends Kwc_List_ChildPages_Teaser_Controller
{
    protected $_model = 'Kwc_List_ChildPages_Teaser_Trl_AdminModel';

    public function preDispatch()
    {
        parent::preDispatch();
        $this->_getModel()->setComponentId($this->_getParam('componentId'));
    }

    protected function _getSelect()
    {
        $ret = Kwf_Controller_Action_Auto_Kwc_Grid::_getSelect();
        return $ret;
    }
}
