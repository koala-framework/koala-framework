<?php
class Kwc_Basic_Download_Trl_Component extends Kwc_Abstract_Composite_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        return $ret;
    }
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $return = parent::getTemplateVars($renderer);

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
}
