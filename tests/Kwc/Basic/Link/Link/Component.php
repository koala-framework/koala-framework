<?php
class Kwc_Basic_Link_Link_Component extends Kwc_Basic_Link_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component'] = array(
            'linkTag' => 'Kwc_Basic_Link_Link_LinkTag_Component',
        );
        $ret['ownModel'] = 'Kwc_Basic_Link_Link_Model';
        return $ret;
    }
}
