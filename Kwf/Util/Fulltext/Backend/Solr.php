<?php
class Kwf_Util_Fulltext_Backend_Solr extends Kwf_Util_Fulltext_Backend_Abstract
{
    private function _getSolrService()
    {
        static $i;
        if (is_null($i)) {
            $i = new Apache_Solr_Service("vivid", 8983, "solr/vwpkwat");
        }
        return $i;
    }

    public function optimize($debugOutput = false)
    {
        $this->_getSolrService()->optimize();
    }
}
