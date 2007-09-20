<?php
class Vpc_Basic_Download_Index extends Vpc_Abstract
{
    protected $_tablename = 'Vpc_Basic_Download_IndexModel';
    const NAME = 'Standard.Download';

    protected $_settings = array(
        'extensions' => array('pdf', 'doc', 'mp3', 'xls', 'ppt'),
        'name' => 'Filename',
        'info' => '',
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

        $row = $this->_getTable()->find($this->getDbId(), $this->getComponentKey())->current();
        if ($row) {
            $filename = $row->name != '' ? $row->name : 'unnamed';
            $return['url'] = $this->_getTable('Vps_Dao_File')->generateUrl($row->vps_upload_id, $this->getId(), $filename, Vps_Dao_File::DOWNLOAD);
            if ($this->getSetting('showIcon')) {
                $return['icon'] = '';
            }
            if ($this->getSetting('showFilesize')) {
                $return['filesize'] = $this->_getTable('Vps_Dao_File')->getFilesize($row->vps_upload_id);
            }
            $return['text'] = $this->getSetting('name');
            $return['info'] = $this->getSetting('info');
        }

        return $return;
    }
}