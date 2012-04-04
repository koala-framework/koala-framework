<?php
class Kwf_Util_Fulltext_Backend_ZendSearch extends Kwf_Util_Fulltext_Backend_Abstract
{
    public function getSubroots()
    {
        return array_keys(Kwf_Util_Fulltext_Lucene::getInstances());
    }

    public function optimize($debugOutput = false)
    {
        foreach (Kwf_Util_Fulltext_Lucene::getInstances() as $subroot=>$i) {
            if ($debugOutput) echo "$subroot\n";
            $i->optimize();
        };
    }

    public function getAllDocumentIds(Kwf_Component_Data $subroot)
    {
        $index = Kwf_Util_Fulltext_Lucene::getInstance($subroot);
        $query = Zend_Search_Lucene_Search_QueryParser::parse('dummy:dummy');
        $ret = array();
        foreach ($index->find($query) as $hit) {
            $ret[] = $hit->componentId();
        }
        return $ret;
    }

    public function getAllDocuments(Kwf_Component_Data $subroot)
    {
        $index = Kwf_Util_Fulltext_Lucene::getInstance($subroot);
        $query = Zend_Search_Lucene_Search_QueryParser::parse('dummy:dummy');
        $ret = array();
        foreach ($index->find($query) as $hit) {
            $doc = $hit->getDocument();
            $data = array();
            foreach ($doc->getFieldNames() as $n) {
                $data[$n] = $doc->getFieldValue($n);
            }
            $ret[$doc->componentId] = $data;
        }
        return $ret;
    }

    public function deleteDocument(Kwf_Component_Data $subroot, $componentId)
    {
        $index = Kwf_Util_Fulltext_Lucene::getInstance($subroot);
        $index->delete($componentId);
        $index->commit();
    }

    public function search(Kwf_Component_Data $subroot, $query)
    {
        $index = Kwf_Util_Fulltext_Lucene::getInstance($subroot);

        $query = Zend_Search_Lucene_Search_QueryParser::parse($queryStr);

        $userQuery = Zend_Search_Lucene_Search_QueryParser::parse($queryStr);
        $query = new Zend_Search_Lucene_Search_Query_Boolean();
        $query->addSubquery($userQuery, true /* required */);


        $hits = $index->find($query);
        $ret = array();
        foreach ($hits as $hit) {
            $ret[] = array(
                'score' => $hit->score,
                'componentId' => $hit->componentId,
            );
        }
        return $ret;
    }


    public function documentExists(Kwf_Component_Data $page)
    {
        $index = Kwf_Util_Fulltext_Lucene::getInstance($page);
        $term = new Zend_Search_Lucene_Index_Term($page->componentId, 'componentId');
        $found = false;
        foreach ($index->find(new Zend_Search_Lucene_Search_Query_Term($term)) as $doc) {
            $found = true;
            break;
        }
        return $found;
    }

    public function indexPage(Kwf_Component_Data $page, $debugOutput = false)
    {
        $boosts = array(
            'contenth1' => 5,
            'contenth2' => 3,
            'contenth3' => 2,
            'contenth4' => 1.5,
            'contenth5' => 1.3,
            'contenth6' => 1.2,
            'contentstrong' => 2,
        );

        if (Kwc_Abstract::getFlag($page->componentClass, 'skipFulltext')) return; //performance

        //echo "checking for childComponents\n";
        $fulltextComponents = $page->getRecursiveChildComponents(array('flag'=>'hasFulltext', 'inherit' => false, 'page'=>false));
        if (Kwc_Abstract::getFlag($page->componentClass, 'hasFulltext')) {
            $fulltextComponents[] = $page;
        }
        if ($fulltextComponents) {
            if ($debugOutput) echo " *** indexing $page->componentId $page->url...";
            $contents = $this->getFulltextContentForPage($page, $fulltextComponents);
            unset($fulltextComponents);
            if (!$contents || !isset($contents['content']) || !$contents['content']) {
                if ($debugOutput) echo " [no content]";
                return false;
            }
            $doc = new Zend_Search_Lucene_Document();

            //whole content, for preview in search result
            $doc->addField(Zend_Search_Lucene_Field::UnIndexed('content', $contents['content'], 'utf-8'));
            unset($contents['content']);

            $t = $page->getTitle();
            if (substr($t, -3) == ' - ') $t = substr($t, 0, -3);
            $field = Zend_Search_Lucene_Field::Text('title', $t, 'utf-8');
            $field->boost = 10;
            $doc->addField($field);

            foreach ($contents as $fieldName=>$text) {
                $field = Zend_Search_Lucene_Field::UnStored($fieldName, $text, 'utf-8');
                if (isset($boosts[$fieldName])) $field->boost = $boosts[$fieldName];
                $doc->addField($field);
            }
            if ($debugOutput) echo "\n";

            //das wird verwendet um alle dokumente im index zu finden
            //ned wirklisch a schöne lösung :(
            $field = Zend_Search_Lucene_Field::UnStored('dummy', 'dummy', 'utf-8');
            $field->boost = 0.0001;
            $doc->addField($field);

            $field = Zend_Search_Lucene_Field::Keyword('componentId', $page->componentId, 'utf-8');
            $field->boost = 0.0001;
            $doc->addField($field);

            //foreach ($doc->getFieldNames() as $fieldName) {
                //echo "$fieldName: ".substr($doc->$fieldName, 0, 80)."\n";
                //echo "$fieldName: ".$doc->$fieldName."\n";
            //}

            $term = new Zend_Search_Lucene_Index_Term($page->componentId, 'componentId');
            $index = Kwf_Util_Fulltext_Lucene::getInstance($page);
            $hits = $index->termDocs($term);
            foreach ($hits as $id) {
                //echo "deleting $hit->componentId\n";
                $index->delete($id);
            }

            $index->addDocument($doc);

            $m = Kwc_FulltextSearch_MetaModel::getInstance();
            $row = $m->getRow($page->componentId);
            if (!$row) {
                $row = $m->createRow();
                $row->page_id = $page->componentId;
            }
            $row->indexed_date = date('Y-m-d H:i:s');
            $row->save();
            unset($row);

            return true;
        }
        return false;
    }
}
