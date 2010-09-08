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
        $userQuery = Zend_Search_Lucene_Search_QueryParser::parse($queryString);
        $query = new Zend_Search_Lucene_Search_Query_Boolean();
        $query->addSubquery($userQuery, true /* required */);

        $subRoot = $this->getData();
        while ($subRoot) {
            if (Vpc_Abstract::getFlag($subRoot->componentClass, 'subroot')) break;
            $subRoot = $subRoot->parent;
        }
        if ($subRoot) {
            $pathTerm  = new Zend_Search_Lucene_Index_Term($subRoot->dbId, 'subroot');
            $pathQuery = new Zend_Search_Lucene_Search_Query_Term($pathTerm);
            $query->addSubquery($pathQuery, true /* required */);
        }

        $time = microtime(true);
        $allHits = $index->find($query);
        $ret['hitCount'] = count($allHits);
        $hits = array();
        $numStart = min(($page-1)*$perPage + 1, count($allHits));
        $numEnd = min(($page)*$perPage, count($allHits));
        if (count($allHits)) {
            for($i=$numStart; $i <= $numEnd; $i++) {
                $h = $allHits[$i-1];
                $c = Vps_Component_Data_Root::getInstance()->getComponentByDbId($h->componentId);
                if ($c) {
                    $hits[] = array(
                        'data' => $c
                    );
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
