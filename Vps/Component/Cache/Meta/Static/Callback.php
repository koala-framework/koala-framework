<?php
class Vps_Component_Cache_Meta_Static_Callback extends Vps_Component_Cache_Meta_Static_Model
{
    public function __construct($model, $pattern = null)
    {
        if (!$pattern)
            throw new Vps_Exception('Callback must have pattern');
        if (strpos($pattern, '%') !== false)
            throw new Vps_Exception('Callback must not use wildcard in pattern');
        parent::__construct($model, $pattern);
    }

    public function isCallback()
    {
        return true;
    }
}