<?php
class Vps_Component_Cache_Meta_Static_Callback extends Vps_Component_Cache_Meta_Static_Model
{
    public static function getType()
    {
        return self::TYPE_CALLBACK;
    }

    public function __construct($model, $pattern = '{component_id}')
    {
        if ($pattern && strpos($this->getPattern(), '%') !== false)
            throw new Vps_Exception('Callback must not use wildcard in pattern');
        parent::__construct($model, $pattern);
    }
}