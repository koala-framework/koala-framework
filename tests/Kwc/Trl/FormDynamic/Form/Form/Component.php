<?php
class Kwc_Trl_FormDynamic_Form_Form_Component extends Kwc_FormDynamic_Basic_Form_Form_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['success'] = 'Kwc_Form_Success_Component';
        return $ret;
    }
}