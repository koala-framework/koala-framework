<?php
/**
 * Wenn bei $targetComponent der Cache gelöscht wird, soll er auch hier gelöscht
 * werden
 */
class Vps_Component_Cache_Meta_Component extends Vps_Component_Cache_Meta_Abstract
{
    private $_targetComponent;

    public function __construct(Vps_Component_Data $targetComponent)
    {
        $this->_targetComponent = $targetComponent;
    }
}