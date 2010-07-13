<?php
class Vpc_Abstract_Composite_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assetsAdmin']['dep'][] = 'VpsTabPanel';
        $ret['generators']['child'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => array()
        );
        $cc = Vps_Registry::get('config')->vpc->childComponents;
        if (isset($cc->Vpc_Abstract_Composite_Component)) {
            $ret['generators']['child']['component'] =
                $cc->Vpc_Abstract_Composite_Component->toArray();
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

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['keys'] = array();
        foreach ($this->getData()->getChildComponents(array('generator' => 'child')) as $c) {
            $ret[$c->id] = $c;
            $ret['keys'][] = $c->id;
        }
        return $ret;
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
