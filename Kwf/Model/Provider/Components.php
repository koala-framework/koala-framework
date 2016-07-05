<?php
class Kwf_Model_Provider_Components extends Kwf_Model_Provider_Abstract
{
    public function findModels()
    {
        $ret = array();
        foreach (Kwc_Abstract::getComponentClasses() as $componentClass) {
            $cls = strpos($componentClass, '.') ? substr($componentClass, 0, strpos($componentClass, '.')) : $componentClass;
            $m = call_user_func(array($cls, 'createOwnModel'), $componentClass);
            if ($m) self::_findAllInstancesProcessModel($ret, $m);

            $m = call_user_func(array($cls, 'createChildModel'), $componentClass);
            if ($m) self::_findAllInstancesProcessModel($ret, $m);

            foreach (Kwc_Abstract::getSetting($componentClass, 'generators') as $g) {
                if (isset($g['model'])) {
                    self::_findAllInstancesProcessModel($ret, $g['model']);
                }
            }
        }

        foreach (Kwf_Component_Data_Root::getInstance()->getPlugins('Kwf_Component_PluginRoot_Interface_Models') as $plugin) {
            foreach ($plugin->getModels() as $model) {
                self::_findAllInstancesProcessModel($ret, $model);
            }
        }
        return $ret;
    }
}
