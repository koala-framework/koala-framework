<?php
class Kwc_Trl_ImageEnlarge_ImageEnlarge_EnlargeTag_Component extends Kwc_Basic_ImageEnlarge_EnlargeTag_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwc_Trl_ImageEnlarge_ImageEnlarge_EnlargeTag_TestModel';
        $ret['dimensions'] = array(array(
            'width'=>null, 'height'=>null
        ));
        $ret['imageTitle'] = false;
        return $ret;
    }
}
