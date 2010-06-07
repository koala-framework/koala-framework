<?php
class Vpc_Directories_Item_Directory_Trl_Admin extends Vpc_Admin
{
    protected function _getContentClass()
    {
        return null;
    }

    public function getExtConfig()
    {
        $componentConfigs = array();
        $contentEditComponents = array();

        $contentClass = $this->_getContentClass();
        $cfgKeys = array();
        if ($contentClass) {
            $cfg = Vpc_Admin::getInstance($contentClass)->getExtConfig();
            foreach ($cfg as $k=>$c) {
                $componentConfigs[$contentClass.'-'.$k] = $c;
                $contentEditComponents[] = array(
                    'componentClass' => $contentClass,
                    'type' => $k
                );
            }
            $cfgKeys = array_keys($cfg);
        }

        $ret = array(
            'items' => array(
                'xtype'=>'vpc.directories.item.directory',
                'controllerUrl' => $this->getControllerUrl(),
                'title' => trlVps('Edit {0}', $this->_getSetting('componentName')),
                'icon' => $this->_getSetting('componentIcon')->__toString(),
                'contentClass' => $contentClass,
                'contentType' => $cfgKeys ? $cfgKeys[0] : null,
                'componentConfigs' => $componentConfigs,
                'contentEditComponents' => $contentEditComponents,
                'componentPlugins' => $componentPlugins
            )
        );
        return $ret;
    }
}
