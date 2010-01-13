<?php
class Vpc_Advanced_YouTube_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlVps('YouTube'),
            'componentIcon' => new Vps_Asset('film'),
            'ownModel'     => 'Vps_Component_FieldModel'
        ));
        return $ret;
    }
}
