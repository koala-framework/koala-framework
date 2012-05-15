<?php
class Kwc_FulltextSearch_Search_Component extends Kwc_Abstract_Composite_Component implements Kwc_Paging_ParentInterface
{
    private $_hits;
    private $_numHits;
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
        $ret['searchParams'] = null; //backend dependent params
        //example: $ret['searchParams'] = array('type'=>'news');

        $ret['flags']['usesFulltext'] = true;
        $ret['updateTags'][] = 'fulltext';
        return $ret;
    }


    public function processInput($postData)
    {
        if (isset($postData['query']) && is_string($postData['query'])) {
            $queryString = $postData['query'];
        } else {
            $queryString = '';
        }
        $this->_queryString = $queryString;

        $time = microtime(true);

        $this->_hits = array();
        $this->_numHits = 0;
        $this->_error = '';
        if ($queryString) {
            $limit = $this->getData()->getChildComponent('-paging')->getComponent()->getLimit();
            $res = Kwf_Util_Fulltext_Backend_Abstract::getInstance()
                ->userSearch($this->getData(), $queryString, $limit['start'], $limit['limit'], $this->_getSetting('searchParams'));
            $this->_hits = $res['hits'];
            $this->_numHits = $res['numHits'];
            $this->_error = $res['error'];
        }
        $this->_time = microtime(true)-$time;
    }

    /**
     * @deprecated doesn't work anymore
     */
    protected final function _beforeFind($query)
    {
    }

    public function getPagingCount()
    {
        return $this->_numHits;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['hits'] = $this->_hits;
        $ret['queryTime'] = $this->_time;
        $ret['queryString'] = $this->_queryString;
        $ret['queryParts'] = preg_split('/[^a-zA-Z0-9äöüÄÖÜß]/', $this->_queryString);
        $ret['hitCount'] = $this->_numHits;

        $limit = $this->getData()->getChildComponent('-paging')->getComponent()->getLimit();
        $numStart = $limit['start'];
        $numEnd = min(count($this->_hits), $limit['start'] + $limit['limit']);
        $ret['numStart'] = $numStart+1;
        $ret['numEnd'] = $numEnd;

        $ret['error'] = $this->_error;
        return $ret;
    }
}
