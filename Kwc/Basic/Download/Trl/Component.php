<?php
class Vpc_Basic_Download_Trl_Component extends Vpc_Abstract_Composite_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        return $ret;
    }
    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();

        /*
        $fileRow = $this->_getFileRow();
        $parentRow = $fileRow->getParentRow('File');
        if ($this->_getSetting('showFilesize') && $parentRow) {
            $return['filesize'] = $parentRow->getFileSize();
        } else {
            $return['filesize'] = null;
        }
        */
        $return['infotext'] = $this->_getRow()->infotext;
        /*
        if ($return['infotext'] == '' && $parentRow)
            $return['infotext'] = $parentRow->filename;

        if ($this->_getSetting('showIcon')) {
            $return['icon'] = $this->getIcon();
        } else {
            $return['icon'] = null;
        }
        */
        return $return;
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        $ret[] = new Vpc_Chained_Abstract_ParentIdCacheMeta(Vpc_Abstract::getSetting($componentClass, 'ownModel'));
        return $ret;
    }
}
