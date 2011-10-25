<?php
abstract class Kwc_Menu_Abstract_Trl_Component extends Kwc_Chained_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['parentPage'] = self::getChainedByMaster($ret['parentPage'], $this->getData());
        return $ret;
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        foreach (Kwc_Abstract::getComponentClasses() as $class) {
            foreach (Kwc_Abstract::getSetting($class, 'generators') as $key => $generator) {
                if (!isset($generator['showInMenu']) || !$generator['showInMenu']) continue;
                $generator = current(Kwf_Component_Generator_Abstract::getInstances(
                    $class, array('generator' => $key))
                );
                if (!$generator->getGeneratorFlag('page') || !$generator->getGeneratorFlag('table')) continue;
                $ret[] = new Kwf_Component_Cache_Meta_Static_Model($generator->getModel());
            }
        }

        $ret[] = new Kwf_Component_Cache_Meta_Static_Model('Kwf_Component_Model');

        return $ret;
    }
}
