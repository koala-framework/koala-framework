<?php
class Kwf_Component_Generator_RecursiveTable2_Flagged extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['testFlag'] = true;
        return $ret;
    }

}
