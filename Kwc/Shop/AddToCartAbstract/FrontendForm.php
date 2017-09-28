<?php
class Kwc_Shop_AddToCartAbstract_FrontendForm extends Kwf_Form
{
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        if (!$row->amount) {
            $row->amount = 1;
        }
    }
}
