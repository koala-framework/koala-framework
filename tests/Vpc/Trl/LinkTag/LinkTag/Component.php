<?php
class Vpc_Trl_LinkTag_LinkTag_Component extends Vpc_Basic_LinkTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Trl_LinkTag_LinkTag_TestModel';
        $ret['generators']['child']['component'] = array(
            'empty' => 'Vpc_Basic_Empty_Component',
            'extern' => 'Vpc_Trl_LinkTag_LinkTag_Extern_Component'
        );
        return $ret;
    }
}
