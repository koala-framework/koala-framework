<?php
abstract class Vpc_Abstract_List_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => 'List',
            'tablename'     => 'Vpc_Abstract_List_Model',
            'showVisible' => true,
            'default' => array(
                'visible' => 1
            )
        ));
        $ret['generators']['child'] = array(
            'class' => 'Vps_Component_Generator_Table',
            'component' => null
        );
        $ret['assetsAdmin']['dep'][] = 'VpsProxyPanel';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Abstract/List/Panel.js';
        return $ret;
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

    public function getSearchVars()
    {
        $ret = parent::getSearchVars();
        foreach ($this->getData()->getChildComponents() as $c) {
            foreach ($c->getSearchVars() as $k=>$i) {
                if (!isset($ret[$k])) $ret[$k] = '';
                $ret[$k] .= ' '.$i;
            }
        }
        return $ret;
    }
}
