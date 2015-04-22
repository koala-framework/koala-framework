<?php
class Kwc_ImageResponsive_CreatesImgElement_Components_ImageAbstract_Component extends Kwc_Abstract_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_ImageResponsive_CreatesImgElement_Components_Image_TestModel';
        $ret['dimensions'] = array(
            'default'=>array(
                'width' => 300,
                'height' => 200,
                'cover' => true
           ),
            'fullWidth'=>array(
                'text' => trlKwfStatic('full width'),
                'width' => self::CONTENT_WIDTH,
                'height' => 0,
                'cover' => true
           ),
            'original'=>array(
                'text' => trlKwfStatic('original')
             ),
            'custom'=>array(
                'text' => trlKwfStatic('user-defined'),
                'width' => self::USER_SELECT,
                'height' => self::USER_SELECT,
                'cover' => true
           )
        );
        return $ret;
    }
}
