<?php
class Vpc_Shop_Cart_Plugins_Voucher_VoucherHistoryController extends Vps_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Vpc_Shop_Cart_Plugins_Voucher_VoucherHistory';

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Vps_Grid_Column_Date('date', trlVps('Date')))
            ->setEditor(new Vps_Form_Field_DateField());
        $this->_columns->add(new Vps_Grid_Column('amount', trlcVps('Amount of Money', 'Amount'), 50))
            ->setRenderer('euroMoney')
            ->setEditor(new Vps_Form_Field_NumberField());
        $ed = new Vps_Form_Field_TextArea();
        $ed->setWidth(300)->setHeight(70);
        $this->_columns->add(new Vps_Grid_Column('comment', trlVps('Comment'), 300))
            ->setRenderer('nl2br')
            ->setEditor($ed);
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('voucher_id', $this->_getParam('voucher_id'));
        return $ret;
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row, $submitRow)
    {
        parent::_beforeInsert($row, $submitRow);
        $row->voucher_id = $this->_getParam('voucher_id');
        $row->order_id = null;
    }

}
