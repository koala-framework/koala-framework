<?php
abstract class Vpc_Abstract_List_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => 'List',
            'modelname'     => 'Vpc_Abstract_List_Model',
            'showVisible' => true,
            'showPosition' => true
        ));
        $ret['generators']['child'] = array(
            'class' => 'Vps_Component_Generator_Table',
            'component' => null
        );
        $ret['assetsAdmin']['dep'][] = 'VpsProxyPanel';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Abstract/List/Panel.js';
        return $ret;
    }

    public static function validateSettings($settings)
    {
        if (isset($settings['default'])) {
            throw new Vps_Exception("Setting default doesn't exist anymore");
        }
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['children'] = $this->getData()->getChildComponents(array('generator' => 'child'));
        return $ret;
    }

    public function hasContent()
    {
        $childComponents = $this->getData()->getChildComponents(array('generator' => 'child'));
        foreach ($childComponents as $c) {
            if ($c->hasContent()) return true;
        }
        return false;
    }

    public function getCacheVars()
    {
        $ret = parent::getCacheVars();
        $ret[] = $this->_getCacheVars();
        return $ret;
    }

    protected function _getCacheVars()
    {
        return array(
            'model' => $this->getModel(),
            'id' => $this->getData()->dbId,
            'field' => Vps_Component_Cache::META_FIELD_COMPONENT_ID
        );
    }
}
