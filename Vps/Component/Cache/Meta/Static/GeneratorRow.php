<?php
class Vps_Component_Cache_Meta_Static_GeneratorRow extends Vps_Component_Cache_Meta_Abstract
{
    public function getCacheMeta($componentClass)
    {
        $ret = array();
        foreach (Vpc_Abstract::getComponentClasses() as $class) {
            foreach (Vpc_Abstract::getSetting($class, 'generators') as $key => $gData) {
                if ($gData['component'] == $componentClass) {
                    $generator = Vps_Component_Generator_Abstract::getInstance($class, $key);
                    if (isset($gData['dbIdShortcut'])) {
                        $pattern = $gData['dbIdShortcut'];
                    } else {
                        $pattern = '%' . $generator->getIdSeparator();
                    }
                    $pattern .= '{id}';
                    $ret[] = new Vps_Component_Cache_Meta_Static_Model(
                        $generator->getModel(), $pattern
                    );
                }
            }
        }
        return $ret;
    }
}