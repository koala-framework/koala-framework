<?php
class Kwf_Component_Cache_Chained_StartMaster_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Cache_Chained_Master_Component',
        );
        $ret['flags']['chainedType'] = 'Trl';
        return $ret;
    }
}
