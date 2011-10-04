<?php
class Vpc_FulltextSearch_Search_Component extends Vpc_Abstract_Composite_Component implements Vpc_Paging_ParentInterface
{
    private $_hits;
    private $_time;
    private $_queryString;
    private $_error = false;
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['cssClass'] = 'webStandard';
        $ret['viewCache'] = false;
        $ret['generators']['child']['component']['paging'] = 'Vpc_FulltextSearch_Search_Paging_Component';
        $ret['flags']['processInput'] = true;

        $ret['placeholder']['helpFooter'] = trlVpsStatic('Search results can be extended using wildcards.').'<br />'.
                                            trlVpsStatic('Examples: "Hallo Welt" , ? , * , AND , OR');
        return $ret;
    }


    public function processInput($postData)
    {
        $index = Vps_Util_Fulltext::getInstance();

        if (isset($postData['query'])) {
            $queryString = $postData['query'];
        } else {
            $queryString = '';
        }
        if ($queryString) {
            $userQuery = Zend_Search_Lucene_Search_QueryParser::parse($queryString);
            $query = new Zend_Search_Lucene_Search_Query_Boolean();
            $query->addSubquery($userQuery, true /* required */);

            $subRoot = $this->getData();
            while ($subRoot) {
                if (Vpc_Abstract::getFlag($subRoot->componentClass, 'subroot')) break;
                $subRoot = $subRoot->parent;
            }
            if ($subRoot) {
                $pathTerm  = new Zend_Search_Lucene_Index_Term($subRoot->componentId, 'subroot');
                $pathQuery = new Zend_Search_Lucene_Search_Query_Term($pathTerm);
                $query->addSubquery($pathQuery, true /* required */);
            }
            $time = microtime(true);
            try {
                $this->_hits = $index->find($query);
            } catch (Zend_Search_Lucene_Exception $e) {
                $this->_hits = array();
                $this->_error = $this->getData()->trlVps('Invalid search terms');
            }
            $this->_time = microtime(true)-$time;
        } else {
            $this->_hits = array();
            $this->_time = false;
        }

        $this->_queryString = $queryString;
    }

    public function getPagingCount()
    {
        return count($this->_hits);
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $limit = $this->getData()->getChildComponent('-paging')->getComponent()->getLimit();
        $numStart = $limit['start'];
        $numEnd = min(count($this->_hits), $limit['start'] + $limit['limit']);
        $ret['hits'] = array();
        if (count($this->_hits)) {
            for($i=$numStart; $i < $numEnd; $i++) {
                $h = $this->_hits[$i];
                $c = Vps_Component_Data_Root::getInstance()->getComponentById($h->componentId);
                if ($c) {
                    $ret['hits'][] = array(
                        'data' => $c,
                        'content' => $h->content
                    );
                }
            }
        }

        $ret['queryTime'] = $this->_time;
        $ret['queryString'] = $this->_queryString;
        $ret['queryParts'] = preg_split('/[^a-zA-Z0-9äöüÄÖÜß]/', $this->_queryString);
        $ret['hitCount'] = count($this->_hits);
        $ret['numStart'] = $numStart+1;
        $ret['numEnd'] = $numEnd;
        $ret['error'] = $this->_error;
        return $ret;
    }
}
