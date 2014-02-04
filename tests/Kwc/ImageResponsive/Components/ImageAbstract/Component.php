<?php
class Kwc_ImageResponsive_Components_ImageAbstract_Component extends Kwc_Abstract_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_ImageResponsive_Components_Image_TestModel';
        $ret['dimensions'] = array(
            'default'=>array(
                'width' => 300,
                'height' => 200,
                'cover' => true
           ),
            'fullWidth'=>array(
                'text' => trlKwf('full width'),
                'width' => self::CONTENT_WIDTH,
                'height' => 0,
                'cover' => true
           ),
            'original'=>array(
                'text' => trlKwf('original')
             ),
            'custom'=>array(
                'text' => trlKwf('user-defined'),
                'width' => self::USER_SELECT,
                'height' => self::USER_SELECT,
                'cover' => true
           )
        );
        return $ret;
    }
}
