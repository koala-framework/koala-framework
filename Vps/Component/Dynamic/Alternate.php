<?php
/**
 * gibt 1 oder 2 zurück
 */
class Vps_Component_Dynamic_Alternate extends Vps_Component_Dynamic_Abstract
{
    public function getContent()
    {
        return ($this->_info['partial']['number'] % 2) + 1;
    }
}
