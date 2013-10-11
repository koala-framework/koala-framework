<?php
class Kwf_Util_Fulltext_Backend_Solr extends Kwf_Util_Fulltext_Backend_Abstract
{
    /**
     * @return Apache_Solr_Service
     */
    private function _getSolrService($subroot)
    {
        static $i = array();
        if (is_string($subroot) && $subroot) {
            $subrootId = Kwf_Component_Data_Root::getInstance()->getComponentById($subroot)->id;
        } else {
            $subrootId = ''; //valid; no subroots exist
            while ($subroot) {
                if (Kwc_Abstract::getFlag($subroot->componentClass, 'subroot')) {
                    $subrootId = $subroot->id;
                }
                $subroot = $subroot->parent;
            }
        }
        if (!isset($i[$subrootId])) {
            $solr = Kwf_Config::getValueArray('fulltext.solr');
            $path = $solr['path'];
            $path = str_replace('%subroot%', $subrootId, $path);
            $path = str_replace('%appid%', Kwf_Config::getValue('application.id'), $path);
            $i[$subrootId] = new Kwf_Util_Fulltext_Solr_Service($solr['host'], $solr['port'], $path);
        }
        return $i[$subrootId];
    }

    public function getSubroots()
    {
        $ret = Kwf_Config::getValueArray('fulltext.solr.subroots');
        if ($ret) return $ret;
        $ret = array();
        foreach (Kwc_Abstract::getComponentClasses() as $c) {
            if (Kwc_Abstract::getFlag($c, 'subroot')) {
                foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByClass($c) as $sr) {
                    $ret[] = $sr->componentId;
                }
            }
        }
        if (!$ret) $ret = array(''); //no subroots exist
        return $ret;
    }

    public function optimize($debugOutput = false)
    {
        foreach ($this->getSubroots() as $sr) {
            $this->_getSolrService($sr)->optimize();
        }
    }

    public function deleteDocument(Kwf_Component_Data $subroot, $componentId)
    {
        $this->_getSolrService($subroot)->deleteById($componentId);
        $this->_getSolrService($subroot)->commit();
    }

    public function deleteAll(Kwf_Component_Data $subroot)
    {
        $this->_getSolrService($subroot)->deleteByQuery('*:*');
        $this->_getSolrService($subroot)->commit();
    }


    public function documentExists(Kwf_Component_Data $page)
    {
        $createDocs = $this->_getSolrService($page)->getCreateDocuments();
        $this->_getSolrService($page)->setCreateDocuments(false);

        $numFound = $this->_getSolrService($page)
            ->search('componentId:'.$page->componentId, 0, 10, array('fl'=>'componentId'))
            ->response->numFound;

        $this->_getSolrService($page)->setCreateDocuments($createDocs);

        return $numFound > 0;
    }

    public function getAllDocumentIds(Kwf_Component_Data $subroot)
    {
        return $this->_getSolrService($subroot)->getAllDocumentIds();
    }

    public function getAllDocuments(Kwf_Component_Data $subroot)
    {
        return $this->_getSolrService($subroot)->getAllDocuments();
    }

    public function indexPage(Kwf_Component_Data $page, $debugOutput = false)
    {
        if (Kwc_Abstract::getFlag($page->componentClass, 'skipFulltext')) return; //performance

        //echo "checking for childComponents\n";
        $fulltextComponents = $this->getFulltextComponents($page);
        if ($fulltextComponents) {
            if ($debugOutput) echo " *** indexing $page->componentId $page->url...";
            $contents = $this->getFulltextContentForPage($page, $fulltextComponents);
            unset($fulltextComponents);
            if (!$contents) {
                if ($debugOutput) echo " [no content]\n";
                return false;
            }
            if ($debugOutput) echo " [".implode(' ', array_keys($contents))."]\n";
            require_once Kwf_Config::getValue('externLibraryPath.solr').'/Apache/Solr/Document.php';
            $doc = new Apache_Solr_Document();
            foreach ($contents as $field=>$text) {
                if ($text instanceof Kwf_DateTime) {
                    $text = gmdate('Y-m-d\TH:i:s\Z', $text->getTimestamp());
                }
                $doc->addField($field, $text);
            }
            $doc->addField('componentId', $page->componentId);

            $response = $this->_getSolrService($page)->addDocument($doc);
            if ($response->getHttpStatus() != 200) {
                throw new Kwf_Exception("addDocument failed");
            }
            $this->_getSolrService($page)->commit();

            $this->_afterIndex($page);

            return true;
        }
        return false;
    }

    public function search(Kwf_Component_Data $subroot, $query)
    {
        $ret = array();
        foreach ($this->_getSolrService($subroot)->search($query)->response->docs as $doc) {
            $ret[] = array(
                'componentId' => $doc->componentId,
                'title' => $doc->title,
                'content' => $doc->content,
            );
        }
        return $ret;
    }

    public function userSearch(Kwf_Component_Data $subroot, $queryString, $offset, $limit, $params = array())
    {
        $ret = array();
        $params['fl'] = 'componentId,content';
        $service = $this->_getSolrService($subroot);
        if (isset($params['type'])) {
            $service->setSearchRequestHandler($params['type']);
            unset($params['type']);
        }
        $res = $service->search($queryString, $offset, $limit, $params);
        $numHits = $res->response->numFound;
        foreach ($res->response->docs as $doc) {
            $data = Kwf_Component_Data_Root::getInstance()->getComponentById($doc->componentId);
            if (!$data) {
                //if page was removed/hidden and index is not yet updated delete the document now
                $this->deleteDocument($subroot, $doc->componentId);
                $numHits--;
            } else {
                $ret[] = array(
                    'data' => $data,
                    'content' => $doc->content,
                );
            }
        }
        //TODO: error handling
        return array(
            'hits' => $ret,
            'numHits' => $numHits,
            'error' => false
        );
    }
}
