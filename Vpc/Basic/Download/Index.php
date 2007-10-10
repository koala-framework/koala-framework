<?php
class Vpc_Basic_Download_Index extends Vpc_Abstract
{
    protected $_tablename = 'Vpc_Basic_Download_IndexModel';
    const NAME = 'Standard.Download';

    protected $_settings = array(
        'extensions' => array('pdf', 'doc', 'mp3', 'xls', 'ppt'),
        'filename' => 'unnamed',
        'showIcon' => true,
        'showFilesize' => true
    );

    public function getTemplateVars()
    {
        $return['url'] = '';
        $return['icon'] = '';
        $return['text'] = '';
        $return['filesize'] = '';
        $return['template'] = 'Basic/Download.html';

        $row = $this->getTable()->find($this->getDbId(), $this->getComponentKey())->current();
        if ($row) {
            $filename = $row->filename != '' ? $row->filename : 'unnamed';
            $return['url'] = $this->getTable('Vps_Dao_File')->generateUrl($row->vps_upload_id, $this->getId(), $filename, Vps_Dao_File::DOWNLOAD);
            if ($this->getSetting('showIcon')) {
                $return['icon'] = '';
            }
            if ($this->getSetting('showFilesize')) {
                $return['filesize'] = $this->getTable('Vps_Dao_File')->getFilesize($row->vps_upload_id);
            }
            $return['text'] = $this->getSetting('filename');
            $return['info'] = $this->getSetting('infotext');
        }

        return $return;
    }
}