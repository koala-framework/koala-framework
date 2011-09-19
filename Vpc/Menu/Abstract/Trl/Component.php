<?php
abstract class Vpc_Menu_Abstract_Trl_Component extends Vpc_Chained_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['parentPage'] = self::getChainedByMaster($ret['parentPage'], $this->getData());
        return $ret;
    }

    protected function _getChainedComponent($component)
    {
        $ret = Vpc_Chained_Trl_Component::getChainedByMaster($component, $this->getData());
        if ($ret) {
            if (isset($component->current)) $ret->current = $component->current;
            if (isset($component->class)) $ret->class = $component->class;
        }
        return $ret;
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        foreach (Vpc_Abstract::getComponentClasses() as $class) {
            foreach (Vpc_Abstract::getSetting($class, 'generators') as $key => $generator) {
                if (!isset($generator['showInMenu']) || !$generator['showInMenu']) continue;
                $generator = current(Vps_Component_Generator_Abstract::getInstances(
                    $class, array('generator' => $key))
                );
                if (!$generator->getGeneratorFlag('page') || !$generator->getGeneratorFlag('table')) continue;
                $ret[] = new Vps_Component_Cache_Meta_Static_Model($generator->getModel());
            }
        }

        $ret[] = new Vps_Component_Cache_Meta_Static_Model('Vps_Component_Model');
        $ret[] = new Vps_Component_Cache_Meta_Static_Model('Vpc_Menu_Abstract_Model');

        return $ret;
    }
}
