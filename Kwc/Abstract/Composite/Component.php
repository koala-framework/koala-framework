<?php
class Kwc_Abstract_Composite_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['assetsAdmin']['dep'][] = 'KwfTabPanel';
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => array()
        );
        $cc = Kwf_Registry::get('config')->kwc->childComponents;
        if (isset($cc->Kwc_Abstract_Composite_Component)) {
            $ret['generators']['child']['component'] =
                $cc->Kwc_Abstract_Composite_Component->toArray();
        }

        $ret['extConfig'] = 'Kwc_Abstract_Composite_ExtConfigForm';

        return $ret;
    }

    public function hasContent()
    {
        foreach ($this->getData()->getChildComponents(array('generator' => 'child')) as $c) {
            if ($c->hasContent()) return true;
        }
        return false;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
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
