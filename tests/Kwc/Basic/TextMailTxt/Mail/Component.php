<?php
class Kwc_Basic_TextMailTxt_Mail_Component extends Kwc_Mail_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['content']['component'] = 'Kwc_Basic_TextMailTxt_Mail_Text_Component';
        $ret['ownModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'component_id',
        ));
        return $ret;
    }
}
