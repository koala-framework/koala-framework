<?php
class Vpc_Shop_Products_View_Component extends Vpc_Directories_List_ViewPage_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        foreach ($ret['items'] as $i) {
            $i->addToCart = $this->getData()->parent->getChildComponent('-'.$i->row->id);
        }
        return $ret;
    }

}
