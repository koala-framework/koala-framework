<?php
class Vpc_Directories_Category_Directory_Trl_Admin extends Vpc_Directories_Item_Directory_Trl_Admin
    implements Vpc_Directories_Item_Directory_PluginAdminInterface
{
    public function getPluginExtConfig()
    {
        $ret = array();
        $ret['pluginClass'] = 'Vpc.Directories.Category.Directory.Plugin';
        $ret['controllerUrl'] = $this->getControllerUrl('Categories');
        return $ret;
    }
}
