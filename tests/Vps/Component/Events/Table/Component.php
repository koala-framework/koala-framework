<?php
class Vps_Component_Events_Table_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Vps_Component_Generator_Table',
            'component' => 'Vpc_Basic_Empty_Component'
        );
        $ret['childModel'] = 'Vps_Component_Events_Table_Model';
        return $ret;
    }
}
?>