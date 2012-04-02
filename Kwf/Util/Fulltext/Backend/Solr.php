<?php
class Kwf_Util_Fulltext_Backend_Solr extends Kwf_Util_Fulltext_Backend_Abstract
{
    private function _getSolrService()
    {
        static $i;
        if (is_null($i)) {
            $solr = Kwf_Config::getValueArray('fulltext.solr');
            $i = new Apache_Solr_Service($solr['host'], $solr['port'], $solr['path']); //add subroot somehow to path??
        }
        return $i;
    }

    public function getSubroots()
    {
        $ret = array();
        foreach (Kwc_Abstract::getComponentClasses() as $c) {
            if (Kwc_Abstract::getFlag($c, 'subroot')) {
                foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByClass($c) as $sr) {
                    $ret[] = $sr->componentId;
                }
            }
        }
        return $ret;
    }

    public function optimize($debugOutput = false)
    {
        $this->_getSolrService()->optimize();
    }

    public function deleteDocument(Kwf_Component_Data $subroot, $componentId)
    {
        //TODO use subroot?
        $this->_getSolrService()->deleteById($componentId);
    }

    public function documentExists(Kwf_Component_Data $page)
    {
        //TODO use subroot
        $this->_getSolrService()->search('componentId:'.$page->componentId);
    }

    public function getAllDocuments(Kwf_Component_Data $subroot)
    {
        //TODO use subroot
        return $this->_getSolrService()->search('*:*');
    }

    public function indexPage(Kwf_Component_Data $page, $debugOutput = false)
    {
        if (Kwc_Abstract::getFlag($page->componentClass, 'skipFulltext')) return; //performance

        //echo "checking for childComponents\n";
        $fulltextComponents = $page->getRecursiveChildComponents(array('flag'=>'hasFulltext', 'inherit' => false));
        if (Kwc_Abstract::getFlag($page->componentClass, 'hasFulltext')) {
            $fulltextComponents[] = $page;
        }
        if ($fulltextComponents) {
            if ($debugOutput) echo " *** indexing $page->componentId $page->url...";
            $contents = $this->getFulltextContentForPage($page, $fulltextComponents);
            unset($fulltextComponents);
            if (!$contents) {
                if ($debugOutput) echo " [no content]";
                return false;
            }
            $doc = new Apache_Solr_Document();
            foreach ($contents as $field=>$text) {
                $doc->addField($field, $text);
            }
            $doc->addField('componentId', $page->componentId);

            $response = $solr->addDocument($doc);
            if ($response->getHttpStatus() != 200) {
                throw new Kwf_Exception("addDocument failed");
            }
            return true;
        }
        return false;
    }

    public function search(Kwf_Component_Data $subroot, $query)
    {
        //TODO use subroot!!
        return $this->_getSolrService()->search($query);
    }
}
