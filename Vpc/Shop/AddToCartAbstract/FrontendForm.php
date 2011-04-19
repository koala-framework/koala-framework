<?php
class Vpc_Shop_AddToCartAbstract_FrontendForm extends Vps_Form
{
    protected function _beforeInsert(&$row)
    {
        $row->amount = 1;
    }
}
