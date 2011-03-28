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

        $ret['extConfig'] = 'Vpc_Abstract_Composite_ExtConfigForm';

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

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        $generators = Vpc_Abstract::getSetting($componentClass, 'generators');
        if (isset($generators['child'])) {
            $components = $generators['child']['component'];
            if (!is_array($components)) $components = array($components);
            foreach ($components as $class) {
                if ($class) {
                    $ret[] = new Vpc_Abstract_Composite_MetaHasContent($class);
                }
            }
        }
        return $ret;
    }
}
