<?php
class Kwc_Directories_Item_Directory_ExtConfigTabs extends Kwc_Directories_Item_Directory_ExtConfigAbstract
{
    protected function _getConfig()
    {
        $ret = parent::_getConfig();
        $ret['items']['needsComponentPanel'] = false;
        $ret['items']['xtype'] = 'kwc.directories.item.directory.tabs';
        $ret['items']['width'] = '500';
        $ret['items']['hasMultipleDetailComponents'] = false;
        $model = Kwf_Model_Abstract::getInstance($this->_getSetting('childModel'));
        if ($model->hasColumn('component')) {
            $ret['items']['hasMultipleDetailComponents'] = true;
        }
        $ret['items']['details'] = array(
            'xtype' => 'kwc.directories.item.directory.form',
            'controllerUrl' => $this->getControllerUrl('Form')
        );
        return $ret;
    }
}
