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

        $return = parent::getTemplateVars();
        $return['filesize'] = $this->getFilesize();
        $return['url'] = $url;
        $return['filename'] = $filename;
        return $return;
    }
    
    public function getFilesize()
    {
        return $this->_getRow()->getFileSize();
    }
    
    public function getFileRow()
    {
        return $this->_getRow();
    }
}
