<?php
class Vpc_Basic_Download_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'tablename' => 'Vpc_Basic_Download_Model',
            'componentName' => 'Standard.Download',
            'extensions' => array('pdf', 'doc', 'mp3', 'xls', 'ppt'),
            'showIcon' => true,
            'showFilesize' => true,
            'default'   => array(
                'filename' => 'unnamed'
            )
        ));
    }
    
    public function getTemplateVars()
    {
        $row = $this->_row;
        $fileTable = $this->getTable('Vps_Dao_File');
        $url = $fileTable->generateUrl(
            $row->vps_upload_id, 
            $this->getId(), 
            $row->filename != '' ? $row->filename : 'unnamed', 
            Vps_Dao_File::DOWNLOAD
        );
        $filesize = $fileTable->getFilesize($row->vps_upload_id);

        $return = parent::getTemplateVars();
        $return['url'] = '';
        $return['icon'] = '';
        $return['text'] = '';
        $return['filesize'] = '';
        $return['url'] = $url;
        $return['text'] = $row->filename;
        $return['info'] = $row->infotext;
        if ($this->_getSetting('showIcon')) {
            $return['icon'] = '';
        }
        if ($this->_getSetting('showFilesize')) {
            $return['filesize'] = $filesize;
        }
        
        return $return;
    }
}