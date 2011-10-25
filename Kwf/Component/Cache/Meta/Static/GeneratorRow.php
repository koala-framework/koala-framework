<?php
class Kwf_Component_Cache_Meta_Static_GeneratorRow extends Kwf_Component_Cache_Meta_Abstract
{
    public function getCacheMeta($componentClass)
    {
        $ret = array();
        foreach (Kwc_Abstract::getComponentClasses() as $class) {
            foreach (Kwc_Abstract::getSetting($class, 'generators') as $key => $gData) {
                if ($gData['component'] == $componentClass) {
                    $generator = Kwf_Component_Generator_Abstract::getInstance($class, $key);
                    if (isset($gData['dbIdShortcut'])) {
                        $pattern = $gData['dbIdShortcut'];
                    } else {
                        $pattern = '%' . $generator->getIdSeparator();
                    }
                    $pattern .= '{id}';
                    $ret[] = new Kwf_Component_Cache_Meta_Static_Model(
                        $generator->getModel(), $pattern
                    );
                }
            }
        }
        return $ret;
    }
}