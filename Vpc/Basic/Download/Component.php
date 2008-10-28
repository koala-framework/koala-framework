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
        $return['infotext'] = $this->_getRow()->infotext;

        $fileRow = $this->_getFileRow();
        if (!$this->_getSetting('showFilesize')) {
            $return['filesize'] = null;
        } else {
            $return['filesize'] = $fileRow->getFilesize();
        }

        $icon = $this->getIcon();
        $return['iconname'] = $icon;
        if ($icon) {
            $icon = '/assets/silkicons/' . $icon . '.png';
        }
        $return['icon'] = $icon;
        return $return;
    }

    private function _getFileRow()
    {
        if (!$this->_fileRow) {
            $this->_fileRow = $this->getData()->getChildComponent('-downloadTag')->
                getComponent()->getFileRow();
        }
        return $this->_fileRow;
    }
    
    public function getIcon()
    {
        $extension = $this->_getFileRow()->getFileExtension();
        switch ($extension) {
            case 'pdf':
                return 'page_white_acrobat';
            case 'doc':
            case 'docx':
                return 'page_white_word';
            case 'xls':
            case 'xlsx':
                return 'page_white_excel';
            case 'ppt':
            case 'pptx':
                return 'page_white_powerpoint';
            case 'zip':
            case 'rar':
                return 'page_white_compressed';
            case 'exe':
                return 'page_white_gear';
            case 'jpg':
            case 'gif':
            case 'png':
            case 'psd':
                return 'page_white_picture';
            default:
                return 'page_white_get';
        }
    }

    public function getSearchContent()
    {
        return $this->_getRow()->infotext;
    }
}
