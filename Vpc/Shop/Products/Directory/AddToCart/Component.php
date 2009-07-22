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
        $id = $this->_getProduct()->id;
        $addToCart = $this->getData()->parent->getComponent()->getItemDirectory()
            ->getChildComponent('_'.$id)
            ->getChildComponent('-addToCart');

        $this->_form = Vpc_Abstract_Form::createComponentForm($addToCart->componentClass,
                'order'.$id);

        parent::_initForm();
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $id = $this->_getProduct()->id;
        $row->add_component_id = $this->getData()->parent->getComponent()->getItemDirectory()
            ->getChildComponent('_'.$id)
            ->getChildComponent('-addToCart')->dbId;
    }
}
