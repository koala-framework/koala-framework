<?php
class Kwc_FulltextSearch_MetaModel extends Kwf_Model_Db
{
    protected $_primaryKey = 'page_id';
    protected $_table = 'kwc_fulltext_meta';
    private static $_instance;

    /**
     * @return self
     */
    public static function getInstance()
    {
        if (isset(self::$_instance)) {
            return self::$_instance;
        }
        return Kwf_Model_Abstract::getInstance('Kwc_FulltextSearch_MetaModel');
    }

    public static function clearInstance()
    {
        self::$_instance = null;
    }

    public static function setInstance($instance)
    {
        self::$_instance = $instance;
    }

    public function getDocumentForPage(Kwf_Component_Data $page, array $fulltextComponents = array())
    {
        if (!$fulltextComponents) {
            $fulltextComponents = $page->getRecursiveChildComponents(array('flag'=>'hasFulltext', 'inherit' => false));
            if (Kwc_Abstract::getFlag($page->componentClass, 'hasFulltext')) {
                $fulltextComponents[] = $page;
            }
        }

        $doc = new Zend_Search_Lucene_Document();

        //whole content, for preview in search result
        $doc->addField(Zend_Search_Lucene_Field::UnIndexed('content', '', 'utf-8'));

        //normal content with boost=1 goes here
        $doc->addField(Zend_Search_Lucene_Field::UnStored('normalContent', '', 'utf-8'));

        $t = $page->getTitle();
        if (substr($t, -3) == ' - ') $t = substr($t, 0, -3);
        $field = Zend_Search_Lucene_Field::Text('title', $t, 'utf-8');
        $field->boost = 10;
        $doc->addField($field);

        $boosts = array(
            'contenth1' => 5,
            'contenth2' => 3,
            'contenth3' => 2,
            'contenth4' => 1.5,
            'contenth5' => 1.3,
            'contenth6' => 1.2,
            'contentstrong' => 2,
        );
        foreach ($fulltextComponents as $c) {
            if (!method_exists($c->getComponent(), 'getFulltextContent')) continue;
            $content = $c->getComponent()->getFulltextContent();
            unset($c);
            foreach ($content as $field=>$text) {
                if (!in_array($field, $doc->getFieldNames())) {
                    $field = Zend_Search_Lucene_Field::UnStored($field, $text, 'utf-8');
                    if (isset($boosts[$field])) $field->boost = $boosts[$field];
                    $doc->addField($field);
                } else {
                    $doc->$field .= ' '.$text;
                }
            }
        }

        if (!$doc->getField('content')->value) {
            //echo " [no content]";
            $doc = null;
        }

        return $doc;
    }

    public function indexPage(Kwf_Component_Data $page, $debugOutput = false)
    {
        if (Kwc_Abstract::getFlag($page->componentClass, 'skipFulltext')) return;

        //echo "checking for childComponents\n";
        $fulltextComponents = $page->getRecursiveChildComponents(array('flag'=>'hasFulltext', 'inherit' => false));
        if (Kwc_Abstract::getFlag($page->componentClass, 'hasFulltext')) {
            $fulltextComponents[] = $page;
        }
        if ($fulltextComponents) {
            if ($debugOutput) echo " *** indexing $page->componentId $page->url...";
            $doc = $this->getDocumentForPage($page, $fulltextComponents);
            unset($fulltextComponents);
            if (!$doc && $debugOutput) echo " [no content]";
            if ($debugOutput) echo "\n";

            if ($doc) {

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
                $index = Kwf_Util_Fulltext::getInstance($page);
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
        }
        return false;
    }
}
