<?php
class Vpc_Basic_Download_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'tablename' => 'Vpc_Basic_Download_Model',
            'componentName' => trlVps('Download'),
            'componentIcon' => new Vps_Asset('folder_link'),
            'showFilesize' => true,
            'cssClass' => 'webStandard',
            'default'   => array(
            )
        ));
        $ret['flags']['searchContent'] = true;
        $ret['generators']['child']['component']['downloadTag'] = 'Vpc_Basic_DownloadTag_Component';
        return $ret;
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['infotext'] = $this->_getRow()->infotext;
        
        $fileRow = $this->getData()->getChildComponent('-downloadTag')->
            getComponent()->getFileRow();
        if (!$this->_getSetting('showFilesize')) {
            $return['filesize'] = null;
        } else {
            $return['filesize'] = $fileRow->getFilesize();
        }

        $extension = $fileRow->getFileExtension();
           
        $icon = false;
        switch ($extension) {
            case 'pdf':
                $icon = 'page_white_acrobat';
                break;
            case 'doc':
            case 'docx':
                $icon = 'page_white_word';
                break;
            case 'xls':
            case 'xlsx':
                $icon = 'page_white_excel';
                break;
            case 'ppt':
            case 'pptx':
                $icon = 'page_white_powerpoint';
                break;
            case 'zip':
            case 'rar':
                $icon = 'page_white_compressed';
                break;
            case 'exe':
                $icon = 'page_white_gear';
                break;
            case 'jpg':
            case 'gif':
            case 'png':
            case 'psd':
                $icon = 'page_white_picture';
                break;
            default:
                $icon = 'page_white_get';
                break;
        }
        if ($icon) {
            $icon = '/assets/silkicons/' . $icon . '.png';
        }        
        $return['icon'] = $icon;
        return $return;
    }

    public function getSearchContent()
    {
        return $this->_getRow()->infotext;
    }
}
