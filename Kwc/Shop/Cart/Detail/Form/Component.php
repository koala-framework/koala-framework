<?php
class Vpc_Shop_Cart_Detail_Form_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['success'] = false;
        return $ret;
    }

    protected function _initForm()
    {
        $addToCart = Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->getData()->parent->row->add_component_id);
        if ($addToCart) {
            $f = $addToCart->getComponent()->getForm();
            $this->_form = clone $f;
            $this->_form->setName('order'.$this->getData()->parent->row->id);
            $this->_form->setId($this->getData()->parent->row->id);
        }
    }
}
