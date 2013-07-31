<?php
class Kwc_Blog_Category_Detail_List_Component extends Kwc_Directories_Category_Detail_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['feed'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Blog_List_Feed_Component',
            'name' => trlKwfStatic('Feed')
        );
        return $ret;
    }
}
