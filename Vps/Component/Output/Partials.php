<?php
class Vps_Component_Output_Partials
{
    public function render($component, $config)
    {
        $componentId = $component->componentId;
        $partialsClass = $config[0];
        $config = $config[1];
        $partial = new $partialsClass(unserialize(base64_decode(($config))));
        $ids = $partial->getIds();
        $ret = '';
        $number = 0; $count = count($ids);
        foreach ($ids as $id) {
            $info = base64_encode(serialize(array(
                'total' => $count,
                'number' => $number++
            )));
            $ret .= "{partial: $componentId $partialsClass $config $id $info}";
        }
        return $ret;
    }

    public static function getHelperOutput(Vps_Component_Data $component, $partialClass = null, $params = array())
    {
        if (!$component instanceof Vps_Component_Data ||
            !method_exists($component->getComponent(), 'getPartialVars')
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
        if (method_exists($component->getComponent(), 'getPartialParams')) {
            $params = array_merge($component->getComponent()->getPartialParams(), $params);
        }
        $serializedParams = base64_encode(serialize($params));
        return "{partials: $componentId $partialClass $serializedParams}";
    }
}
