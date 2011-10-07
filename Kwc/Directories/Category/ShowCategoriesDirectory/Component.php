<?php
class Vpc_Directories_Category_ShowCategoriesDirectory_Component extends Vpc_Directories_Category_ShowCategories_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail'] = array(
            'class' => 'Vps_Component_Generator_Table',
            'component' => 'Vpc_Directories_Item_Detail_Component'
        );
        $ret['useDirectorySelect'] = false;
        return $ret;
    }
    protected function _getItemDirectory()
    {
        return $this->getData();
    }
}
