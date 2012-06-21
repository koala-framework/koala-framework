<?php
class Kwc_Basic_TextMailTxt_Mail_Component extends Kwc_Mail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['content']['component'] = 'Kwc_Basic_TextMailTxt_Mail_Text_Component';
        $ret['ownModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'component_id',
        ));
        return $ret;
    }
}
