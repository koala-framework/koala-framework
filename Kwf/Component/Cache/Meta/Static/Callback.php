<?php
class Kwf_Component_Cache_Meta_Static_Callback extends Kwf_Component_Cache_Meta_Static_Model
{
    public static function getMetaType()
    {
        return self::META_TYPE_CALLBACK;
    }

    public function __construct($model, $pattern = '{component_id}')
    {
        if ($pattern && strpos($this->getPattern(), '%') !== false)
            throw new Kwf_Exception('Callback must not use wildcard in pattern');
        parent::__construct($model, $pattern);
    }
}