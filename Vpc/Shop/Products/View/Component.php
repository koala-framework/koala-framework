<?php
class Vpc_Shop_Products_View_Component extends Vpc_Shop_Products_ViewWithoutAddToCart_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['processInput'] = true;
        return $ret;
    }
    public function getPartialVars($partial, $nr, $info)
    {
        $ret = parent::getPartialVars($partial, $nr, $info);
        $ret['item']->addToCart = $this->getData()->parent->getComponent()
            ->getItemDirectory()->getChildComponent('-'.$ret['item']->row->id);
        $ret['item']->addToCart->getComponent()->setActionUrl($this->getData()->url);
        return $ret;
    }

    public function processInput(array $postData)
    {
        parent::processInput($postData);
        foreach ($this->getItems() as $i) {
            $addToCart = $this->getData()->parent->getComponent()
                ->getItemDirectory()->getChildComponent('-'.$i->row->id);
            $addToCart->getComponent()->processInput($postData);
        }
    }
    public function getPartialCacheVars($nr)
    {
        $ret = parent::getPartialCacheVars($nr);
        // Wenn sich bei einer View ändert, was angezeigt wird
        $ret[] = array(
            'model' => 'Vps_Component_FieldModel',
            'field' => 'component_id',
            'value' => $this->getData()->parent->componentId
        );
        return $ret;
    }
}
