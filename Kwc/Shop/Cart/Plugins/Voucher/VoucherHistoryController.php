<?php
class Kwc_Shop_Cart_Plugins_Voucher_VoucherHistoryController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Kwc_Shop_Cart_Plugins_Voucher_VoucherHistory';

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Kwf_Grid_Column_Date('date', trlKwf('Date')))
            ->setEditor(new Kwf_Form_Field_DateField());
        $this->_columns->add(new Kwf_Grid_Column('amount', trlcKwf('Amount of Money', 'Amount'), 50))
            ->setRenderer('euroMoney')
            ->setEditor(new Kwf_Form_Field_NumberField());
        $ed = new Kwf_Form_Field_TextArea();
        $ed->setWidth(300)->setHeight(70);
        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment'), 300))
            ->setRenderer('nl2br')
            ->setEditor($ed);
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('voucher_id', $this->_getParam('voucher_id'));
        return $ret;
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row, $submitRow)
    {
        parent::_beforeInsert($row, $submitRow);
        $row->voucher_id = $this->_getParam('voucher_id');
        $row->order_id = null;
    }

}
