<?php
class Kwf_Util_Fulltext_Backend_Solr extends Kwf_Util_Fulltext_Backend_Abstract
{
    /**
     * @return Apache_Solr_Service[]
     */
    protected function _getRemoteSolrServices($subroot)
    {
        static $i = array();

        $ret = array();

        if (is_string($subroot) && $subroot) {
            $subroot = Kwf_Component_Data_Root::getInstance()->getComponentById($subroot, array('ignoreVisible' => true));
        }

        // checking if there are fully configured remote solr services for this subroot
        $remoteSolrServices = $subroot->getBaseProperty('fulltext.remoteSolrServices');
        if (!is_array($remoteSolrServices) || !count($remoteSolrServices)) {
            return $ret;
        }
        foreach ($remoteSolrServices as $remoteSolrServiceLabel => $remoteSolrService) {
            if (
                !isset($remoteSolrService['host']) || !$remoteSolrService['host'] &&
                !isset($remoteSolrService['port']) || !$remoteSolrService['port'] &&
                !isset($remoteSolrService['path']) || !$remoteSolrService['path']
            ) {
                continue;
            }

            // creating service
            $remoteSolrConfigId = $remoteSolrService['host'] . ':' . $remoteSolrService['port'] . $remoteSolrService['path'];
            if (!isset($i[$remoteSolrConfigId])) {
                $i[$remoteSolrConfigId] =
                    new Kwf_Util_Fulltext_Solr_Service($remoteSolrService['host'], $remoteSolrService['port'], $remoteSolrService['path']);
            }
            $ret[] = $i[$remoteSolrConfigId];
        }

        return $ret;
    }

    /**
     * @return Apache_Solr_Service
     */
    protected function _getSolrService($subroot)
    {
        static $i = array();
        if (is_string($subroot) && $subroot) {
            $subroot = Kwf_Component_Data_Root::getInstance()->getComponentById($subroot, array('ignoreVisible' => true));
        }

        // Create core names from subroot. e.g. root-at => at, root-ro-master => ro_master
        $subrootIds = array();
        while ($subroot) {
            if (Kwc_Abstract::getFlag($subroot->componentClass, 'subroot')) {
                $subrootIds[] = $subroot->id;
            }
            $subroot = $subroot->parent;
        }
        $subrootId = implode('_', array_reverse($subrootIds));

        if (!isset($i[$subrootId])) {
            $solr = Kwf_Config::getValueArray('fulltext.solr');
            $basePath = $solr['basePath'];
            $path = $solr['path'];
            $subrootPart = $subrootId;
            if (!$subrootPart) $subrootPart = 'root';
            $path = str_replace('%basePath%', $basePath, $path);
            $path = str_replace('%subroot%', $subrootPart, $path);
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
                foreach (Kwf_Component_Data_Root::getInstance()->getComponentsBySameClass($c, array('ignoreVisible' => true)) as $sr) {
                    if (isset($sr->parent) && $sr->parent) {//only keep highest level
                        unset($ret[$sr->parent->componentId]);
                    }
                    $ret[$sr->componentId] = $sr->componentId;
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
            foreach ($this->_getRemoteSolrServices($sr) as $remoteSolrService) {
                $remoteSolrService->optimize();
            }
        }
    }

    public function deleteDocument(Kwf_Component_Data $subroot, $componentId)
    {
        $this->_getSolrService($subroot)->deleteById($componentId);
        $this->_getSolrService($subroot)->commit();
        foreach ($this->_getRemoteSolrServices($subroot) as $remoteSolrService) {
            $remoteSolrService->deleteById(sprintf('external_url-%s', $componentId));
            $remoteSolrService->commit();
        }
    }

    public function deleteAll(Kwf_Component_Data $subroot)
    {
        $this->_getSolrService($subroot)->deleteByQuery('*:*');
        $this->_getSolrService($subroot)->commit();
        foreach ($this->_getRemoteSolrServices($subroot) as $remoteSolrService) {
            $remoteSolrService->deleteByQuery(sprintf('componentId:external_url-*'));
            $remoteSolrService->commit();
        }
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

    public function search(Kwf_Component_Data $subroot, $query)
    {
        $ret = array();

        $service = $this->_getSolrService($subroot);
        foreach ($service->search($service->escape($query))->response->docs as $doc) {
            $ret[] = array(
                'componentId' => $doc->componentId,
                'title' => $doc->title,
                'content' => $doc->content,
            );
        }

        return $ret;
    }

    /**
     * Filtering out external documents' ids to avoid them being used in checkForInvalidSubrootAction
     *
     * @param Kwf_Component_Data $subroot
     * @return array|int[]|string[]
     */
    public function getAllDocumentIds(Kwf_Component_Data $subroot)
    {
        return array_filter(
            $this->_getSolrService($subroot)->getAllDocumentIds(),
            function ($documentId) {
                return substr($documentId, 0, 13) != 'external_url-';
            }
        );
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
            $this->_indexPageRemotely($page, $doc);

            $this->_afterIndex($page);

            return true;
        }
        return false;
    }

    protected function _indexPageRemotely(Kwf_Component_Data $page, Apache_Solr_Document $document)
    {
        // calling remote solr services if defined
        foreach ($this->_getRemoteSolrServices($page) as $remoteSolrService) {
            // addig new field to external document
            if (!$document->getField('externalUrl')) {
                $document->addField('externalUrl', $page->getAbsoluteUrl());
            }
            $document->setField('componentId', sprintf('external_url-%s', $page->componentId));
            try {
                $response = $remoteSolrService->addDocument($document);
                if (($responseHttpStatus = $response->getHttpStatus()) != 200) {
                    throw new Kwf_Exception(sprintf(
                        "Unexpected response from remote Solr with status: %d",
                        $responseHttpStatus
                    ));
                }
                $remoteSolrService->commit();
            } catch (Exception $e) {
                throw new Kwf_Exception(sprintf(
                    "Remote Solr service addDocument failed: %s:%d:%s, %s",
                    $remoteSolrService->getHost(),
                    $remoteSolrService->getPort(),
                    $remoteSolrService->getPath(),
                    $e->getMessage()
                ));
            }
        }
    }
    
    public function userSearch(Kwf_Component_Data $subroot, $queryString, $offset, $limit, $params = array())
    {
        $ret = array();
        $params['fl'] = 'componentId,content,externalUrl,title,imageUrl,price';
        $service = $this->_getSolrService($subroot);
        if (isset($params['type'])) {
            $service->setSearchRequestHandler($params['type']);
            unset($params['type']);
        }
        $res = $service->search($service->escape($queryString), $offset, $limit, $params);
        $numHits = $res->response->numFound;
        foreach ($res->response->docs as $doc) {
            if (strpos($doc->componentId, 'external_url-') === 0) {
                $fakeData = new stdClass();
                $fakeData->componentId = $doc->componentId;
                $fakeData->externalUrl = $doc->externalUrl;
                $fakeData->imageUrl = $doc->imageUrl;
                $fakeData->price = $doc->price;
                $ret[] = array(
                    'data' => $fakeData,
                    'externalUrl' => $doc->externalUrl,
                    'fakeComponentId' => $doc->componentId,
                    'content' => $doc->content,
                    'title' => $doc->title,
                    'imageUrl' => $doc->imageUrl,
                    'price' => $doc->price,
                );
            } else {
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
        }

        return array(
            'hits' => $ret,
            'numHits' => $numHits,
            'error' => false
        );
    }

    public function getDocumentContent(Kwf_Component_Data $page)
    {
        $createDocs = $this->_getSolrService($page)->getCreateDocuments();
        $this->_getSolrService($page)->setCreateDocuments(false);

        $res = $this->_getSolrService($page)
            ->search('componentId:'.$page->componentId, 0, 10, array('fl'=>'content'));
        foreach ($res->response->docs as $doc) {
            return $doc->content;
        }
        return null;
    }
}
