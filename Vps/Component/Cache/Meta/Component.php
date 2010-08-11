<?php
/**
 * Wenn bei $targetComponent der Cache gelöscht wird, soll er auch hier gelöscht
 * werden
 */
class Vps_Component_Cache_Meta_Component extends Vps_Component_Cache_Meta_Abstract
{
    private $_component;

    public function __construct(Vps_Component_Data $targetComponent)
    {
        $this->_component = $component;
    }
}