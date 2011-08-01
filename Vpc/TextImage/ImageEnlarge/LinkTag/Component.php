<?php
class Vpc_TextImage_ImageEnlarge_LinkTag_Component extends Vpc_Basic_LinkTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_TextImage_ImageEnlarge_LinkTag_Model';
        $ret['generators']['child']['component']['download'] = 'Vpc_Basic_DownloadTag_Component';
        $ret['generators']['child']['component'] = array_merge(
            array(
                'none' => 'Vpc_Basic_LinkTag_Empty_Component',
                'enlarge' => 'Vpc_TextImage_ImageEnlarge_LinkTag_EnlargeTag_Component'
            ),
            $ret['generators']['child']['component']
        );
        return $ret;
    }
}
