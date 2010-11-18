<?php
class Vps_Component_Generator_RecursiveTable2_Flagged extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['testFlag'] = true;
        return $ret;
    }

}
