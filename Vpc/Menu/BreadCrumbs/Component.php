<?php
class Vpc_Menu_BreadCrumbs_Component extends Vpc_Menu_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['separator'] = '»';
        $ret['showHome'] = false;
        $ret['showCurrentPage'] = true;
        return $ret;
    }

    public static function useAlternativeComponent($componentClass, $parentData, $generator)
    {
        return false;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['separator'] = $this->_getSetting('separator');
        $ret['links'] = array();
        $page = $this->getData();
        do {
            $ret['links'][] = $page;
        } while ($page = $page->getParentPage());
        $page = $this->getData()->getPage();
        if ($this->_getSetting('showHome') && $page) {
            if (!isset($page->isHome) || !$page->isHome) {
                $home = Vps_Component_Data_Root::getInstance()->getRecursiveChildComponents(array(
                    'home' => true,
                    'subRoot' => $this->getData()
                ), array());
                if ($home) {
                    $ret['links'][] = $home[0];
                }
            }
        }
        $ret['links'] = array_reverse($ret['links']);
        if (count($ret['links']) && !$this->_getSetting('showCurrentPage')) {
            array_pop($ret['links']);
        }
        return $ret;
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        foreach (Vpc_Abstract::getComponentClasses() as $componentClass) {
            foreach (Vpc_Abstract::getSetting($componentClass, 'generators') as $key => $generator) {
                if (!is_instance_of($generator['class'], 'Vps_Component_Generator_PseudoPage_Table') &&
                    !is_instance_of($generator['class'], 'Vpc_Root_Category_Generator')
                ) continue;
                $generator = current(Vps_Component_Generator_Abstract::getInstances(
                    $componentClass, array('generator' => $key))
                );
                $ret[] = new Vps_Component_Cache_Meta_Static_Model($generator->getModel());
            }
        }
        $ret[] = new Vps_Component_Cache_Meta_Static_Model('Vps_Component_Model', '{componentId}');
        $ret[] = new Vps_Component_Cache_Meta_Static_Model('Vpc_Root_Category_GeneratorModel', '{id}');
        return $ret;
    }
}
