<?php
class Kwc_List_ChildPages_Teaser_Controller extends Kwf_Controller_Action_Auto_Kwc_Grid
{
    protected $_buttons = array('save');
    protected $_model = 'Kwc_List_ChildPages_Teaser_Model';
    protected $_defaultOrder = array('field' => 'pos', 'direction' => 'ASC');

    protected function _initColumns()
    {
        $cmp = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible'=>true, 'limit'=>1));
        $this->_getModel()->updatePages($cmp);

        $this->_columns->add(new Kwf_Grid_Column('pos'));
        $this->_columns->add(new Kwf_Grid_Column('child_id'));
        $this->_columns->add(new Kwf_Grid_Column('name', trlKwf('Page name'), 200))
            ->setData(new Kwc_List_ChildPages_Teaser_ChildPageNameData());
        $this->_columns->add(new Kwf_Grid_Column_Visible('visible'));

    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('component_id', $this->_getParam('componentId'));
        return $ret;
    }
}
