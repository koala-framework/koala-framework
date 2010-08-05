<?php
class Vps_Component_Output_Partials extends Vps_Component_Output_Abstract
{
    public function render($component, $config)
    {
        if ($component instanceof Vps_Component_Data) {
            $componentId = $component->componentId;
        } else {
            $componentId = $component;
        }
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
            $ret .= "{partial: $componentId($id) $partialsClass $config $id $info}";
        }
        return $ret;
    }
}
