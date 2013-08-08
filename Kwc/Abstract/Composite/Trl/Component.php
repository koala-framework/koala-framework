<?php
class Kwc_Abstract_Composite_Trl_Component extends Kwc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['extConfig'] = Kwc_Abstract::getSetting($masterComponentClass, 'extConfig');
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        foreach ($this->getData()->getChildComponents(array('generator' => 'child')) as $c) {
            if ($ret[$c->id]) $ret[$c->id] = $c; // Bei TextImage kann zB. Bild ausgeblendet werden und soll dann in Übersetzung auch nicht angezeigt werden
        }
        return $ret;
    }

    public function hasContent()
    {
        foreach ($this->getData()->getChildComponents(array('generator' => 'child')) as $c) {
            if ($c->hasContent()) return true;
        }
        return false;
    }

    public function getExportData()
    {
        $children = $this->getData()->getChildComponents(array('generator' => 'child'));
        if (!count($children)) return array();
        $ret = array('composite' => array());
        foreach ($children as $child) {
            $ret['composite'][$child->id] = $child->getComponent()->getExportData();
        }
        return $ret;
    }
}
