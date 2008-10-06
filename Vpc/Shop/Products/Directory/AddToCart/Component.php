<?php
class Vpc_Shop_Products_Directory_AddToCart_Component extends Vpc_Shop_AddToCart_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['processInput'] = false;
        return $ret;
    }

    protected function _initForm()
    {
        $addToCart = $this->getData()->parent->getChildComponent('_'.$this->getData()->row->id)
            ->getChildComponent('-addToCart');

        $this->_form = Vpc_Abstract_Form::createComponentForm($addToCart->componentClass,
                'order'.$this->getData()->parent->row->id);

        parent::_initForm();
    }
    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $row->add_component_id = $this->getData()->parent
            ->getChildComponent('_'.$this->getData()->row->id)
            ->getChildComponent('-addToCart')->dbId;
    }

    protected function _getProduct()
    {
        return $this->getData()->row;
    }
}
