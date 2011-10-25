<?php
class Kwf_Component_Generator_Subroot_CategoryCh extends Kwf_Component_Generator_Subroot_Category
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['component'] = array(
            'image' => 'Kwc_Basic_Image_Component',
            'empty_ch' => 'Kwc_Basic_Empty_Component'
        );
        return $ret;
    }
}
?>