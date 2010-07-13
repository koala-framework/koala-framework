<?php
class Vpc_NewsletterCategory_Admin extends Vpc_Newsletter_Admin
    implements Vpc_Directories_Item_Directory_PluginAdminInterface
{
    public function getPluginExtConfig()
    {
        $ret = array();
        $ret['pluginClass'] = 'Vpc.NewsletterCategory.Plugin';
        $ret['controllerUrl'] = $this->getControllerUrl('Categories');
        return $ret;
    }

    protected function _getPluginAdmins()
    {
        return array($this);
    }
}
