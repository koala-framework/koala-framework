<?php
class Kwc_Directories_CategorySimple_Admin extends Kwc_Admin
    implements Kwc_Directories_Item_Directory_PluginAdminInterface
{
    public function getPluginExtConfig()
    {
        $ret = array();
        $ret['pluginClass'] = 'Kwc.Directories.CategorySimple.Plugin';
        $ret['controllerUrl'] = $this->getControllerUrl('Categories');
        return $ret;
    }
}
