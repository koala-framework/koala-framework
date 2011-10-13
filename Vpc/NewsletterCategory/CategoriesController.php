<?php
class Vpc_NewsletterCategory_CategoriesController extends Vps_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Vpc_NewsletterCategory_CategoriesModel';
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Grid_Column('category', trlVps('Category'), 200))
            ->setEditor(new Vps_Form_Field_TextField());
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('newsletter_component_id', $this->_getParam('componentId'));
        return $ret;
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row, $submitRow)
    {
        parent::_beforeInsert($row, $submitRow);
        $row->newsletter_component_id = $this->_getParam('componentId');
    }
}
