<?php
class Kwc_Shop_AddToCartAbstract_Trl_Form_Component extends Kwc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlKwfStatic('add to cart');
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['cssClass'] = self::getCssClass($this->getData()->parent->componentClass);
        return $ret;
    }

    protected function _initForm()
    {
        $this->_form = $this->getData()->parent->chained->getComponent()->getForm();
        $this->_form->trlStaticExecute($this->getData()->getLanguage());
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);

        $orders = Kwf_Model_Abstract::getInstance('Kwc_Shop_Cart_Orders');
        $row->shop_order_id = $orders->getCartOrderAndSave()->id;
        $row->add_component_id = $this->getData()->parent->dbId;
        $row->add_component_class = $this->getData()->parent->componentClass;
    }
}

