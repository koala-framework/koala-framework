<?php
class Kwc_Shop_Cart_Detail_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Kwc_Shop_Cart_Detail_Form_Component';
        $ret['viewCache'] = false;
        $ret['flags']['processInput'] = true;
        $ret['placeholder']['product'] = trlKwfStatic('Product').': ';
        $ret['placeholder']['unitPrice'] = '';
        return $ret;
    }

    public function preProcessInput($data)
    {
        if (isset($data[$this->getData()->componentId.'-delete'])) {
            $this->getData()->row->delete();
        }
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $addCmp = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->getData()->row->add_component_id, array('subroot'=>$this->getData()));
        $ret['product'] = $addCmp->parent;
        $ret['row'] = $this->getData()->row;
        $ret['price'] = $addCmp->getComponent()->getPrice($ret['row']);
        $ret['text'] = $addCmp->getComponent()->getProductText($ret['row']);
        return $ret;
    }

    public function getAddToCartForm()
    {
        return Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->getData()->row->add_component_id, array('subroot'=>$this->getData()));

    }

    public function getOrderProductRow()
    {
        return $this->getData()->row;
    }
}
