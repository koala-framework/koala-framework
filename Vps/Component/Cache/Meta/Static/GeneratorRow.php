<?php
class Vps_Component_Cache_Meta_Static_GeneratorRow extends Vps_Component_Cache_Meta_Static_Abstract
{
    public function getModelnames($componentClass)
    {
        $ret = array();
        foreach (Vpc_Abstract::getComponentClasses() as $class) {
            foreach (Vpc_Abstract::getSetting($class, 'generators') as $key => $gData) {
                if ($gData['component'] == $componentClass) {
                    $generator = Vps_Component_Generator_Abstract::getInstance($class, $key);
                    $ret[] = $this->_getModelname($generator->getModel());
                }
            }
        }
        return $ret;
    }
}