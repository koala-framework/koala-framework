<?php
abstract class Vpc_Abstract_List_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => 'List',
            'tablename'     => 'Vpc_Abstract_List_Model',
            'childComponentClasses' => array(),
            'showVisible' => true,
            'default' => array(
                'visible' => 1
            )
        ));
        $ret['assetsAdmin']['dep'][] = 'VpsProxyPanel';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Abstract/List/Panel.js';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['children'] = $this->getData()->getChildComponents(array('treecache' => 'Vpc_Abstract_List_TreeCache'));
        return $ret;
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

    public function getStatisticVars()
    {
        $ret = parent::getStatisticVars();
        foreach ($this->getChildComponents() as $c) {
            $ret = array_merge($ret, $c->getStatisticVars());
        }
        return $ret;
    }
}
