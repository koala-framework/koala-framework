<?php
class Vps_Component_Generator_Subroot_CategoryCh extends Vps_Component_Generator_Subroot_Category
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['component'] = array(
            'image' => 'Vpc_Basic_Image_Component',
            'empty_ch' => 'Vpc_Basic_Empty_Component'
        );
        return $ret;
    }
}
?>