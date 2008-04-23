<?php
class Vpc_Basic_DownloadTag_TreeCache extends Vpc_Basic_LinkTag_Abstract_TreeCache
{
    protected $_loadTableFromComponent = true;
    public function afterGenerate()
    {
        parent::afterGenerate();
        $where = array(
            'generated = ?' => Vps_Dao_TreeCache::GENERATE_AFTER,
            'component_class = ?' => $this->_class
        );
        foreach ($this->_cache->fetchAll($where) as $tcRow) {
            $row = $this->_table->findRow($tcRow->db_id);
            $filename = $row->filename != '' ? $row->filename : 'unnamed';
            $url = $row->getFileUrl(null, 'default', $filename, false,
                            Vps_Db_Table_Row_Abstract::FILE_PASSWORD_DOWNLOAD);
            $tcRow->url_pattern = null;
            $tcRow->url_match = null;
            $tcRow->url_match_preview = null;
            $tcRow->url = $url;
            $tcRow->url_preview = $url;
            $tcRow->generated = Vps_Dao_TreeCache::GENERATE_FINISHED;
            $tcRow->save();
        }
    }
}
