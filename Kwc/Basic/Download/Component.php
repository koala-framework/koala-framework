<?php
class Kwc_Basic_Download_Component extends Kwc_Abstract_Composite_Component
{
    private $_fileRow;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_Download_Model';
        $ret['componentName'] = trlKwfStatic('Download');
        $ret['componentCategory'] = 'special';
        $ret['componentIcon'] = 'folder_link';
        $ret['showFilesize'] = true;
        $ret['showIcon'] = true;
        $ret['cssClass'] = 'webStandard';
        $ret['flags']['searchContent'] = true;
        $ret['flags']['hasFulltext'] = true;
        $ret['generators']['child']['component']['downloadTag'] = 'Kwc_Basic_DownloadTag_Component';
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

        if ($this->_getSetting('showIcon')) {
            $return['icon'] = $this->getIcon();
        } else {
            $return['icon'] = null;
        }
        if ($parentRow) {
            $return['extension'] = $parentRow->extension;
        } else {
            $return['extension'] = null;
        }
        return $return;
    }

    protected function _getFileRow()
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
        return Kwf_Util_FileIcon::getFileIcon($fileRow->extension);
    }

    public function getSearchContent()
    {
        return $this->_getRow()->infotext;
    }

    public function getFulltextContent()
    {
        $ret = array();
        $text = $this->_getRow()->infotext;
        $ret['content'] = $text;
        $ret['normalContent'] = $text;
        return $ret;
    }
}
