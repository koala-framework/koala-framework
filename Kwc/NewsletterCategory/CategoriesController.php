<?php
class Kwc_NewsletterCategory_CategoriesController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Kwc_NewsletterCategory_CategoriesModel';
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('category', trlKwf('Category'), 200))
            ->setEditor(new Kwf_Form_Field_TextField());
        $this->_columns->add(new Kwf_Grid_Column('id', trlKwf('id'), 40));
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('newsletter_component_id', $this->_getParam('componentId'));
        return $ret;
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row, $submitRow)
    {
        parent::_beforeInsert($row, $submitRow);
        $row->newsletter_component_id = $this->_getParam('componentId');
    }
}
