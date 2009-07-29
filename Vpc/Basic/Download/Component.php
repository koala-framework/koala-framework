<?php
class Vpc_Basic_Download_Component extends Vpc_Abstract_Composite_Component
{
    protected $_fileRow;

    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'modelname' => 'Vpc_Basic_Download_Model',
            'componentName' => trlVps('Download'),
            'componentIcon' => new Vps_Asset('folder_link'),
            'showFilesize' => true,
            'cssClass' => 'webStandard',
        ));
        $ret['flags']['searchContent'] = true;
        $ret['generators']['child']['component']['downloadTag'] = 'Vpc_Basic_DownloadTag_Component';
        return $ret;
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();

        $fileRow = $this->_getFileRow();
        $parentRow = $fileRow->getParentRow('File');
        if ($this->_getSetting('showFilesize') && $parentRow) {
            $return['filesize'] = $parentRow->getFileSize();
        } else {
            $return['filesize'] = null;
        }
        $return['infotext'] = $this->_getRow()->infotext;
        if ($return['infotext'] == '' && $parentRow)
            $return['infotext'] = $parentRow->filename;

        $return['icon'] = $this->getIcon();
        return $return;
    }

    private function _getFileRow()
    {
        if (!$this->_fileRow) {
            $this->_fileRow = $this->getData()
                ->getChildComponent('-downloadTag')->
                getComponent()->getFileRow();
        }
        return $this->_fileRow;
    }

    public function getIcon()
    {
        $fileRow = $this->_getFileRow()->getParentRow('File');
        if (!$fileRow) return 'page_white_get';
        return Vps_Util_FileIcon::getFileIcon($fileRow->extension);
    }

    public function getSearchContent()
    {
        return $this->_getRow()->infotext;
    }
}
