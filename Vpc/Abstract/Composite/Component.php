<?php
class Vpc_Abstract_Composite_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assetsAdmin']['dep'][] = 'VpsTabPanel';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $components = array();
        foreach ($this->getData()->getChildComponents(array('treecache' => 'Vpc_Abstract_Composite_TreeCache')) as $c) {
            $components[$c->id] = $c;
        }
        foreach ($this->_getSetting('generators') as $id=>$c) {
            //TODO :D
            if (isset($components[$id])) {
                $ret[$id] = $components[$id];
            }
        }
        return $ret;
    }
}
