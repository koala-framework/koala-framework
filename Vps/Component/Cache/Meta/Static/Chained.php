<?php
class Vps_Component_Cache_Meta_Static_Chained extends Vps_Component_Cache_Meta_Abstract
{
    private $_sourceComponentClass;

    public function __construct($sourceComponentClass)
    {
        $this->_sourceComponentClass = $sourceComponentClass;
    }

    public function getSourceComponentClass()
    {
        return $this->_sourceComponentClass;
    }

    public static function getDeleteWheres($componentIds)
    {
        $chainedTypes = array();
        foreach (Vps_Component_Abstract::getComponentClasses() as $cc) {
            if (!Vpc_Abstract::hasSetting($cc, 'masterComponentClass')) continue;
            $chainedType = Vpc_Abstract::getFlag($cc, 'chainedType');
            if ($chainedType) $chainedTypes[$chainedType] = $cc;
        }

        $ret = array();
        foreach ($componentIds as $componentId) {

            if (strpos($componentId, '%') !== false) continue;

            // Komponente von Master bei der der Cache gelöscht wird
            $component = Vps_Component_Data_Root::getInstance()
                ->getComponentById($componentId, array('ignoreVisible' => true));
            if (!$component) continue;

            // Alle zur Mastercomponent gehörigen ChainedComponents finden:
            // Nach oben schauen, wenn chainedType gefunden, statisch die
            // dazugehörigen chained-Class holen und anhand dieser die
            // ChainedComponents finden. Danach einfach die componentId
            // vom Master mit der der Chained ersetzen
            $chainedFound = false;
            $c = $component;
            while ($c) {

                $chainedType = Vpc_Abstract::getFlag($c->componentClass, 'chainedType');
                if ($chainedType && isset($chainedTypes[$chainedType])) {
                    $chainedFound = true;
                    $chainedComponents = $c->parent->getChildComponents(array(
                        'componentClass' => $chainedTypes[$chainedType],
                        'ignoreVisible' => true
                    ));
                    foreach ($chainedComponents as $chainedComponent) {
                        $part2 = substr($componentId, strlen($c->componentId));
                        $componentId = $chainedComponent->componentId . $part2;
                        $ret[] = array(
                            'componentId' => $componentId
                        );
                    }
                }

                $c = $c->parent;
            }
            if (!$chainedFound) throw new Vps_Exception("No Flag chainedType set for {$component->componentClass} or parent");

        }
        return $ret;
    }
}