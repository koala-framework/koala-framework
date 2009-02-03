<?php
class Vps_View_Helper_Partials
{
    public function partials($component, $partialClass = null, $params = null)
    {
        if (!$component instanceof Vps_Component_Data ||
            !$component->getComponent() instanceof Vps_Component_Partial_Interface
        )
            throw new Vps_Exception('Component has to implement Vps_Component_Partial_Interface');
        if (!$partialClass) {
            if (!method_exists($component->getComponent(), 'getPartialClass')) {
                throw new Vps_Exception('If no partial class is given, component musst implement method "getPartialClass"');
            }
            $partialClass = $component->getComponent()->getPartialClass();
        }
        $componentId = $component->componentId;
        $componentClass = $component->componentClass;
        if (is_null($params))
            $params = $component->getComponent()->getPartialParams();
        $serializedParams = serialize($params);
        return "{partials: $componentId $componentClass $partialClass $serializedParams }";
    }
}
