<?php
class Vpc_Basic_Download_Component extends Vpc_Abstract_Composite_Component
{
    protected $_fileRow;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Basic_Download_Model';
        $ret['componentName'] = trlVps('Download');
        $ret['componentIcon'] = new Vps_Asset('folder_link');
        $ret['showFilesize'] = true;
        $ret['showIcon'] = true;
        $ret['cssClass'] = 'webStandard';
        $ret['flags']['searchContent'] = true;
        $ret['flags']['hasFulltext'] = true;
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

        if ($this->_getSetting('showIcon')) {
            $return['icon'] = $this->getIcon();
        } else {
            $return['icon'] = null;
        }
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

    public function modifyFulltextDocument(Zend_Search_Lucene_Document $doc)
    {
        $fieldName = $this->getData()->componentId;

        $doc->getField('content')->value .= ' '.$this->_getRow()->infotext;

        $field = Zend_Search_Lucene_Field::UnStored($fieldName, $this->_getRow()->infotext, 'utf-8');
        $doc->addField($field);
    }
}
