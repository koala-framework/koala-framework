<?php
class Kwc_Shop_Cart_Detail_Form_Trl_Component extends Kwc_Form_Trl_Component
{
    public function getAddToCartForm()
    {
        return Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->getData()->parent->chained->row->add_component_id/*, array('subroot'=>$this->getData())*/);

    }

    public function getOrderProductRow()
    {
        return $this->getData()->parent->chained->row;
    }
}
