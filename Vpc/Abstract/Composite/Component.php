<?php
class Vpc_Abstract_Composite_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'childComponentClasses' => array()
        ));
        $ret['assetsAdmin']['dep'][] = 'VpsTabPanel';
        return $ret;
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $componentId = $this->getData()->componentId;
        foreach ($this->_getSetting('childComponentClasses') as $id=>$c) {
            $return[$id] = $componentId . '-' . $id;
        }
        return $return;
    }
}
