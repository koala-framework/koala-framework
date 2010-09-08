<?php
class Vpc_FulltextSearch_Search_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $index = Vps_Util_Fulltext::getInstance();

        $perPage = 20;

        $page = 1;

        if (isset($_GET['query'])) {
            $queryString = $_GET['query'];
        } else {
            $queryString = '';
        }
        $query = Zend_Search_Lucene_Search_QueryParser::parse($queryString);
        $time = microtime(true);
        $allHits = $index->find($query);
        $hitCount = count($allHits);
        $ret['hitCount'] = $hitCount;
        $hits = array();
        $numStart = min(($page-1)*$perPage + 1, count($allHits));
        $numEnd = min(($page)*$perPage, count($allHits));
        if (count($allHits)) {
            for($i=$numStart; $i <= $numEnd; $i++) {
                $h = $allHits[$i-1];
                $h->data = Vps_Component_Data_Root::getInstance()->getComponentByDbId($h->componentId, array('subroot'=>$this->getData()));
                if ($h->data) {
                    $hits[] = $h;
                }
            }
        }
        $ret['hits'] = $hits;

        $time = microtime(true)-$time;
        $ret['queryTime'] = $time;
        $ret['query'] = $query;

        $ret['numStart'] = $numStart;
        $ret['numEnd'] = $numEnd;

        return $ret;
    }
}
