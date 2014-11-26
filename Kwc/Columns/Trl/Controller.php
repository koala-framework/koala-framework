<?php
class Kwc_Columns_Trl_Controller extends Kwf_Controller_Action_Auto_Kwc_Grid
{
    protected $_buttons = array();
    protected $_hasComponentId = false;
    protected $_sortable = false;
    protected $_defaultOrder = 'pos';

    public function preDispatch()
    {
        $masterComponentClass = Kwc_Abstract::getSetting($this->_getParam('class'), 'masterComponentClass');
        $this->setModel(Kwc_Columns_ModelFactory::getModelInstance(array(
            'componentClass' => $masterComponentClass
        )));
        parent::preDispatch();
    }

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Kwf_Grid_Column('name', trlKwf('Name'), 200));

        // Not visible
        $this->_columns->add(new Kwf_Grid_Column('total_columns'));
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $component = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible' => true));
        $ret->whereEquals('component_id', $component->chained->dbId);
        return $ret;
    }
}
