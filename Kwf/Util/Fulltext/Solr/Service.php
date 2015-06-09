<?php
class Kwf_Util_Fulltext_Solr_Service extends Apache_Solr_Service
{
    public function getAllDocuments()
    {
        $createDocs = $this->getCreateDocuments();
        $this->setCreateDocuments(false);
        $res = $this->search('*:*', 0, 100000, array('fl'=>'componentId,content'));
        $ret = array();
        foreach ($res->response->docs as $doc) {
            $ret[$doc->componentId] = array(
                'content' => $doc->content
            );
        }
        $this->setCreateDocuments($createDocs);
        return $ret;
    }
    public function getAllDocumentIds()
    {
        $createDocs = $this->getCreateDocuments();
        $this->setCreateDocuments(false);
        $res = $this->search('*:*', 0, 100000, array('fl'=>'componentId'));
        $ret = array();
        foreach ($res->response->docs as $doc) {
            $ret[] = $doc->componentId;
        }
        $this->setCreateDocuments($createDocs);
        return $ret;
    }

    public function setSearchRequestHandler($path)
    {
        $this->_searchUrl = $this->_constructUrl($path);
    }
}
