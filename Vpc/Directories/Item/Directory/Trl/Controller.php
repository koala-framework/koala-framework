<?php
class Vpc_Directories_Item_Directory_Trl_Controller extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array(
        'save',
        'reload',
    );

    protected $_editDialog = array(
        'width' =>  500,
        'height' =>  400
    );

    public function preDispatch()
    {
        parent::preDispatch();
        $url = Vpc_Admin::getInstance($this->_getParam('class'))->getControllerUrl('Form');
        $this->_editDialog['controllerUrl'] = $url;
    }


    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $id = $this->_getParam('componentId');
        $c = Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId($id, array('ignoreVisible'=>true));
        $ret->whereEquals('component_id', $c->chained->dbId);
        return $ret;
    }
}
