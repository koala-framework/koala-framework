<?php
class Kwc_Trl_LinkTag_LinkTag_Component extends Kwc_Basic_LinkTag_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwc_Trl_LinkTag_LinkTag_TestModel';
        $ret['generators']['child']['component'] = array(
            'empty' => 'Kwc_Basic_None_Component',
            'extern' => 'Kwc_Trl_LinkTag_LinkTag_Extern_Component'
        );
        return $ret;
    }
}
