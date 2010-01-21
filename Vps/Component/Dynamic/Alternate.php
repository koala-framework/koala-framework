<?php
/**
 * gibt 1 oder 2 zurück
 */
class Vps_Component_Dynamic_Alternate extends Vps_Component_Dynamic_Abstract
{
    protected $_modulo = 2;
    public function setArguments($modulo = null)
    {
        if ($modulo) $this->_modulo = $modulo;
    }

    public function getContent()
    {
        return ($this->_info['partial']['number'] % $this->_modulo) + 1;
    }
}
