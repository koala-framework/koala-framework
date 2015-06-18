<?php
class Kwc_Shop_AddToCartAbstract_FrontendForm extends Kwf_Form
{
    protected function _beforeInsert($row)
    {
        if (!$row->amount) {
            $row->amount = 1;
        }
    }
}
