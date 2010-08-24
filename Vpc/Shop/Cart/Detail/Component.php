<?php
class Vpc_Shop_Cart_Detail_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Vpc_Shop_Cart_Detail_Form_Component';
        $ret['viewCache'] = false;
        $ret['flags']['processInput'] = true;
        $ret['placeholder']['product'] = trlVps('Product').': ';
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
        $addCmp = Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->getData()->row->add_component_id);
        $ret['product'] = $addCmp->parent;
        $ret['row'] = $this->getData()->row;
        $ret['price'] = $addCmp->getComponent()->getPrice($ret['row']);
        $ret['text'] = $addCmp->getComponent()->getProductText($ret['row']);
        return $ret;
    }
}
