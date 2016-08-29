<?php
class Kwc_Shop_Products_View_Component extends Kwc_Shop_Products_ViewWithoutAddToCart_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['flags']['processInput'] = true;
        return $ret;
    }

    public function getPartialVars($partial, $nr, $info)
    {
        $ret = parent::getPartialVars($partial, $nr, $info);
        $ret['item']->addToCart = null;
        $ret['item']->addToCart = $this->getData()->parent->getComponent()
            ->getItemDirectory()->getChildComponent('-'.$ret['item']->id);
        return $ret;
    }

    public function processInput(array $postData)
    {
        parent::processInput($postData);
        foreach ($this->getItems() as $i) {
            $addToCart = $this->getData()->parent->getComponent()
                ->getItemDirectory()->getChildComponent('-'.$i->id);
            $addToCart->getComponent()->processInput($postData);
        }
    }
}
