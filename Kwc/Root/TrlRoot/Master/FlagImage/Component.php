<?php
class Kwc_Root_TrlRoot_Master_FlagImage_Component extends Kwc_Abstract_Image_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['dimensions'] = array(
            'default'=>array(
                'text' => trlKwfStatic('default'),
                'width' => 16,
                'height' => 16,
                'cover' => false,
            ),
        );
        $ret['componentName'] = trlcKwfStatic('Flag of a Country', 'Flag');
        return $ret;
    }
}
