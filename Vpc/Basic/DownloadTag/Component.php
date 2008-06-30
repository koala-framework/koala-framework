<?php
class Vpc_Basic_DownloadTag_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'tablename'     => 'Vpc_Basic_DownloadTag_Model',
            'componentName' => 'Download Tag',
            'componentIcon' => new Vps_Asset('folder_link'),
            'default'   => array(
            )
        ));
        $ret['assetsAdmin']['dep'][] = 'VpsSwfUpload';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Basic/DownloadTag/Panel.js';
        return $ret;
    }

    public function getTemplateVars()
    {
        $row = $this->_getRow();
        $filename = $row->filename != '' ? $row->filename : 'unnamed';

        $url = $row->getFileUrl(null, 'default', $filename, false, Vps_Db_Table_Row_Abstract::FILE_PASSWORD_DOWNLOAD);
        $filename = $row->filename . '.' . $this->_row->getFileExtension();
        switch ($row->getFileExtension()) {
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
        $return = parent::getTemplateVars();
        $return['filesize'] = $this->getFilesize();
        $return['url'] = $url;
        $return['filename'] = $filename;
        $return['icon'] = $icon;
        return $return;
    }
    
    public function getFilesize()
    {
        return $this->_getRow()->getFileSize();
    }
}
