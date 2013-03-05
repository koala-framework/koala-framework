<?php
class Kwc_Shop_Cart_Trl_Component extends Kwc_Directories_Item_Directory_Trl_Component
{
    public function getOrderProductsModel()
    {
        return $this->getData()->chained->getComponent()->getChildModel();
    }

    public function getForms()
    {
        $ret = array();
        foreach ($this->getData()->getChildComponents(array('generator'=>'detail')) as $c) {
            $ret[] = $c->getChildComponent('-form')
                ->getChildComponent('-child')
                ->getComponent()->getForm();
        }
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['checkout'] = $this->getData()->getChildComponent('_checkout');
        $ret['shop'] = $this->getData()->getParentPage();
        return $ret;
    }
}
