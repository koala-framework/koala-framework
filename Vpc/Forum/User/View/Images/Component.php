<?php
class Vpc_Forum_User_View_Images_Component extends Vpc_Composite_ImagesEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['child']
                    = 'Vpc_Forum_User_View_Images_Image_Component';
        $ret['childComponentClasses']['edit']
                    = 'Vpc_Forum_User_View_Images_Edit_Component';
        $ret['loginDecorator'] = 'Vpc_Decorator_CheckLogin_Component';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['parentTemplate'] = Vpc_Admin::getComponentFile(get_parent_class($this), '', 'tpl');
        return $ret;
    }

}
