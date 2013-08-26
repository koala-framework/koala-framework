<?php
class Kwc_Basic_Image_Crop_Root extends Kwc_Root_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_Image_Crop_ImageComponent'
        );
        $ret['generators']['page1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_Image_Crop_ImageFixDimensionComponent'
        );
        $ret['generators']['page2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_Image_Crop_ImageUserSelectComponent'
        );
        $ret['generators']['page10'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_Image_Crop_ParentImage_Component'
        );

        $ret['generators']['page3'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_Image_Crop_MultipleDimensionsComponent'
        );
        $ret['generators']['page4'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_Image_Crop_MultipleDimensionsComponent'
        );
        $ret['generators']['page5'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_Image_Crop_MultipleDimensionsComponent'
        );
        $ret['generators']['page6'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_Image_Crop_MultipleDimensionsComponent'
        );
        $ret['generators']['page7'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_Image_Crop_MultipleDimensionsComponent'
        );
        $ret['generators']['page8'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_Image_Crop_MultipleDimensionsComponent'
        );
        $ret['generators']['page9'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_Image_Crop_MultipleDimensionsComponent'
        );
        return $ret;
    }
}
