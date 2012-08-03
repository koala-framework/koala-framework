<?php
class Kwc_Shop_Products_Directory_AddToCart_Component extends Kwc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['processInput'] = false;
        $ret['placeholder']['submitButton'] = trlKwfStatic('add to cart');
        $ret['generators']['child']['component']['success'] = 'Kwc_Shop_AddToCartAbstract_Success_Component';
        return $ret;
    }

    protected function _initForm()
    {
        $id = $this->getData()->row->id;
        $addToCart = $this->getData()->parent->getComponent()->getItemDirectory()
            ->getChildComponent('_'.$id)
            ->getChildComponent('-addToCart');
        $this->_form = $addToCart->getComponent()->getForm();
        $this->_form->setName('order'.$id);
        parent::_initForm();
    }

    public function setActionUrl($url)
    {
        $this->_actionUrl = $url;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['action'] = '';
        return $ret;
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $id = $this->getData()->row->id;
        $addToCart = $this->getData()->parent->getComponent()->getItemDirectory()
            ->getChildComponent('_'.$id)
            ->getChildComponent('-addToCart');
        $addToCart->getComponent()->_beforeInsert($row);
    }
}
