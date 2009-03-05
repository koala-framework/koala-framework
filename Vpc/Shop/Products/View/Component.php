<?php
class Vpc_Shop_Products_View_Component extends Vpc_Directories_List_ViewPage_Component
{
    public function getPartialVars($partial, $nr, $info)
    {
        $ret = parent::getPartialVars($partial, $nr, $info);
        $ret['item']->addToCart = $this->getData()->parent->getChildComponent('-'.$ret['item']->row->id);
        return $ret;
    }
}
