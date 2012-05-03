<?php
class Kwc_FulltextSearch_Search_Component extends Kwc_Abstract_Composite_Component implements Kwc_Paging_ParentInterface
{
    private $_hits;
    private $_time;
    private $_queryString;
    private $_error = false;
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('Fulltext Search');
        $ret['cssClass'] = 'webStandard';
        $ret['viewCache'] = false;
        $ret['generators']['child']['component']['paging'] = 'Kwc_FulltextSearch_Search_Paging_Component';
        $ret['flags']['processInput'] = true;

        $ret['placeholder']['helpFooter'] = trlKwfStatic('Search results can be extended using wildcards.').'<br />'.
                                            trlKwfStatic('Examples: "Hallo Welt" , ? , * , AND , OR');
        return $ret;
    }


    public function processInput($postData)
    {
        $subRoot = $this->getData();
        while ($subRoot) {
            if (Kwc_Abstract::getFlag($subRoot->componentClass, 'subroot')) break;
            $subRoot = $subRoot->parent;
        }
        if (!$subRoot) $subRoot = Kwf_Component_Data_Root::getInstance();
        $index = Kwf_Util_Fulltext::getInstance($subRoot);

        if (isset($postData['query']) && is_string($postData['query'])) {
            $queryString = $postData['query'];
        } else {
            $queryString = '';
        }
        $userQuery = false;
        if ($queryString) {
            try {
                $userQuery = Zend_Search_Lucene_Search_QueryParser::parse($queryString);
            } catch (ErrorException $e) {
                //ignore iconv errors that happen with invalid input
            }
        }

        if ($userQuery) {
            $query = new Zend_Search_Lucene_Search_Query_Boolean();
            $query->addSubquery($userQuery, true /* required */);
            $this->_beforeFind($query);
            $time = microtime(true);
            try {
                $this->_hits = $index->find($query);
            } catch (Zend_Search_Lucene_Exception $e) {
                $this->_hits = array();
                $this->_error = $this->getData()->trlKwf('Invalid search terms');
            }
            $this->_time = microtime(true)-$time;
        } else {
            $this->_hits = array();
            $this->_time = false;
        }

        $this->_queryString = $queryString;
    }

    protected function _beforeFind($query)
    {
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
                $c = Kwf_Component_Data_Root::getInstance()->getComponentById($h->componentId);
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
