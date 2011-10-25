<?php
class Kwc_List_ChildPages_Teaser_Controller extends Kwf_Controller_Action_Auto_Kwc_Grid
{
    protected $_buttons = array();
    protected $_model = 'Kwc_List_ChildPages_Teaser_Model';
    protected $_defaultOrder = array('field' => 'pos', 'direction' => 'ASC');

    protected function _initColumns()
    {
        $c = Kwc_Abstract::getChildComponentClass($this->_getParam('class'), 'child');
        $childOwnModel = Kwc_Abstract::getSetting($c, 'ownModel');

        $this->_columns->add(new Kwf_Grid_Column('pos'));
        if ($childOwnModel) {
            $this->_columns->add(new Kwf_Grid_Column_Checkbox('visible', ''))
                ->setData(new Kwc_List_ChildPages_Teaser_ChildVisibilityData($childOwnModel, $this->_getParam('componentId')))
                ->setRenderer('booleanTickCross')
                ->setHeaderIcon(new Kwf_Asset('visible'))
                ->setTooltip('Visibility');
        }
        $this->_columns->add(new Kwf_Grid_Column('name', trlKwf('Page name'), 200));
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('parent_component_id', $this->_getParam('componentId'));
        $ret->whereEquals('ignore_visible', true);
        return $ret;
    }
}
