<?php
class Vpc_Guestbook_Admin extends Vpc_Directories_Item_Directory_Admin
{
    protected function _getContentClass()
    {
        $detail = Vpc_Abstract::getChildComponentClass($this->_class, 'detail');
        return Vpc_Abstract::getChildComponentClass($detail, 'child', 'content');
    }

    public function getExtConfig()
    {
        $ret = parent::getExtConfig();

        $url = Vpc_Admin::getInstance($this->_class)->getControllerUrl('Settings');
        $icon = new Vps_Asset('wrench_orange');
        $arr = array('settings' => array(
            'xtype' => 'vps.autoform',
            'controllerUrl' => $url,
            'title' => trlVps('Settings'),
            'icon' => $icon->__toString()
        ));
        return array_merge($arr, $ret);

        /*
        $contentClass = $this->_getContentClass();

        $componentConfigs = array();
        $contentEditComponents = array();
        $cfg = Vpc_Admin::getInstance($contentClass)->getExtConfig();
        foreach ($cfg as $k=>$c) {
            $componentConfigs[$contentClass.'-'.$k] = $c;
            $contentEditComponents[] = array(
                'componentClass' => $contentClass,
                'type' => $k
            );
        }
        $cfgKeys = array_keys($cfg);

        $componentPlugins = array();
        foreach ($this->_getPluginAdmins() as $a) {
            $componentPlugins[] = $a->getPluginExtConfig();
        }

        
        $url = Vpc_Admin::getInstance($this->_getParam('class'))->getControllerUrl('Form');
        $this->_editDialog['controllerUrl'] = $url;

        return array(
            'items' => array(
                'xtype'=>'vpc.directories.item.directory',
                'controllerUrl' => $this->getControllerUrl(),
                'title' => trlVps('Edit {0}', $this->_getSetting('componentName')),
                'icon' => $this->_getSetting('componentIcon')->__toString(),
                'contentClass' => $contentClass,
                'contentType' => $cfgKeys[0],
                'componentConfigs' => $componentConfigs,
                'contentEditComponents' => $contentEditComponents,
                'componentPlugins' => $componentPlugins
            )
        );*/
    }
}
