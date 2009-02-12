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
}
