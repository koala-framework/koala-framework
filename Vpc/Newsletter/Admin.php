<?php
class Vpc_Newsletter_Admin extends Vpc_Directories_Item_Directory_Admin
{
    public function addResources(Vps_Acl $acl)
    {
        parent::addResources($acl);
        $this->_addResourcesBySameClass($acl);
    }

    public function getExtConfig()
    {
        $ret = array();
        $detail = Vpc_Abstract::getChildComponentClass($this->_class, 'detail');

        $ret['items'] = array(
            'xtype'=>'vpc.directories.item.directory',
            'contentClass' => $detail,
            'componentConfigs' => array(),
            'controllerUrl' => $this->getControllerUrl(),
            'title' => trlVps('Edit {0}', $this->_getSetting('componentName')),
            'icon' => $this->_getSetting('componentIcon')->__toString()
        );

        $detail = Vpc_Abstract::getChildComponentClass($this->_class, 'detail');
        $cfg = Vpc_Admin::getInstance($detail)->getExtConfig();
        foreach ($cfg as $k=>$c) {
            $ret['items']['componentConfigs'][$detail.'-'.$k] = $c;
            $ret['items']['contentEditComponents'][] = array(
                'componentClass' => $detail,
                'type' => $k
            );
        }
        $cfgKeys = array_keys($cfg);
        $ret['items']['contentType'] = $cfgKeys[0];
        return $ret;
    }
}
