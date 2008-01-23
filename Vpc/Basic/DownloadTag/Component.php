<?php
class Vpc_Basic_DownloadTag_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'tablename' => 'Vpc_Basic_DownloadTag_Model',
            'componentName' => 'Download Tag',
            'default'   => array(
            )
        ));
    }

    public function getTemplateVars()
    {
        $filename = $row->filename != '' ? $row->filename : 'unnamed';

        $url = $this->_row->getFileUrl(null, 'default', $filename); // TODO: Vps_Dao_Row_File::DOWNLOAD
        $filesize = $this->_row->getFileSize();
        $filename = $row->filename . '.' . $this->_row->getFileExtension();

        $return = parent::getTemplateVars();
        $return['filesize'] = $filesize;
        $return['url'] = $url;
        $return['filename'] = $filename;
        return $return;
    }
}
