<?php
class Kwc_Directories_Category_Directory_Admin
    extends Kwc_Directories_Item_Directory_Admin
    implements Kwc_Directories_Item_Directory_PluginAdminInterface
{
    public function getPluginExtConfig()
    {
        $ret = array();
        $ret['pluginClass'] = 'Kwc.Directories.Category.Directory.Plugin';
        $ret['controllerUrl'] = $this->getControllerUrl('Categories');
        return $ret;
    }
}
